<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorApi;

Route::middleware('auth:sanctum')->get('/dashboard', function (Request $request) {
    $user = Auth::user();
    // Ambil vendor yang terdaftar untuk user ini
    $vendors = VendorApi::whereHas('users', function($q) use ($user) {
        $q->where('id', $user->id);
    })->get();
    $result = [
        'do_today' => 0,
        'do_next_day' => 0,
        'do_waiting' => 0,
        'do_in_progress' => 0,
        'do_done' => 0,
        'total_order' => 0,
        'waybill_today' => 0,
        'on_delivery' => 0,
        'retur' => 0,
        'delivered_mahasiswa' => 0,
        'total_delivered' => 0,
        'total_waybill' => 0,
        'do_by_date' => [
            'dates' => [],
            'values' => [],
        ],
        'persentase_ba' => [],
    ];
    // Loop vendor dan ambil data dari endpoint masing-masing
    foreach ($vendors as $vendor) {
        // Contoh: request ke endpoint vendor
        // $data = Http::withToken($vendor->api_token)->get($vendor->dashboard_endpoint)->json();
        // Lalu merge ke $result sesuai kebutuhan
        // ...
    }
    // Dummy data untuk demo
    $result['do_today'] = 0;
    $result['do_next_day'] = 0;
    $result['do_waiting'] = 0;
    $result['do_in_progress'] = 0;
    $result['do_done'] = 35331;
    $result['total_order'] = 35331;
    $result['waybill_today'] = 0;
    $result['on_delivery'] = 0;
    $result['retur'] = 28;
    $result['delivered_mahasiswa'] = 35203;
    $result['total_delivered'] = 35331;
    $result['total_waybill'] = 35331;
    $result['do_by_date'] = [
        'dates' => ['Jan 12', 'Jan 13', 'Jan 22', 'Feb 5', 'Feb 11', 'Feb 17', 'Feb 23', 'Mar 3', 'Mar 9', 'Apr 10'],
        'values' => [62, 194, 485, 100, 1369, 1087, 3354, 5013, 1854, 137],
    ];
    $result['persentase_ba'] = [
        ['name' => 'Terkirim ke Mahasiswa', 'y' => 99.9],
        ['name' => 'Dikirim ke UT', 'y' => 0.1],
        ['name' => 'Belum Terkirim', 'y' => 0.0],
    ];
    return response()->json($result);
});
