@extends('layouts.app-minimal')

@section('title', $page->title . ' - Paramita')

@section('sidebar-nav')
    @include('partials.sidebar-nav')
@endsection

@section('header-title', $page->title)

@push('styles')
<style>
    .dashboard-hero {
        background: linear-gradient(135deg, rgba(0, 171, 85, 0.10), rgba(51, 102, 255, 0.08));
        border: 1px solid var(--grey-200);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .dashboard-hero h2 { color: var(--grey-900); font-size: 1.4rem; margin-bottom: 4px; }
    .dashboard-hero p { color: var(--grey-600); }
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .dashboard-stat {
        background: #fff;
        border: 1px solid var(--grey-200);
        border-radius: 8px;
        padding: 16px;
    }
    .dashboard-stat-label {
        color: var(--grey-500);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .dashboard-stat-value {
        color: var(--grey-900);
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1.15;
        margin-top: 6px;
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.85fr);
        gap: 16px;
    }
    .dashboard-card {
        background: #fff;
        border: 1px solid var(--grey-200);
        border-radius: 8px;
        padding: 16px;
        min-width: 0;
    }
    .dashboard-card h4 { color: var(--grey-800); font-size: 0.95rem; margin-bottom: 12px; }
    .dashboard-chart-box { overflow-x: auto; }
    .dashboard-line-svg { min-width: 560px; width: 100%; height: 220px; }
    .dashboard-axis-label { fill: var(--grey-500); font-size: 11px; }
    .dashboard-bar-row { display: grid; grid-template-columns: 120px 1fr 44px; align-items: center; gap: 10px; margin: 10px 0; }
    .dashboard-bar-label { color: var(--grey-600); font-size: 0.82rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dashboard-bar-track { height: 10px; border-radius: 999px; background: var(--grey-200); overflow: hidden; }
    .dashboard-bar-fill { height: 100%; border-radius: 999px; background: var(--secondary); }
    .dashboard-bar-value { color: var(--grey-700); font-size: 0.78rem; font-weight: 700; text-align: right; }
    .dashboard-donut-wrap { display: flex; align-items: center; gap: 18px; }
    .dashboard-donut {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        position: relative;
        flex: 0 0 auto;
    }
    .dashboard-donut::after {
        content: '';
        position: absolute;
        inset: 30px;
        border-radius: 50%;
        background: #fff;
    }
    .dashboard-legend { display: grid; gap: 8px; }
    .dashboard-legend-item { display: flex; align-items: center; gap: 8px; color: var(--grey-700); font-size: 0.84rem; }
    .dashboard-dot { width: 10px; height: 10px; border-radius: 999px; display: inline-block; }
    .dashboard-mini-list { display: grid; gap: 10px; }
    .dashboard-mini-item { display: flex; justify-content: space-between; gap: 12px; padding-bottom: 10px; border-bottom: 1px solid var(--grey-200); }
    .dashboard-mini-item:last-child { border-bottom: 0; padding-bottom: 0; }
    .dashboard-mini-label { color: var(--grey-600); }
    .dashboard-mini-value { color: var(--grey-900); font-weight: 800; }
    @media (max-width: 960px) { .dashboard-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div id="dynamic-page-content">
@if($page->description)
    <div style="margin-bottom: 20px; color: var(--grey-600); font-size: 0.9375rem;">
        {{ $page->description }}
    </div>
@endif

@forelse($templateSections as $section)
    @php $tpl = $section['template']; @endphp
    <div class="card mb-24">
        <div class="card-header">
            <div style="display: flex; align-items: center; gap: 12px;">
                <h3 class="card-title">
                    {{ $page->jsonTemplates->count() > 1 ? '📋 ' : '' }}{{ $tpl->name }}
                </h3>
                <span class="badge badge-info">v{{ $tpl->version }}</span>
                <span class="badge badge-secondary">{{ $tpl->category ?? 'uncategorized' }}</span>
                @if($section['fetched_at'])
                    <span style="font-size: 0.75rem; color: var(--grey-500);">
                        <span class="status-dot green" style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--success); margin-right: 4px;"></span>
                        Live • {{ $section['fetched_at']->format('H:i:s') }}
                    </span>
                @endif
            </div>
            @if($tpl->description)
                <span style="font-size: 0.8125rem; color: var(--grey-500);">{{ $tpl->description }}</span>
            @endif
        </div>
        <div class="card-body">
            @if(isset($section['no_endpoint']) && $section['no_endpoint'])
                <div style="background: rgba(0, 184, 217, 0.08); border: 1px solid rgba(0, 184, 217, 0.2); border-radius: 8px; padding: 16px 20px; color: var(--info);">
                    <strong>Tidak ada endpoint:</strong> Template "{{ $tpl->name }}" belum terhubung ke endpoint API.
                </div>
            @elseif($section['error'])
                <div style="background: rgba(255, 86, 48, 0.08); border: 1px solid rgba(255, 86, 48, 0.2); border-radius: 8px; padding: 16px 20px; color: var(--error);">
                    <strong>Gagal memuat data:</strong> {{ $section['error'] }}
                </div>
            @elseif($section['data'] && is_array($section['data']))
                @php $items = $section['data']; @endphp

                {{-- Summary Stats (if data is array of objects) --}}
                @if($page->slug === 'dashboard' && isset($items['summaryCards']) && is_array($items['summaryCards']))
                    @php
                        $dashboard = $items;
                        $summaryCards = $dashboard['summaryCards'] ?? [];
                        $charts = $dashboard['charts'] ?? [];
                        $deliveryStats = $dashboard['deliveryStats'] ?? [];
                        $lineChart = $charts['doByDate'] ?? [];
                        $lineData = $lineChart['data'] ?? [];
                        $lineMax = max(1, collect($lineData)->max(fn ($row) => (float) ($row['do'] ?? 0)) ?: 1);
                        $lineWidth = 640;
                        $lineHeight = 180;
                        $linePad = 28;
                        $lineCount = max(1, count($lineData) - 1);
                        $points = collect($lineData)->map(function ($row, $index) use ($lineWidth, $lineHeight, $linePad, $lineCount, $lineMax) {
                            $x = $linePad + (($lineWidth - ($linePad * 2)) * ($index / $lineCount));
                            $y = $linePad + (($lineHeight - ($linePad * 2)) * (1 - (((float) ($row['do'] ?? 0)) / $lineMax)));

                            return round($x, 1) . ',' . round($y, 1);
                        })->implode(' ');
                        $bars = $charts['DoEkspeditur']['data'] ?? [];
                        $barMax = max(1, collect($bars)->max(fn ($row) => (float) ($row['value'] ?? 0)) ?: 1);
                        $delivery = $charts['deliveryPercentage']['data'] ?? [];
                        $colors = ['#36B37E', '#FFAB00', '#FF5630', '#3366FF'];
                        $totalDelivery = max(1, collect($delivery)->sum(fn ($row) => (float) ($row['value'] ?? 0)));
                        $cursor = 0;
                        $segments = [];
                        foreach ($delivery as $index => $row) {
                            $value = (float) ($row['value'] ?? 0);
                            $end = $cursor + (($value / $totalDelivery) * 100);
                            $segments[] = ($colors[$index % count($colors)]) . ' ' . round($cursor, 2) . '% ' . round($end, 2) . '%';
                            $cursor = $end;
                        }
                        $donutStyle = 'conic-gradient(' . implode(', ', $segments) . ')';
                        $statusRows = $charts['DoStatus']['data'] ?? [];
                    @endphp

                    <div class="dashboard-hero">
                        <h2>{{ $dashboard['title'] ?? 'Dashboard' }}</h2>
                        <p>{{ $dashboard['semester'] ?? ($dashboard['message'] ?? 'Ringkasan data dashboard') }}</p>
                    </div>

                    <div class="dashboard-stats">
                        @foreach($summaryCards as $card)
                            <div class="dashboard-stat">
                                <div class="dashboard-stat-label">{{ $card['label'] ?? '-' }}</div>
                                <div class="dashboard-stat-value">
                                    {{ is_numeric($card['value'] ?? null) ? number_format((float) $card['value']) : ($card['value'] ?? '-') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="dashboard-grid">
                        <div class="dashboard-card">
                            <h4>{{ $lineChart['title'] ?? 'DO By Date' }}</h4>
                            <div class="dashboard-chart-box">
                                <svg class="dashboard-line-svg" viewBox="0 0 {{ $lineWidth }} {{ $lineHeight }}" role="img" aria-label="DO by date chart">
                                    @for($i = 0; $i < 4; $i++)
                                        @php $y = $linePad + (($lineHeight - ($linePad * 2)) * ($i / 3)); @endphp
                                        <line x1="{{ $linePad }}" y1="{{ $y }}" x2="{{ $lineWidth - $linePad }}" y2="{{ $y }}" stroke="#DFE3E8" stroke-width="1" />
                                    @endfor
                                    <polyline fill="none" stroke="#3366FF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $points }}" />
                                    @foreach($lineData as $index => $row)
                                        @php
                                            $x = $linePad + (($lineWidth - ($linePad * 2)) * ($index / $lineCount));
                                            $y = $linePad + (($lineHeight - ($linePad * 2)) * (1 - (((float) ($row['do'] ?? 0)) / $lineMax)));
                                        @endphp
                                        <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#3366FF" />
                                        @if($index === 0 || $index === count($lineData) - 1)
                                            <text class="dashboard-axis-label" x="{{ $x }}" y="{{ $lineHeight - 4 }}" text-anchor="middle">{{ $row['date'] ?? '' }}</text>
                                        @endif
                                    @endforeach
                                </svg>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <h4>{{ $charts['deliveryPercentage']['title'] ?? 'Delivery Percentage' }}</h4>
                            <div class="dashboard-donut-wrap">
                                <div class="dashboard-donut" style="background: {{ $donutStyle }};"></div>
                                <div class="dashboard-legend">
                                    @foreach($delivery as $index => $row)
                                        <div class="dashboard-legend-item">
                                            <span class="dashboard-dot" style="background: {{ $colors[$index % count($colors)] }};"></span>
                                            <span>{{ $row['label'] ?? '-' }}: <strong>{{ $row['value'] ?? '-' }}%</strong></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <h4>{{ $charts['DoEkspeditur']['title'] ?? 'DO By Ekspeditur' }}</h4>
                            @foreach($bars as $row)
                                @php $width = min(100, (((float) ($row['value'] ?? 0)) / $barMax) * 100); @endphp
                                <div class="dashboard-bar-row">
                                    <div class="dashboard-bar-label">{{ $row['label'] ?? '-' }}</div>
                                    <div class="dashboard-bar-track"><div class="dashboard-bar-fill" style="width: {{ $width }}%;"></div></div>
                                    <div class="dashboard-bar-value">{{ number_format((float) ($row['value'] ?? 0)) }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="dashboard-card">
                            <h4>{{ $charts['DoStatus']['title'] ?? 'DO Status' }}</h4>
                            <div class="dashboard-mini-list">
                                @foreach(array_merge($statusRows, $deliveryStats) as $row)
                                    <div class="dashboard-mini-item">
                                        <span class="dashboard-mini-label">{{ $row['label'] ?? '-' }}</span>
                                        <span class="dashboard-mini-value">{{ is_numeric($row['value'] ?? null) ? number_format((float) $row['value']) : ($row['value'] ?? '-') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if(isset($section['full_response']))
                        <details style="margin-top: 16px;">
                            <summary style="cursor: pointer; font-size: 0.8125rem; color: var(--grey-500); padding: 8px 0;">Lihat respons API lengkap</summary>
                            <div class="json-block" style="margin-top: 8px; font-size: 0.6875rem; max-height: 300px; overflow-y: auto;">
                                {{ json_encode($section['full_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                            </div>
                        </details>
                    @endif
                @elseif(count($items) > 0 && isset($items[0]) && is_array($items[0]))
                    @php
                        $templateKey = $tpl->id;
                        $tableQuery = $section['query_parameters'] ?? [];
                        $tableSearch = $tableQuery['search'] ?? null;
                        $resetQuery = request()->query();
                        unset($resetQuery['search'][$templateKey], $resetQuery['offset'][$templateKey]);
                        $resetUrl = $resetQuery === [] ? request()->url() : request()->url() . '?' . http_build_query($resetQuery);
                    @endphp
                    <form method="GET" data-dynamic-page-form style="display: flex; gap: 8px; align-items: center; margin-bottom: 16px; flex-wrap: wrap;">
                        <input type="hidden" name="limit[{{ $templateKey }}]" value="{{ $tableQuery['limit'] ?? 10 }}">
                        <input
                            type="search"
                            name="search[{{ $templateKey }}]"
                            value="{{ $tableSearch }}"
                            placeholder="Cari data..."
                            style="min-width: 240px; flex: 1; max-width: 420px; border: 1px solid var(--grey-300); border-radius: 8px; padding: 8px 12px; font: inherit; color: var(--grey-800);"
                        >
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        @if(filled($tableSearch))
                            <a href="{{ $resetUrl }}" data-dynamic-page-link class="btn btn-secondary btn-sm">Reset</a>
                        @endif
                    </form>

                    @php $totalItems = count($items); @endphp

                    @if($totalItems === 0)
                        <div class="empty-state">
                            <h3>Data tidak ditemukan</h3>
                            <p>Tidak ada data yang cocok dengan pencarian "{{ $tableSearch }}".</p>
                        </div>
                    @else
                    @php
                        // Auto-detect numeric fields for stats
                        $numericFields = [];
                        $firstItem = $items[0];
                        foreach ($firstItem as $key => $val) {
                            if (is_numeric($val) || is_int($val) || is_float($val)) {
                                $numericFields[] = $key;
                            }
                        }
                    @endphp
                    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); margin-bottom: 20px;">
                        <div class="stat-card" style="padding: 16px;">
                            <div class="stat-icon blue" style="width: 40px; height: 40px; font-size: 1rem;">📦</div>
                            <div class="stat-content">
                                <div class="stat-label">Total Item</div>
                                <div class="stat-value" style="font-size: 1.25rem;">{{ number_format($totalItems) }}</div>
                            </div>
                        </div>
                        @foreach(array_slice($numericFields, 0, 3) as $field)
                            @php
                                $sum = collect($items)->sum(fn($i) => (float)($i[$field] ?? 0));
                                $label = ucfirst(str_replace(['_', '-'], ' ', $field));
                            @endphp
                            <div class="stat-card" style="padding: 16px;">
                                <div class="stat-icon green" style="width: 40px; height: 40px; font-size: 1rem;">📊</div>
                                <div class="stat-content">
                                    <div class="stat-label">{{ $label }}</div>
                                    <div class="stat-value" style="font-size: 1.25rem;">{{ number_format($sum, $sum == round($sum) ? 0 : 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Data Table --}}
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    @foreach(array_keys($firstItem) as $col)
                                        <th>{{ ucfirst(str_replace(['_', '-'], ' ', $col)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    @foreach($item as $val)
                                        <td>{{ is_numeric($val) ? $val : (is_string($val) ? (strlen($val) > 60 ? substr($val, 0, 60) . '...' : $val) : '-') }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Info --}}
                    @if($section['pagination'])
                    @php
                        $pagination = $section['pagination'] ?? [];
                        $limit = (int) ($pagination['limit'] ?? ($tableQuery['limit'] ?? 10));
                        $offset = (int) ($pagination['offset'] ?? ($tableQuery['offset'] ?? 0));
                        $totalAvailable = (int) ($pagination['total'] ?? count($items));
                        $prevOffset = $pagination['prev_offset'] ?? null;
                        $nextOffset = $pagination['next_offset'] ?? null;
                        $pageQuery = request()->query();
                        $pageQuery['limit'][$templateKey] = $limit;

                        if (filled($tableSearch)) {
                            $pageQuery['search'][$templateKey] = $tableSearch;
                        } else {
                            unset($pageQuery['search'][$templateKey]);
                        }

                        $prevQuery = $pageQuery;
                        $nextQuery = $pageQuery;

                        if ($prevOffset !== null) {
                            $prevQuery['offset'][$templateKey] = $prevOffset;
                        }

                        if ($nextOffset !== null) {
                            $nextQuery['offset'][$templateKey] = $nextOffset;
                        }
                    @endphp
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--grey-200); display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <div style="font-size: 0.8125rem; color: var(--grey-600);">
                            Menampilkan {{ count($items) }} item
                            dari {{ number_format($totalAvailable) }} total
                        </div>
                        <div style="font-size: 0.8125rem; color: var(--grey-500); display: flex; gap: 16px; align-items: center;">
                            <span>Total: <strong>{{ $totalAvailable }}</strong></span>
                            <span>Limit: <strong>{{ $limit }}</strong></span>
                            <span>Offset: <strong>{{ $offset }}</strong></span>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            @if($prevOffset !== null)
                                <a class="btn btn-secondary btn-sm" data-dynamic-page-link href="{{ request()->url() . '?' . http_build_query($prevQuery) }}">Prev</a>
                            @else
                                <span class="btn btn-secondary btn-sm" style="opacity: .45; cursor: not-allowed;">Prev</span>
                            @endif

                            @if($nextOffset !== null)
                                <a class="btn btn-primary btn-sm" data-dynamic-page-link href="{{ request()->url() . '?' . http_build_query($nextQuery) }}">Next</a>
                            @else
                                <span class="btn btn-secondary btn-sm" style="opacity: .45; cursor: not-allowed;">Next</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endif
                @elseif(count($items) > 0)
                    {{-- Key-value display --}}
                    <div class="grid-2">
                        @foreach($items as $key => $val)
                            @if(is_scalar($val) || is_null($val))
                            <div style="padding: 8px 0; border-bottom: 1px solid var(--grey-100);">
                                <div class="stat-label">{{ ucfirst(str_replace(['_', '-'], ' ', $key)) }}</div>
                                <div style="font-weight: 600;">{{ $val ?? '-' }}</div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @php
                        // Show nested arrays as JSON
                        $nested = array_filter($items, fn($v) => is_array($v), ARRAY_FILTER_USE_BOTH);
                    @endphp
                    @if(!empty($nested))
                        <div class="json-block" style="margin-top: 16px; font-size: 0.75rem;">
                            {{ json_encode($nested, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <h3>Data kosong</h3>
                        <p>Template "{{ $tpl->name }}" tidak mengembalikan data.</p>
                    </div>
                @endif

                {{-- Raw response toggle --}}
                @if(isset($section['full_response']))
                <details style="margin-top: 16px;">
                    <summary style="cursor: pointer; font-size: 0.8125rem; color: var(--grey-500); padding: 8px 0;">📄 Lihat respons API lengkap</summary>
                    <div class="json-block" style="margin-top: 8px; font-size: 0.6875rem; max-height: 300px; overflow-y: auto;">
                        {{ json_encode($section['full_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                    </div>
                </details>
                @endif
            @else
                <div class="empty-state">
                    <h3>Tidak ada data</h3>
                    <p>Template "{{ $tpl->name }}" belum memiliki endpoint yang terhubung.</p>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="card">
        <div class="empty-state">
            <h3>Halaman belum memiliki template</h3>
            <p>Admin dapat menambahkan JSON Template ke halaman ini melalui panel admin.</p>
        </div>
    </div>
@endforelse
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var contentId = 'dynamic-page-content';

    function swapContent(url, push) {
        var content = document.getElementById(contentId);
        if (!content) return;

        content.style.opacity = '0.55';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { return response.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var next = doc.getElementById(contentId);
                if (!next) {
                    window.location.href = url;
                    return;
                }

                content.innerHTML = next.innerHTML;
                content.style.opacity = '';

                if (push) history.pushState(null, '', url);
            })
            .catch(function () {
                window.location.href = url;
            });
    }

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('[data-dynamic-page-form]');
        if (!form) return;

        event.preventDefault();

        var url = new URL(form.action || window.location.href, window.location.origin);
        var params = new URLSearchParams(window.location.search);

        new FormData(form).forEach(function (value, key) {
            if (value === '') {
                params.delete(key);
                return;
            }

            params.set(key, value);

            var match = key.match(/^search\[(.+)]$/);
            if (match) params.delete('offset[' + match[1] + ']');
        });

        url.search = params.toString();
        swapContent(url.toString(), true);
    });

    document.addEventListener('click', function (event) {
        var link = event.target.closest('[data-dynamic-page-link]');
        if (!link) return;

        event.preventDefault();
        swapContent(link.href, true);
    });

    window.addEventListener('popstate', function () {
        swapContent(window.location.href, false);
    });
});
</script>
@endpush
