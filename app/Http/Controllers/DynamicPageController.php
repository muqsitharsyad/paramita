<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DynamicPageController extends Controller
{
    private const RESPONSE_CACHE_SECONDS = 30;

    private const PASSTHROUGH_QUERY_KEYS = ['limit', 'offset', 'search'];

    public function show(string $slug)
    {
        $page = Page::with([
                'roles:id,name',
                'jsonTemplates.endpoints.vendorApi.vendor',
            ])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $user = auth()->user()->loadMissing('roles:id,name');
        $userRoleNames = $user->roles->pluck('name');
        $pageRoleNames = $page->roles->pluck('name');

        if ($pageRoleNames->intersect($userRoleNames)->isEmpty()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $cachedSections = [];
        $pendingRequests = [];

        $queryParameters = request()->only(self::PASSTHROUGH_QUERY_KEYS);

        foreach ($page->jsonTemplates as $template) {
            $endpoint = $template->endpoints
                ->where('status', 'active')
                ->first(fn ($item) => $item->vendorApi);

            if (! $endpoint || ! $endpoint->vendorApi) {
                continue;
            }

            $fullUrl = $this->appendQueryParameters($endpoint->full_url, $queryParameters);
            $cacheKey = $this->getResponseCacheKey($endpoint->id, $queryParameters);

            if ($cached = Cache::get($cacheKey)) {
                $cachedSections[$template->id] = $cached;
                continue;
            }

            $pendingRequests[$template->id] = [
                'template_name' => $template->name,
                'url' => $fullUrl,
                'cache_key' => $cacheKey,
            ];
        }

        if ($pendingRequests !== []) {
            try {
                $responses = Http::pool(fn (Pool $pool) => collect($pendingRequests)
                    ->mapWithKeys(fn (array $request, int $templateId) => [
                        $templateId => $pool
                            ->as((string) $templateId)
                            ->withHeaders(['Accept' => 'application/json'])
                            ->withOptions(['verify' => false])
                            ->timeout(15)
                            ->get($request['url']),
                    ])
                    ->all());
            } catch (\Throwable $e) {
                $responses = [];

                Log::warning('DynamicPage pooled fetch failed', [
                    'page' => $page->slug,
                    'error' => $e->getMessage(),
                ]);
            }

            foreach ($pendingRequests as $templateId => $request) {
                $cachedSections[$templateId] = $this->normalizeResponse(
                    $page->slug,
                    $request['template_name'],
                    $request['url'],
                    $responses[$templateId] ?? null
                );

                Cache::put(
                    $request['cache_key'],
                    $cachedSections[$templateId],
                    now()->addSeconds(self::RESPONSE_CACHE_SECONDS)
                );
            }
        }

        $templateSections = [];

        foreach ($page->jsonTemplates as $template) {
            $section = [
                'template' => $template,
                'data' => null,
                'pagination' => null,
                'error' => null,
                'fetched_at' => null,
            ];

            if (isset($cachedSections[$template->id])) {
                $payload = $cachedSections[$template->id];
                $section['data'] = $payload['data'];
                $section['pagination'] = $payload['pagination'];
                $section['error'] = $payload['error'];
                $section['full_response'] = $payload['full_response'];
                $section['fetched_at'] = isset($payload['fetched_at'])
                    ? Carbon::parse($payload['fetched_at'])
                    : null;

                $templateSections[] = $section;
                continue;
            }

            $section['no_endpoint'] = true;
            $templateSections[] = $section;
        }

        return view('dynamic-page', compact('page', 'templateSections'));
    }

    private function getResponseCacheKey(int $endpointId, array $queryParameters): string
    {
        ksort($queryParameters);

        return "dynamic-page:endpoint-response:{$endpointId}:" . md5(http_build_query($queryParameters));
    }

    private function appendQueryParameters(string $url, array $queryParameters): string
    {
        $queryParameters = array_filter(
            $queryParameters,
            fn ($value) => $value !== null && $value !== ''
        );

        if ($queryParameters === []) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . http_build_query($queryParameters);
    }

    private function normalizeResponse(string $pageSlug, string $templateName, string $url, $response): array
    {
        $payload = [
            'data' => null,
            'pagination' => null,
            'error' => null,
            'fetched_at' => now()->toIso8601String(),
            'full_response' => null,
        ];

        if (! $response) {
            $payload['error'] = 'Tidak ada respons dari endpoint.';

            return $payload;
        }

        try {
            if ($response->successful()) {
                $json = $response->json();
                $payload['data'] = $json['data'] ?? $json;
                $payload['pagination'] = $json['pagination'] ?? null;
                $payload['full_response'] = $json;

                return $payload;
            }

            $payload['error'] = 'HTTP ' . $response->status() . ': ' . $response->reason();
        } catch (\Throwable $e) {
            $payload['error'] = 'Gagal mengambil data: ' . $e->getMessage();

            Log::warning('DynamicPage fetch failed', [
                'page' => $pageSlug,
                'template' => $templateName,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }

        return $payload;
    }
}
