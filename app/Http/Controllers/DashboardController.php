<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Helpers\VendorApiAuthHelper;
use Illuminate\Support\Facades\Log;
use App\Models\Vendor;
use App\Models\VendorApi;
use App\Models\ApiEndpoint;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = optional($user->roles->first())->name;
        
        $vendorApis = ApiEndpoint::with(['vendorApi', 'vendorApi.vendor'])
                        ->where('api_endpoints.name', 'dashboard')
                        ->get();
        
        $vendorDashboardData = [];

        foreach ($vendorApis as $api) {
            try {
                $baseUrl = rtrim($api->vendorApi->base_url, '/');
                $client = Http::withHeaders(['Accept' => 'application/json'])
                    ->withOptions(['verify' => false]);

                if ($api->requires_auth) {
                    $authClient = VendorApiAuthHelper::authenticate($api->vendorApi, $baseUrl);
                    if ($authClient) {
                        $client = $authClient;
                    } else {
                        continue;
                    }
                }

                $url = rtrim($baseUrl . $api->path);
                $data = $client->get($url);
                if ($data->successful()) {
                    $vendorDashboardData[$api->vendorApi->api_name] = $data->json('data');
                } else {
                    $vendorDashboardData[$api->vendorApi->api_name] = null;
                    Log::error('Failed to get dashboard data from ' . $api->vendorApi->name . ': ' . $data->status());
                    continue;
                }
            } catch (\Exception $e) {
                Log::error('Error with vendor API ' . $api->vendorApi->name . ': ' . $e->getMessage());
            }
        }
        
        return view('dashboard', [
            'dashboardData' => $vendorDashboardData,
            'role' => $role,
        ]);
    }
}
