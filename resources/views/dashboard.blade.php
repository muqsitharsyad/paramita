@extends('layouts.app')

@section('content')
<div class="container-fluid" style="background: #f6f8fb; min-height: 100vh;">
    <div class="row">
        <div class="col-md-2" style="background: #eaf0fa; min-height: 100vh;">
            <div class="py-4 px-3">
                <img src="/favicon.ico" alt="Logo" style="width: 48px;">
                <h4 class="mt-3 mb-4" style="color: #3b4a6b;">Temprina</h4>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a class="nav-link active" href="#">Dashboard</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Monitoring Stock Packet</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Monitoring Stock Item</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Distribution Map</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Analisa SLA</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Distribution Map By Vendor</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Delivery Analysis</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Reason Monitoring</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Client Order</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Lead Time</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Tracking Delivery</a></li>
                    <li class="nav-item mb-2"><a class="nav-link" href="#">Setting</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center py-4 px-4">
                <h2 class="mb-0" style="color: #3b4a6b;">Dashboard</h2>
                <div>
                    <span class="badge bg-success">Online</span>
                    <span class="ms-2">Admin Puslaba</span>
                    <img src="https://ui-avatars.com/api/?name=Admin" alt="Avatar" style="width:32px; border-radius:50%; margin-left:8px;">
                </div>
            </div>
            <div class="px-4">
                <!-- Tabs for each vendor -->
                <ul class="nav nav-tabs mb-3" id="vendorTab" role="tablist">
                    @php $i = 0; @endphp
                    @foreach($dashboardData as $vendorName => $vendorData)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($i === 0) active @endif" id="tab-{{ Str::slug($vendorName) }}" data-bs-toggle="tab" data-bs-target="#content-{{ Str::slug($vendorName) }}" type="button" role="tab" aria-controls="content-{{ Str::slug($vendorName) }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">{{ $vendorName }}</button>
                        </li>
                        @php $i++; @endphp
                    @endforeach
                </ul>
                <div class="tab-content" id="vendorTabContent">
                    @php $i = 0; @endphp
                    @foreach($dashboardData as $vendorName => $vendorData)
                        <div class="tab-pane fade @if($i === 0) show active @endif" id="content-{{ Str::slug($vendorName) }}" role="tabpanel" aria-labelledby="tab-{{ Str::slug($vendorName) }}">
                            <h4 class="mb-3">{{ $vendorData['title'] ?? $vendorName }} <small class="text-muted">{{ $vendorData['semester'] ?? '' }}</small></h4>
                            <div class="row mb-4">
                                @foreach($vendorData['summaryCards'] ?? [] as $card)
                                    <div class="col-md-2">
                                        <div class="card text-center" style="background:#e3e8ff;">
                                            <div class="card-body">
                                                <div>{{ $card['label'] }}</div>
                                                <div class="fw-bold fs-4">{{ $card['value'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div id="chart_do_by_date_{{ Str::slug($vendorName) }}" style="height:300px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div id="chart_persentase_ba_{{ Str::slug($vendorName) }}" style="height:300px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                @foreach($vendorData['deliveryStats'] ?? [] as $stat)
                                    <div class="col-md-2">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <div>{{ $stat['label'] }}</div>
                                                <div class="fw-bold fs-4">{{ $stat['value'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @php $i++; @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Highcharts CDN & Bootstrap JS (for tabs) -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($dashboardData as $vendorName => $vendorData)
        // DO By Date Chart
        Highcharts.chart('chart_do_by_date_{{ Str::slug($vendorName) }}', {
            chart: { type: 'line' },
            title: { text: '{{ $vendorData['charts']['doByDate']['title'] ?? 'DO By Date' }}' },
            xAxis: { categories: {!! json_encode(collect($vendorData['charts']['doByDate']['data'] ?? [])->pluck('date')) !!} },
            yAxis: { title: { text: 'Jumlah DO' } },
            series: [{ name: 'DO', data: {!! json_encode(collect($vendorData['charts']['doByDate']['data'] ?? [])->pluck('do')) !!} }]
        });
        // Delivery Percentage Chart
        Highcharts.chart('chart_persentase_ba_{{ Str::slug($vendorName) }}', {
            chart: { type: 'pie' },
            title: { text: '{{ $vendorData['charts']['deliveryPercentage']['title'] ?? 'Persentase Paket BA Terkirim' }}' },
            series: [{
                name: 'Persentase',
                colorByPoint: true,
                data: {!! json_encode(collect($vendorData['charts']['deliveryPercentage']['data'] ?? [])->map(function($item){ return ['name'=>$item['label'],'y'=>$item['value']]; })->values()) !!}
            }]
        });
    @endforeach
});
</script>
@endsection
