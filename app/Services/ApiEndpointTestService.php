<?php

namespace App\Services;

use App\Helpers\VendorApiAuthHelper;
use App\Models\ApiEndpoint;
use App\Models\JsonTemplate;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiEndpointTestService
{
    public function test(ApiEndpoint $endpoint, array $testData = []): array
    {
        $endpoint->loadMissing(['vendorApi', 'jsonTemplate']);

        $fullUrl = $this->buildFullUrl($endpoint, $testData['test_parameters'] ?? []);

        try {
            $client = $this->buildClient($endpoint, $testData['test_headers'] ?? []);
            $requestBody = $this->decodeRequestBody($endpoint, $testData['test_body'] ?? null);

            $response = match ($endpoint->method) {
                'GET' => $client->get($fullUrl),
                'POST' => $client->post($fullUrl, $requestBody),
                'PUT' => $client->put($fullUrl, $requestBody),
                'PATCH' => $client->patch($fullUrl, $requestBody),
                'DELETE' => $client->delete($fullUrl),
                default => throw new \RuntimeException('Unsupported HTTP method: ' . $endpoint->method),
            };

            $responseData = $response->json();
            $templateValidation = $endpoint->jsonTemplate
                ? $this->validateAgainstTemplate($responseData, $endpoint->jsonTemplate)
                : null;

            [$healthStatus, $healthMessage] = $this->resolveHealthStatus(
                $response->successful(),
                $response->status(),
                $response->json('message'),
                $templateValidation
            );

            $this->updateEndpointHealth($endpoint, $healthStatus, $healthMessage);

            Log::info('API endpoint tested', [
                'endpoint_id' => $endpoint->id,
                'endpoint' => $endpoint->name,
                'method' => $endpoint->method,
                'url' => $fullUrl,
                'status_code' => $response->status(),
                'health_status' => $healthStatus,
                'template_validation' => $templateValidation,
            ]);

            return [
                'ok' => $response->successful(),
                'method' => $endpoint->method,
                'full_url' => $fullUrl,
                'status_code' => $response->status(),
                'health_status' => $healthStatus,
                'health_message' => $healthMessage,
                'template_validation' => $templateValidation,
            ];
        } catch (Throwable $e) {
            $message = 'Test failed: ' . $e->getMessage();

            $this->updateEndpointHealth($endpoint, 'unhealthy', $message);

            Log::error('API endpoint test failed', [
                'endpoint_id' => $endpoint->id,
                'endpoint' => $endpoint->name,
                'method' => $endpoint->method,
                'url' => $fullUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'method' => $endpoint->method,
                'full_url' => $fullUrl,
                'status_code' => null,
                'health_status' => 'unhealthy',
                'health_message' => $message,
                'template_validation' => null,
            ];
        }
    }

    private function buildClient(ApiEndpoint $endpoint, array $headers): PendingRequest
    {
        $baseClient = Http::withHeaders(['Accept' => 'application/json'])
            ->withOptions(['verify' => false])
            ->timeout($endpoint->vendorApi->timeout ?? 30);

        if ($endpoint->requires_auth) {
            $authenticatedClient = VendorApiAuthHelper::authenticate(
                $endpoint->vendorApi,
                rtrim($endpoint->vendorApi->base_url, '/')
            );

            if (! $authenticatedClient) {
                throw new \RuntimeException('Authentication failed for vendor API.');
            }

            $baseClient = $authenticatedClient
                ->withOptions(['verify' => false])
                ->timeout($endpoint->vendorApi->timeout ?? 30);
        }

        if ($headers === []) {
            return $baseClient;
        }

        return $baseClient->withHeaders(
            collect($headers)
                ->filter(fn ($value, $key) => filled($key) && filled($value))
                ->mapWithKeys(fn ($value, $key) => [(string) $key => (string) $value])
                ->all()
        );
    }

    private function buildFullUrl(ApiEndpoint $endpoint, array $parameters): string
    {
        $url = $endpoint->full_url;

        foreach ($parameters as $key => $value) {
            $url = str_replace('{' . $key . '}', rawurlencode((string) $value), $url);
        }

        return $url;
    }

    private function decodeRequestBody(ApiEndpoint $endpoint, ?string $body): array
    {
        if (! in_array($endpoint->method, ['POST', 'PUT', 'PATCH'], true)) {
            return [];
        }

        $decoded = json_decode($body ?: '{}', true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON in request body: ' . json_last_error_msg());
        }

        return $decoded;
    }

    private function resolveHealthStatus(
        bool $requestSuccessful,
        int $statusCode,
        ?string $responseMessage,
        ?array $templateValidation
    ): array {
        if (! $requestSuccessful) {
            return [
                'unhealthy',
                'HTTP ' . $statusCode . ': ' . ($responseMessage ?: 'Request failed'),
            ];
        }

        if ($templateValidation && ! ($templateValidation['valid'] ?? false)) {
            return [
                'warning',
                'Request successful but response does not match the selected template.',
            ];
        }

        return [
            'healthy',
            $templateValidation
                ? 'Endpoint is working correctly and response matches the selected template.'
                : 'Endpoint is working correctly.',
        ];
    }

    private function updateEndpointHealth(ApiEndpoint $endpoint, string $status, string $message): void
    {
        $endpoint->update([
            'health_status' => $status,
            'last_tested_at' => now(),
            'health_message' => $message,
        ]);
    }

    private function validateAgainstTemplate($responseData, JsonTemplate $template): array
    {
        if (! is_array($responseData)) {
            return [
                'valid' => false,
                'errors' => ['Response is not valid JSON or is empty.'],
            ];
        }

        $templateData = is_array($template->template_data)
            ? $template->template_data
            : json_decode((string) $template->template_data, true);

        if (! is_array($templateData)) {
            return [
                'valid' => false,
                'errors' => ['Template JSON format is invalid.'],
            ];
        }

        $errors = [];
        $this->validateStructure($responseData, $templateData, '', $errors);

        return [
            'valid' => $errors === [],
            'errors' => $errors,
        ];
    }

    private function validateStructure($response, $template, string $path, array &$errors): void
    {
        foreach ($template as $key => $value) {
            $currentPath = $path !== '' ? "{$path}.{$key}" : (string) $key;

            if (! is_array($response) || ! array_key_exists($key, $response)) {
                $errors[] = "Missing key: {$currentPath}";
                continue;
            }

            $responseValue = $response[$key];

            if (is_array($value)) {
                if (! is_array($responseValue)) {
                    $errors[] = "Type mismatch at {$currentPath}: expected array, got " . gettype($responseValue);
                    continue;
                }

                if ($value === []) {
                    continue;
                }

                if ($this->isAssociativeArray($value)) {
                    $this->validateStructure($responseValue, $value, $currentPath, $errors);
                    continue;
                }

                $templateFirstElement = reset($value);

                foreach ($responseValue as $index => $responseItem) {
                    $itemPath = "{$currentPath}[{$index}]";

                    if (is_array($templateFirstElement)) {
                        if (! is_array($responseItem)) {
                            $errors[] = "Type mismatch at {$itemPath}: expected object, got " . gettype($responseItem);
                            continue;
                        }

                        $this->validateStructure($responseItem, $templateFirstElement, $itemPath, $errors);
                        continue;
                    }

                    if (is_string($templateFirstElement) && preg_match('/^\{\{.*\}\}$/', $templateFirstElement)) {
                        continue;
                    }

                    if (gettype($responseItem) !== gettype($templateFirstElement)) {
                        $errors[] = "Type mismatch at {$itemPath}: expected " . gettype($templateFirstElement)
                            . ', got ' . gettype($responseItem);
                    }
                }

                continue;
            }

            if (is_string($value) && preg_match('/^\{\{.*\}\}$/', $value)) {
                continue;
            }

            if (is_string($value) && ! is_string($responseValue)) {
                $errors[] = "Type mismatch at {$currentPath}: expected string, got " . gettype($responseValue);
                continue;
            }

            if (is_int($value) && ! is_int($responseValue)) {
                $errors[] = "Type mismatch at {$currentPath}: expected integer, got " . gettype($responseValue);
                continue;
            }

            if (is_float($value) && ! is_float($responseValue) && ! is_int($responseValue)) {
                $errors[] = "Type mismatch at {$currentPath}: expected number, got " . gettype($responseValue);
                continue;
            }

            if (is_bool($value) && ! is_bool($responseValue)) {
                $errors[] = "Type mismatch at {$currentPath}: expected boolean, got " . gettype($responseValue);
                continue;
            }

            if ($value === null && $responseValue !== null) {
                $errors[] = "Type mismatch at {$currentPath}: expected null, got " . gettype($responseValue);
            }
        }
    }

    private function isAssociativeArray(array $array): bool
    {
        if ($array === []) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}
