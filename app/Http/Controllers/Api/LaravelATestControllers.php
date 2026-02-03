<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaravelATestControllers extends Controller
{
    protected $laravelAUrl;
    protected $timeout;

    public function __construct()
    {
        $this->laravelAUrl = env('LARAVEL_A_URL', 'http://localhost:8000');
        $this->timeout = env('LARAVEL_A_TIMEOUT', 30);
    }

    /**
     * Test CORS connection ke Laravel A
     * GET /api/test/cors-connection
     */
    public function testCorsConnection()
    {
        Log::info('🔄 Testing CORS connection to Laravel A', [
            'url' => $this->laravelAUrl,
            'timeout' => $this->timeout
        ]);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-Test-Request' => 'from-laravel-b-cors-test'
                ])
                ->get($this->laravelAUrl . '/api/test-cors');

            $result = [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'data' => $response->json(),
                'headers' => [
                    'access-control-allow-origin' => $response->header('Access-Control-Allow-Origin'),
                    'access-control-allow-methods' => $response->header('Access-Control-Allow-Methods'),
                    'access-control-allow-headers' => $response->header('Access-Control-Allow-Headers'),
                ],
                'response_time' => $response->transferStats ? $response->transferStats->getTransferTime() : null,
                'test_url' => $this->laravelAUrl . '/api/test-cors'
            ];

            Log::info('CORS Test Result:', $result);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('❌ CORS Test Failed:', [
                'error' => $e->getMessage(),
                'url' => $this->laravelAUrl . '/api/test-cors'
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to connect to Laravel A',
                'test_url' => $this->laravelAUrl . '/api/test-cors'
            ], 500);
        }
    }

    /**
     * Test approve transaction ke Laravel A
     * POST /api/test/approve-transaction
     */
    public function testApproveTransaction(Request $request)
    {
        $testData = $request->validate([
            'type' => 'nullable|string|default:INV',
            'tahun' => 'nullable|string|default:2024',
            'kode' => 'nullable|string|default:TEST001',
            'status' => 'nullable|string|in:pending,approved,rejected|default:approved',
            'notes' => 'nullable|string'
        ]);

        $type = $testData['type'] ?? 'INV';
        $tahun = $testData['tahun'] ?? '2024';
        $kode = $testData['kode'] ?? 'TEST001';

        $url = "{$this->laravelAUrl}/api/transaksi/{$type}/{$tahun}/{$kode}/approve";

        Log::info('🔄 Testing approve transaction to Laravel A', [
            'url' => $url,
            'data' => $testData
        ]);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-Test-Request' => 'from-laravel-b-approve-test',
                    'X-Client' => 'Laravel-B-Test'
                ])
                ->post($url, [
                    'status' => $testData['status'],
                    'approved_by' => 'test_admin@laravel-b.com',
                    'timestamp' => now()->toIso8601String(),
                    'metode_pembayaran' => 'transfer',
                    'uang_dibayar' => 1500000,
                    'notes' => $testData['notes'] ?? 'Test transaction from Laravel B'
                ]);

            $result = [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'data' => $response->json(),
                'headers' => [
                    'access-control-allow-origin' => $response->header('Access-Control-Allow-Origin'),
                    'access-control-allow-methods' => $response->header('Access-Control-Allow-Methods'),
                ],
                'cors_check' => [
                    'origin_allowed' => $response->header('Access-Control-Allow-Origin') === 'http://localhost:8001',
                    'has_cors_headers' => !empty($response->header('Access-Control-Allow-Origin'))
                ],
                'request_url' => $url,
                'request_data' => $testData
            ];

            Log::info('Approve Transaction Test Result:', $result);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('❌ Approve Transaction Test Failed:', [
                'error' => $e->getMessage(),
                'url' => $url,
                'data' => $testData
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to send approve request to Laravel A',
                'request_url' => $url
            ], 500);
        }
    }

    /**
     * Test semua endpoints Laravel A
     * GET /api/test/laravel-a-all
     */
    public function testAllLaravelAEndpoints()
    {
        $endpoints = [
            'test-cors' => '/api/test-cors',
            'test-signature' => '/api/test-signature',
            'test-webhook' => '/api/test-webhook',
            'health' => '/api/health'
        ];

        $results = [];

        foreach ($endpoints as $name => $path) {
            try {
                $url = $this->laravelAUrl . $path;

                Log::info("Testing Laravel A endpoint: {$name}", ['url' => $url]);

                $response = Http::timeout(10)->get($url);

                $results[$name] = [
                    'success' => $response->successful(),
                    'status' => $response->status(),
                    'has_cors' => !empty($response->header('Access-Control-Allow-Origin')),
                    'cors_origin' => $response->header('Access-Control-Allow-Origin'),
                    'response_time' => $response->transferStats ? $response->transferStats->getTransferTime() : null,
                    'url' => $url
                ];

                // Add response data for specific endpoints
                if ($name === 'test-cors') {
                    $results[$name]['data'] = $response->json();
                }

            } catch (\Exception $e) {
                $results[$name] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'url' => $url ?? $path
                ];
            }

            // Small delay between requests
            usleep(100000); // 0.1 second
        }

        // Summary
        $successCount = count(array_filter($results, fn($r) => $r['success'] ?? false));
        $totalCount = count($results);

        $summary = [
            'total_endpoints' => $totalCount,
            'successful' => $successCount,
            'failed' => $totalCount - $successCount,
            'laravel_a_url' => $this->laravelAUrl,
            'tested_at' => now()->toIso8601String()
        ];

        Log::info('All Endpoints Test Summary:', $summary);

        return response()->json([
            'summary' => $summary,
            'results' => $results,
            'recommendation' => $successCount === $totalCount
                ? '✅ All endpoints are working correctly!'
                : '⚠️ Some endpoints have issues. Check individual results.'
        ]);
    }

    /**
     * Simple ping test
     * GET /api/test/ping-laravel-a
     */
    public function pingLaravelA()
    {
        try {
            $start = microtime(true);
            $response = Http::timeout(5)->get($this->laravelAUrl . '/api/test-cors');
            $end = microtime(true);

            $responseTime = round(($end - $start) * 1000, 2); // in ms

            return response()->json([
                'status' => 'online',
                'response_time_ms' => $responseTime,
                'http_status' => $response->status(),
                'has_cors' => !empty($response->header('Access-Control-Allow-Origin')),
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], 503);
        }
    }

    /**
     * Test dengan berbagai HTTP methods
     * GET /api/test/http-methods
     */
    public function testHttpMethods()
    {
        $methods = ['GET', 'POST', 'OPTIONS'];
        $url = $this->laravelAUrl . '/api/test-cors';

        $results = [];

        foreach ($methods as $method) {
            try {
                $response = null;

                switch ($method) {
                    case 'GET':
                        $response = Http::get($url);
                        break;
                    case 'POST':
                        $response = Http::post($url, ['test' => 'data']);
                        break;
                    case 'OPTIONS':
                        $response = Http::withHeaders([
                            'Access-Control-Request-Method' => 'POST',
                            'Access-Control-Request-Headers' => 'Content-Type'
                        ])->send('OPTIONS', $url);
                        break;
                }

                $results[$method] = [
                    'success' => $response ? $response->successful() : false,
                    'status' => $response ? $response->status() : null,
                    'has_cors_headers' => $response ? [
                        'allow_origin' => $response->header('Access-Control-Allow-Origin'),
                        'allow_methods' => $response->header('Access-Control-Allow-Methods'),
                        'allow_headers' => $response->header('Access-Control-Allow-Headers')
                    ] : null
                ];

            } catch (\Exception $e) {
                $results[$method] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'test_url' => $url,
            'methods_tested' => $methods,
            'results' => $results,
            'cors_summary' => [
                'supports_options' => $results['OPTIONS']['success'] ?? false,
                'allows_post' => str_contains($results['OPTIONS']['has_cors_headers']['allow_methods'] ?? '', 'POST'),
                'allows_cors_from_b' => $results['GET']['has_cors_headers']['allow_origin'] === 'http://localhost:8001'
            ]
        ]);
    }

    /**
     * Debug informasi konfigurasi
     * GET /api/test/debug-config
     */
    public function debugConfig()
    {
        return response()->json([
            'laravel_b_config' => [
                'app_url' => config('app.url'),
                'app_env' => config('app.env'),
                'timezone' => config('app.timezone'),
            ],
            'laravel_a_config' => [
                'url' => $this->laravelAUrl,
                'timeout' => $this->timeout,
                'env_url' => env('LARAVEL_A_URL'),
            ],
            'http_client' => [
                'guzzle_installed' => class_exists('GuzzleHttp\Client'),
                'http_facade_available' => class_exists('Illuminate\Support\Facades\Http'),
            ],
            'server' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_time' => now()->toIso8601String(),
                'server_ip' => request()->server('SERVER_ADDR'),
                'client_ip' => request()->ip(),
            ]
        ]);
    }
    // app/Http/Controllers/Api/LaravelATestController.php di Laravel B
// Tambahkan method ini:

public function testRealApprove()
{
    $url = "http://localhost:8000/api/transaksi/INV/2024/REAL001/approve";

    Log::info('🔄 Testing REAL approve transaction', ['url' => $url]);

    try {
        $response = Http::timeout(10)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Client' => 'Laravel-B-Production',
                'X-Request-ID' => uniqid('prod-', true)
            ])
            ->post($url, [
                'status' => 'approved',
                'approved_by' => 'production_admin',
                'timestamp' => now()->toIso8601String(),
                'metode_pembayaran' => 'bank_transfer',
                'uang_dibayar' => 2500000,
                'notes' => 'Real transaction from Laravel B production',
                'additional_data' => [
                    'payment_reference' => 'REF-' . time(),
                    'channel' => 'api_integration'
                ]
            ]);

        $result = [
            'success' => $response->successful(),
            'status_code' => $response->status(),
            'data' => $response->json(),
            'cors_check' => [
                'origin_allowed' => $response->header('Access-Control-Allow-Origin') === 'http://localhost:8001',
                'headers_present' => [
                    'allow_origin' => !empty($response->header('Access-Control-Allow-Origin')),
                    'allow_methods' => !empty($response->header('Access-Control-Allow-Methods'))
                ]
            ],
            'request_details' => [
                'url' => $url,
                'method' => 'POST',
                'timestamp' => now()->toIso8601String()
            ]
        ];

        Log::info('Real Approve Test Result:', $result);

        return response()->json($result);

    } catch (\Exception $e) {
        Log::error('❌ Real Approve Test Failed:', [
            'error' => $e->getMessage(),
            'url' => $url
        ]);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Failed to send real approve request'
        ], 500);
    }
}
}
