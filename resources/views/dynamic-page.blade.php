@extends('layouts.app-minimal')

@section('title', $page->title . ' - Paramita')

@section('sidebar-nav')
    @include('partials.sidebar-nav')
@endsection

@section('header-title', $page->title)

@section('content')
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
                @if(count($items) > 0 && isset($items[0]) && is_array($items[0]))
                    @php
                        $totalItems = count($items);
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
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--grey-200); display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.8125rem; color: var(--grey-600);">
                            Menampilkan {{ count($items) }} item
                            @if(isset($section['pagination']['total']))
                                dari {{ number_format($section['pagination']['total']) }} total
                            @endif
                        </div>
                        <div style="font-size: 0.8125rem; color: var(--grey-500); display: flex; gap: 16px; align-items: center;">
                            @if(isset($section['pagination']['total']))
                                <span>Total: <strong>{{ $section['pagination']['total'] }}</strong></span>
                            @endif
                            @if(isset($section['pagination']['limit']))
                                <span>Limit: <strong>{{ $section['pagination']['limit'] }}</strong></span>
                            @endif
                            @if(isset($section['pagination']['offset']))
                                <span>Offset: <strong>{{ $section['pagination']['offset'] }}</strong></span>
                            @endif
                        </div>
                    </div>
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
@endsection
