<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LaravelAService
{
    protected $baseUrl;
    protected $webhookSecret;

    public function __construct()
    {
        $this->baseUrl = env('LARAVEL_A_URL', 'http://localhost:8001');
        $this->webhookSecret = env('WEBHOOK_SECRET', 'yQw3qgDGsYp7HsApogJfTEJYMbRWIIvH');
    }

    /**
     * Kirim approval ke Laravel A
     */
    public function approveTransaction($type, $tahun, $kode, $data = [])
    {
        try {
            $url = "{$this->baseUrl}/api/transaksi/{$type}/{$tahun}/{$kode}/approve";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $data);

            Log::info('Response from Laravel A:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Error calling Laravel A API:', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            throw $e;
        }
    }

    /**
     * Generate signature untuk keamanan webhook
     */
    public function generateSignature($payload)
    {
        $payloadString = json_encode($payload);
        return hash_hmac('sha256', $payloadString, $this->webhookSecret);
    }
    public function testCorsConnection()
{
    try {
        $response = Http::get('http://localhost:8000/api/test-cors');

        return response()->json([
            'success' => $response->successful(),
            'status' => $response->status(),
            'data' => $response->json(),
            'headers' => [
                'access-control-allow-origin' => $response->header('Access-Control-Allow-Origin')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
}
