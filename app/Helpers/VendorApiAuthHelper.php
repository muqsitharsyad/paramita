<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\VendorApi;

class VendorApiAuthHelper
{
    /**
     * Authenticate to a vendor API and return a valid HTTP client with token if successful.
     * Optionally saves the token to the VendorApi model.
     *
     * @param VendorApi $vendorApi
     * @param string $baseUrl
     * @return \Illuminate\Http\Client\PendingRequest|null
     */
    public static function authenticate(VendorApi $vendorApi, string $baseUrl)
    {
        $client = Http::withHeaders(['Accept' => 'application/json'])
            ->withOptions(['verify' => false]);

        // Only try login if credentials are available
        if ($vendorApi->email && $vendorApi->password) {
            $loginResponse = $client->post(rtrim($baseUrl, '/') . '/login', [
                'email' => $vendorApi->email,
                'password' => $vendorApi->password,
            ]);

            if ($loginResponse->successful()) {
                $token = $loginResponse->json('access_token') ?? $loginResponse->json('token');
                if ($token) {
                    // Save token to database
                    $vendorApi->auth_credentials = $token;
                    $vendorApi->save();
                    return $client->withToken($token);
                }
            } else {
                Log::error('Login failed for vendor API: ' . $vendorApi->api_name);
            }
        }
        return null;
    }
}
