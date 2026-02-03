<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Transaction;

class ManajemenPesanan extends Component
{
    use WithPagination;

    // Properti untuk filter dan pencarian
    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Properti untuk sync ke Laravel A
    public $selectedOrders = [];
    public $syncStatus = '';
    public $isSyncing = false;
    public $debugInfo = '';

    // Properti untuk modal detail
    public $selectedOrder = null;
    public $selectedTransaction = null;

    // Opsi untuk dropdown filter status
    public $statusOptions = [
        '' => 'Semua Status',
        'pending' => 'Pending',
        'success' => 'Success',
        'failed' => 'Failed',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
        'proses' => 'Proses',
    ];

    // Sync configuration
    public $laravelAUrl = 'http://localhost:8000';
    public $enableRetry = true;
    public $maxRetries = 3;

    protected $queryString = ['search', 'statusFilter', 'perPage'];

    /**
     * SYNC TO LARAVEL A - FUNGSI UTAMA
     * Mengirim data ke Laravel A dengan multiple endpoint fallback
     */


    /**
     * Sync selected orders to Laravel A
     */

    /**
     * Sync all orders to Laravel A
     */
    public function syncAllToLaravelA()
    {
        $this->isSyncing = true;
        $this->syncStatus = '🔄 Memulai sync semua order ke Laravel A...';
        $this->debugInfo = '';

        try {
            // Get all order IDs
            $allOrderIds = Order::whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->pluck('id')
                ->toArray();

            $total = count($allOrderIds);

            if ($total === 0) {
                $this->syncStatus = 'ℹ️ Tidak ada order untuk disync';
                $this->isSyncing = false;
                return;
            }

            $startTime = microtime(true);

            // Process in batches untuk menghindari timeout
            $batchSize = 10;
            $batches = array_chunk($allOrderIds, $batchSize);
            $totalSynced = 0;
            $totalSimulated = 0;
            $totalFailed = 0;
            $batchNumber = 1;
            $allResults = [];

            foreach ($batches as $batchOrderIds) {
                $this->syncStatus = "🔄 Processing batch {$batchNumber}/" . count($batches) .
                                  " (" . count($batchOrderIds) . " orders)...";

                $batchResult = $this->syncToLaravelA($batchOrderIds);

                $totalSynced += $batchResult['synced_count'];
                $totalSimulated += $batchResult['simulated_count'];
                $totalFailed += $batchResult['failed_count'];
                $allResults = array_merge($allResults, $batchResult['results']);

                $this->debugInfo .= "Batch {$batchNumber}: " .
                                  $batchResult['synced_count'] . " saved, " .
                                  $batchResult['simulated_count'] . " simulated, " .
                                  $batchResult['failed_count'] . " failed\n";

                $batchNumber++;

                // Delay between batches
                if ($batchNumber <= count($batches)) {
                    sleep(1); // 1 second delay
                }
            }

            $endTime = microtime(true);
            $totalTime = round($endTime - $startTime, 2);

            // Tampilkan hasil akhir
            $finalResult = [
                'synced_count' => $totalSynced,
                'simulated_count' => $totalSimulated,
                'failed_count' => $totalFailed,
                'total' => $total,
                'results' => $allResults
            ];

            $this->showSyncResults($finalResult, $totalTime);

            Log::info('Bulk sync to Laravel A completed', [
                'total_orders' => $total,
                'saved' => $totalSynced,
                'simulated' => $totalSimulated,
                'failed' => $totalFailed,
                'batches' => count($batches),
                'total_time' => $totalTime
            ]);

        } catch (\Exception $e) {
            $this->syncStatus = '💥 Bulk sync error: ' . $e->getMessage();
            $this->debugInfo = "Error:\n" . $e->getTraceAsString();
            Log::error('💥 Sync all error: ' . $e->getMessage());
        }

        $this->isSyncing = false;
    }

    /**
     * Prepare transaction data for sync
     */
    private function prepareTransactionDataForSync(Order $order)
    {
        return [
            'transaction_number' => $order->order_number,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name ?? 'Customer ' . $order->id,
            'customer_email' => $order->customer_email ?? 'no-email@example.com',
            'product_name' => $order->product?->name ?? 'Unknown Product',
            'amount' => floatval($order->final_price),
            'original_amount' => floatval($order->original_price),
            'discount_amount' => floatval($order->discount_amount ?? 0),
            'status' => $this->mapStatusForLaravelA($order->status),
            'payment_type' => $order->transaction?->payment_type ?? 'cash',
            'payment_proof' => $order->transaction?->payment_proof ?? null,
            'notes' => $order->notes ?? 'Order from Laravel B',
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'source_system' => 'laravel_b',
            'sync_id' => 'b2a-sync-' . uniqid(),
            'demo_mode' => false,
            'simulate_integration' => false,
            'save_to_database' => true,
            'is_real_sync' => true
        ];
    }

    /**
     * Map status dari Laravel B ke Laravel A
     */
    private function mapStatusForLaravelA($status)
    {
        $statusMap = [
            'pending' => 'pending',
            'success' => 'settlement',
            'failed' => 'failed',
            'expired' => 'expired',
            'cancelled' => 'cancelled',
            'proses' => 'pending'
        ];
        return $statusMap[$status] ?? 'pending';
    }

    /**
     * Send transaction data to Laravel A dengan retry mechanism
     */
    private function sendToLaravelA($endpoint, $data, $orderNumber, $enableRetry = true)
    {
        $maxRetries = $enableRetry ? $this->maxRetries : 1;
        $retryDelay = 1000; // milliseconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $fullUrl = $this->laravelAUrl . $endpoint;

                Log::info('Sending to Laravel A', [
                    'order_number' => $orderNumber,
                    'attempt' => $attempt,
                    'endpoint' => $endpoint,
                    'url' => $fullUrl
                ]);

                $response = Http::timeout(30)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'X-Request-ID' => 'laravel-b-sync-' . uniqid(),
                        'X-Order-Number' => $orderNumber,
                        'X-Source-System' => 'laravel_b'
                    ])
                    ->post($fullUrl, $data);

                if ($response->successful()) {
                    $responseData = $response->json();

                    Log::info('✅ Response from Laravel A', [
                        'order_number' => $orderNumber,
                        'attempt' => $attempt,
                        'response' => $responseData
                    ]);

                    return [
                        'success' => true,
                        'data' => $responseData
                    ];
                } else {
                    $error = "HTTP {$response->status()}: " . substr($response->body(), 0, 200);

                    Log::warning('⚠️ Laravel A response not successful', [
                        'order_number' => $orderNumber,
                        'attempt' => $attempt,
                        'status' => $response->status(),
                        'error' => $error
                    ]);

                    // Coba endpoint alternatif jika endpoint utama gagal
                    if ($attempt === $maxRetries && $endpoint !== '/api/transaksi/uas-guaranteed') {
                        Log::info('🔄 Trying alternative endpoint...', ['order_number' => $orderNumber]);

                        // Coba endpoint alternatif
                        $altEndpoint = '/api/transaksi/uas-guaranteed';
                        $altData = $data;
                        $altData['is_fallback'] = true;

                        $altResponse = Http::timeout(20)
                            ->withHeaders([
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                                'X-Fallback-Mode' => 'true'
                            ])
                            ->post($this->laravelAUrl . $altEndpoint, $altData);

                        if ($altResponse->successful()) {
                            $altData = $altResponse->json();
                            return [
                                'success' => true,
                                'data' => $altData
                            ];
                        }
                    }

                    if ($attempt < $maxRetries) {
                        usleep($retryDelay * 1000);
                        $retryDelay *= 2;
                        continue;
                    }

                    return [
                        'success' => false,
                        'error' => $error
                    ];
                }

            } catch (\Exception $e) {
                $error = "Attempt {$attempt}: " . $e->getMessage();
                Log::error('❌ Laravel A request failed', [
                    'order_number' => $orderNumber,
                    'attempt' => $attempt,
                    'endpoint' => $endpoint,
                    'error' => $error
                ]);

                if ($attempt < $maxRetries) {
                    usleep($retryDelay * 1000);
                    $retryDelay *= 2;
                    continue;
                }

                return [
                    'success' => false,
                    'error' => $error
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'Max retries exceeded'
        ];
    }

    /**
     * Update order after successful sync
     */
    private function updateOrderAfterSync(Order $order, $responseData, $isActuallySaved = false)
    {
        try {
            $prefix = $isActuallySaved ? '✅' : '⚠️';
            $action = $isActuallySaved ? 'Saved to' : 'Simulated to';

            $note = "{$prefix} {$action} Laravel A at " . now()->format('Y-m-d H:i:s');
            $note .= " | Message: " . ($responseData['message'] ?? 'unknown');

            if ($isActuallySaved) {
                $note .= " | Transaction: " .
                        ($responseData['transaction_id'] ??
                         $responseData['transaction'] ??
                         $responseData['transaction_number'] ?? 'N/A');
            }

            $note .= " | Status: " . ($responseData['status'] ?? 'N/A');

            if (isset($responseData['simulation'])) {
                $note .= " | Simulation: " . ($responseData['simulation']['step_1']['action'] ?? 'simulated');
            }

            $currentNotes = $order->notes ?? '';
            if ($currentNotes) {
                $note = $currentNotes . "\n" . $note;
            }

            $updateData = [
                'notes' => $note,
                'updated_at' => now()
            ];

            if ($isActuallySaved) {
                $updateData['synced_to_a'] = true;
                $updateData['synced_at'] = now();
            }

            $order->update($updateData);

            Log::info('Order updated after sync to Laravel A', [
                'order_id' => $order->id,
                'saved_to_database' => $isActuallySaved,
                'transaction_number' => $responseData['transaction'] ?? $responseData['transaction_number'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to update order notes: ' . $e->getMessage());
        }
    }

    /**
     * Show sync results
     */
    private function showSyncResults($result, $totalTime)
    {
        $syncedCount = $result['synced_count'];
        $simulatedCount = $result['simulated_count'];
        $failedCount = $result['failed_count'];
        $total = $result['total'];

        if ($syncedCount > 0) {
            $this->syncStatus = "✅ Berhasil sync {$syncedCount} order ke database Laravel A";
            if ($simulatedCount > 0) {
                $this->syncStatus .= ", ⚠️ {$simulatedCount} simulated";
            }
            if ($failedCount > 0) {
                $this->syncStatus .= ", ❌ gagal {$failedCount}";
            }
        } elseif ($simulatedCount > 0) {
            $this->syncStatus = "⚠️ {$simulatedCount} order simulated ke Laravel A";
            if ($failedCount > 0) {
                $this->syncStatus .= ", ❌ gagal {$failedCount}";
            }
        } else {
            $this->syncStatus = "❌ Gagal sync semua order ({$failedCount} failed)";
        }

        $this->syncStatus .= " | ⏱️ Waktu: {$totalTime} detik";

        // Add summary to debug info
        $this->debugInfo .= "\n📊 SUMMARY:\n";
        $this->debugInfo .= "Total orders: {$total}\n";
        $this->debugInfo .= "Saved to database: {$syncedCount}\n";
        $this->debugInfo .= "Simulated only: {$simulatedCount}\n";
        $this->debugInfo .= "Failed: {$failedCount}\n";
        $this->debugInfo .= "Total time: {$totalTime}s\n";

        // Add results details
        if (!empty($result['results'])) {
            $this->debugInfo .= "\n📋 DETAILS:\n";
            foreach ($result['results'] as $item) {
                if ($item['status'] === 'success') {
                    $this->debugInfo .= "✅ {$item['order']}: {$item['message']}";
                    if (isset($item['transaction_id'])) {
                        $this->debugInfo .= " (ID: {$item['transaction_id']})";
                    }
                    $this->debugInfo .= "\n";
                } elseif ($item['status'] === 'simulated') {
                    $this->debugInfo .= "⚠️ {$item['order']}: {$item['message']}\n";
                } else {
                    $this->debugInfo .= "❌ {$item['order']}: {$item['error']}\n";
                }
            }
        }
    }

    /**
     * Check connection to Laravel A
     */
    public function checkLaravelAConnection()
    {
        try {
            $startTime = microtime(true);

            $response = Http::timeout(10)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($this->laravelAUrl . '/api/health');

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            if ($response->successful()) {
                $data = $response->json();

                $this->syncStatus = "✅ Connected to Laravel A";
                $this->debugInfo = "📊 Connection Details:\n";
                $this->debugInfo .= "Response Time: {$responseTime}ms\n";
                $this->debugInfo .= "Status: " . ($data['status'] ?? 'unknown') . "\n";
                $this->debugInfo .= "Message: " . ($data['message'] ?? 'N/A') . "\n";
                $this->debugInfo .= "Version: " . ($data['version'] ?? 'N/A') . "\n";
                $this->debugInfo .= "Timestamp: " . ($data['timestamp'] ?? 'N/A') . "\n";

                // Show available endpoints
                if (isset($data['available_endpoints'])) {
                    $this->debugInfo .= "\n🌐 Available Endpoints:\n";
                    foreach ($data['available_endpoints'] as $endpoint => $method) {
                        $this->debugInfo .= "{$method} {$endpoint}\n";
                    }
                }

                return true;
            } else {
                $this->syncStatus = '❌ Cannot connect to Laravel A (HTTP ' . $response->status() . ')';
                $this->debugInfo = "Response body: " . $response->body();
                return false;
            }

        } catch (\Exception $e) {
            $this->syncStatus = '❌ Connection error: ' . $e->getMessage();
            $this->debugInfo = "Exception: " . $e->getTraceAsString();
            return false;
        }
    }

    /**
     * Test sync dengan data dummy
     */
    public function testSync()
    {
        $this->isSyncing = true;
        $this->syncStatus = '🧪 Testing sync connection ke Laravel A...';
        $this->debugInfo = '';

        try {
            // First, check Laravel A connection
            $healthResponse = Http::timeout(10)
                ->get($this->laravelAUrl . '/api/health');

            if (!$healthResponse->successful()) {
                $this->syncStatus = '❌ Laravel A tidak dapat diakses';
                $this->debugInfo = "Health check failed: HTTP " . $healthResponse->status();
                $this->isSyncing = false;
                return;
            }

            // Create test data
            $testData = [
                'transaction_number' => 'TEST-SYNC-' . time(),
                'order_number' => 'TEST-' . time(),
                'customer_name' => 'Test Customer UAS',
                'customer_email' => 'test@uas.demo',
                'product_name' => 'Test Product UAS',
                'amount' => 500000,
                'original_amount' => 500000,
                'discount_amount' => 0,
                'status' => 'pending',
                'payment_type' => 'bank_transfer',
                'notes' => 'Test sync from Laravel B',
                'created_at' => now()->format('Y-m-d H:i:s'),
                'source_system' => 'laravel_b',
                'sync_id' => 'test-' . uniqid(),
                'demo_mode' => true,
                'simulate_integration' => true,
                'is_test' => true
            ];

            $this->debugInfo = "🎯 Test Data:\n" . json_encode($testData, JSON_PRETTY_PRINT);

            // Send test request
            $endpoint = '/api/transaksi/uas-simulation';
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Test-Mode' => 'true',
                    'X-Source-System' => 'laravel_b_test'
                ])
                ->post($this->laravelAUrl . $endpoint, $testData);

            if ($response->successful()) {
                $responseData = $response->json();
                $this->syncStatus = '✅ Test sync ke Laravel A berhasil!';
                $this->debugInfo .= "\n\n🎉 Response dari Laravel A:\n" . json_encode($responseData, JSON_PRETTY_PRINT);

                $this->debugInfo .= "\n\n📋 Result Summary:\n";
                $this->debugInfo .= "Message: " . ($responseData['message'] ?? 'Success') . "\n";
                $this->debugInfo .= "Transaction: " . ($responseData['transaction'] ?? $responseData['transaction_number'] ?? 'N/A') . "\n";

                if (isset($responseData['simulation'])) {
                    $this->debugInfo .= "\n🔄 Simulation Flow:\n";
                    foreach ($responseData['simulation'] as $step => $details) {
                        $this->debugInfo .= "{$step}: {$details['action']} → {$details['status']}\n";
                    }
                }

            } else {
                $this->syncStatus = '❌ Test sync gagal';
                $this->debugInfo .= "\n\n❌ Error Response:\n" . $response->body();

                // Coba dengan endpoint lain
                $this->debugInfo .= "\n\n🔄 Trying alternative endpoint...\n";

                $altResponse = Http::timeout(30)
                    ->post($this->laravelAUrl . '/api/transaksi/uas-guaranteed', $testData);

                if ($altResponse->successful()) {
                    $altData = $altResponse->json();
                    $this->syncStatus = '✅ Test sync berhasil dengan alternative endpoint!';
                    $this->debugInfo .= "\n🎉 Alternative Response:\n" . json_encode($altData, JSON_PRETTY_PRINT);
                }
            }

        } catch (\Exception $e) {
            $this->syncStatus = '❌ Test error: ' . $e->getMessage();
            $this->debugInfo = "Exception:\n" . $e->getTraceAsString();
        }

        $this->isSyncing = false;
    }

    /**
     * Test API endpoint khusus
     */
    public function testApiEndpoint()
    {
        try {
            $this->syncStatus = '🧪 Testing API endpoint spesifik...';

            // Test simulation endpoint
            $response = Http::timeout(10)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($this->laravelAUrl . '/api/transaksi/uas-simulation');

            if ($response->successful()) {
                $data = $response->json();
                $this->syncStatus = '✅ API endpoint test successful';
                $this->debugInfo = json_encode($data, JSON_PRETTY_PRINT);
            } else {
                $this->syncStatus = '❌ API endpoint test failed';
                $this->debugInfo = "Status: " . $response->status() . "\nResponse: " . $response->body();

                // Coba test dengan POST
                $this->debugInfo .= "\n\n🔄 Trying POST request...\n";
                $postData = [
                    'test' => true,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ];

                $postResponse = Http::timeout(10)
                    ->post($this->laravelAUrl . '/api/transaksi/uas-guaranteed', $postData);

                if ($postResponse->successful()) {
                    $postData = $postResponse->json();
                    $this->syncStatus = '✅ POST test successful';
                    $this->debugInfo .= "\n🎉 POST Response:\n" . json_encode($postData, JSON_PRETTY_PRINT);
                }
            }
        } catch (\Exception $e) {
            $this->syncStatus = '❌ API test error: ' . $e->getMessage();
            $this->debugInfo = $e->getTraceAsString();
        }
    }

    /**
     * Select/Deselect all orders
     */
    public function selectAll()
    {
        $orderIds = Order::whereNull('deleted_at')
            ->pluck('id')
            ->toArray();

        if (count($this->selectedOrders) === count($orderIds)) {
            $this->selectedOrders = [];
        } else {
            $this->selectedOrders = $orderIds;
        }
    }

    /**
     * Get order details for modal
     */
    public function getOrderDetails($orderId)
    {
        $this->selectedOrder = Order::with(['product', 'user', 'voucher', 'transaction'])
            ->find($orderId);

        $this->selectedTransaction = $this->selectedOrder?->transaction;
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->reset('selectedOrder', 'selectedTransaction');
    }

    /**
     * Reset page when search or filter changes
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Get total orders count
     */
    public function getTotalOrdersProperty()
    {
        return Order::whereNull('deleted_at')->count();
    }

    /**
     * Render component
     */
    public function render()
    {
        $query = Order::query()
            ->with(['product', 'user', 'transaction'])
            ->whereNull('deleted_at')
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('product', function ($prodQuery) {
                        $prodQuery->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('transaction', function ($trxQuery) {
                        $trxQuery->where('transaction_number', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->paginate($this->perPage);

        return view('livewire.admin.manajemen-pesanan', [
            'orders' => $orders,
            'totalOrders' => $this->totalOrders,
        ]);
    }
    /**
 * SIMPLE SYNC - Kirim data ke Laravel A tanpa kompleksitas
 */
/**
 * SIMPLE & WORKING SYNC dari B ke A
 */


/**
 * Sync Selected Orders - VERSI SIMPLE YANG PASTI BEKERJA
 */
/**
 * Test with EXACT same data as curl test
 */
public function testWithCurlFormat()
{
    \Log::info('🔧 [B] testWithCurlFormat called');

    $this->syncStatus = '🔧 Testing with curl format...';
    $this->debugInfo = "=== CURL FORMAT TEST ===\n\n";

    try {
        // Data SAMA PERSIS dengan curl test yang berhasil
        $testData = [
            'order_number' => 'CURL-TEST-' . time(),
            'customer_name' => 'Curl Test User',
            'customer_email' => 'curltest@example.com',
            'product_name' => 'Curl Test Product',
            'final_price' => 75000,
            'status' => 'pending',
            'payment_reference' => 'PAY-CURL-TEST-' . time()
        ];

        $this->debugInfo .= "1. Sending test data (same as curl):\n";
        $this->debugInfo .= json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

        \Log::info('📤 [B] Curl format test data:', $testData);

        $response = Http::timeout(10)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
            ->post('http://localhost:8000/api/transaksi/receive-from-b', $testData);

        $this->debugInfo .= "2. Response from Laravel A:\n";
        $this->debugInfo .= "   Status: HTTP " . $response->status() . "\n";

        if ($response->successful()) {
            $responseData = $response->json();
            $this->debugInfo .= "   ✅ Success!\n";
            $this->debugInfo .= "   Message: " . ($responseData['message'] ?? 'N/A') . "\n";
            $this->debugInfo .= "   Transaction ID: " . ($responseData['transaction_id'] ?? 'N/A') . "\n";
            $this->debugInfo .= "   Action: " . ($responseData['action'] ?? 'N/A') . "\n";

            $this->syncStatus = '✅ Curl format test successful!';

            // Suggest next step
            $this->debugInfo .= "\n3. Next steps:\n";
            $this->debugInfo .= "   • Go to Laravel A (Approve Transaksi)\n";
            $this->debugInfo .= "   • Look for transaction: " . ($responseData['transaction_id'] ?? 'B-CURL-TEST') . "\n";
            $this->debugInfo .= "   • It should appear in the table\n";

        } else {
            $this->debugInfo .= "   ❌ Failed!\n";
            $this->debugInfo .= "   Error: " . $response->body() . "\n";
            $this->syncStatus = '❌ Curl format test failed';
        }

    } catch (\Exception $e) {
        $this->debugInfo .= "💥 Exception: " . $e->getMessage() . "\n";
        $this->syncStatus = '💥 Test error: ' . $e->getMessage();
        \Log::error('💥 [B] Curl test error: ' . $e->getMessage());
    }
}

/**
 * Test with real order using curl format
 */
public function testRealOrderSync()
{
    $this->syncStatus = '🔧 Testing real order sync...';
    $this->debugInfo = "=== REAL ORDER TEST ===\n\n";

    $order = Order::first();

    if (!$order) {
        $this->debugInfo .= "❌ No orders found in database\n";
        $this->syncStatus = '❌ No orders to test';
        return;
    }

    $this->debugInfo .= "1. Using order: {$order->order_number}\n";
    $this->debugInfo .= "   Customer: {$order->customer_name}\n";
    $this->debugInfo .= "   Amount: Rp " . number_format($order->final_price) . "\n\n";

    // Format SAMA dengan curl
    $data = [
        'order_number' => $order->order_number,
        'customer_name' => $order->customer_name,
        'customer_email' => $order->customer_email ?? 'email@example.com',
        'product_name' => $order->product?->name ?? 'Product',
        'final_price' => floatval($order->final_price),
        'status' => $order->status,
        'payment_reference' => $order->order_number
    ];

    $this->debugInfo .= "2. Sending data (curl format):\n";
    $this->debugInfo .= json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

    try {
        $response = Http::timeout(10)
            ->post('http://localhost:8000/api/transaksi/receive-from-b', $data);

        $this->debugInfo .= "3. Response:\n";
        $this->debugInfo .= "   Status: HTTP " . $response->status() . "\n";

        if ($response->successful()) {
            $responseData = $response->json();
            $this->debugInfo .= "   ✅ Success!\n";
            $this->debugInfo .= "   Transaction ID: " . ($responseData['transaction_id'] ?? 'N/A') . "\n";

            $this->syncStatus = '✅ Real order test successful!';

            // Update the order
            $order->update([
                'synced_to_a' => true,
                'synced_at' => now(),
                'sync_transaction_id' => $responseData['transaction_id'] ?? null
            ]);

        } else {
            $this->debugInfo .= "   ❌ Failed: " . $response->body() . "\n";
            $this->syncStatus = '❌ Real order test failed';
        }

    } catch (\Exception $e) {
        $this->debugInfo .= "💥 Exception: " . $e->getMessage() . "\n";
        $this->syncStatus = '💥 Test error';
    }
}
/**
 * Sync Selected dengan cara SIMPLE
 */
/**
 * SYNC KE LARAVEL A - VERSI YANG PASTI BEKERJA
 * Menggunakan endpoint dan format SAMA PERSIS dengan curl test
 */
public function syncToLaravelA($orderIds)
{
    \Log::info('🎯 [B] syncToLaravelA START', ['count' => count($orderIds)]);

    $results = [];

    foreach ($orderIds as $orderId) {
        try {
            // 1. Get order data
            $order = Order::with(['product'])->find($orderId);

            if (!$order) {
                $results[] = ['order_id' => $orderId, 'status' => 'error', 'error' => 'Order not found'];
                continue;
            }

            \Log::info('📦 [B] Processing: ' . $order->order_number);

            // 2. Prepare data - SAMA PERSIS dengan curl test
            $data = [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name ?? 'Customer',
                'customer_email' => $order->customer_email ?? 'customer@example.com',
                'product_name' => $order->product?->name ?? 'Product',
                'final_price' => floatval($order->final_price),
                'status' => $order->status,
                'payment_reference' => $order->order_number, // Format: PAY-{order_number}
            ];

            // Optional fields (jika ada)
            if ($order->customer_phone) {
                $data['customer_phone'] = $order->customer_phone;
            }

            if ($order->transaction?->payment_type) {
                $data['payment_type'] = $order->transaction->payment_type;
            }

            if ($order->created_at) {
                $data['created_at'] = $order->created_at->format('Y-m-d H:i:s');
            }

            \Log::info('📤 [B] Sending data (same as curl):', $data);

            // 3. Send to Laravel A - ENDPOINT SAMA PERSIS
            $laravelAUrl = 'http://localhost:8000';
            $endpoint = '/api/transaksi/receive-from-b';
            $fullUrl = $laravelAUrl . $endpoint;

            \Log::info('🌐 [B] POST to: ' . $fullUrl);

            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($fullUrl, $data);

            \Log::info('📥 [B] Response status: ' . $response->status());

            if ($response->successful()) {
                $responseData = $response->json();
                \Log::info('✅ [B] Success response:', $responseData);

                $results[] = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => 'success',
                    'message' => $responseData['message'] ?? 'Success',
                    'transaction_id' => $responseData['transaction_id'] ?? null,
                    'action' => $responseData['action'] ?? 'created'
                ];

                // Update order notes
                $order->update([
                    'synced_to_a' => true,
                    'synced_at' => now(),
                    'sync_status' => 'success',
                    'sync_transaction_id' => $responseData['transaction_id'] ?? null,
                    'notes' => ($order->notes ?? '') . "\n✅ Synced to Laravel A: " .
                              ($responseData['transaction_id'] ?? 'N/A') .
                              " at " . now()->format('Y-m-d H:i:s')
                ]);

            } else {
                $error = 'HTTP ' . $response->status() . ': ' . $response->body();
                \Log::error('❌ [B] Failed: ' . $error);

                $results[] = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => 'failed',
                    'error' => $error
                ];

                $order->update([
                    'sync_status' => 'failed',
                    'notes' => ($order->notes ?? '') . "\n❌ Sync failed: " . $error
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('💥 [B] Exception: ' . $e->getMessage());

            $results[] = [
                'order_id' => $orderId,
                'status' => 'exception',
                'error' => $e->getMessage()
            ];
        }

        // Small delay between requests
        usleep(50000); // 0.05 seconds
    }

    $successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
    \Log::info('📊 [B] Sync completed. Success: ' . $successCount . '/' . count($orderIds));

    return $results;
}

/**
 * Sync selected orders - SIMPLE VERSION
 */
public function syncSelectedToLaravelA()
{
    $this->isSyncing = true;
    $this->syncStatus = '🔄 Starting sync to Laravel A...';
    $this->debugInfo = "=== SYNC LOG ===\n" . now()->format('Y-m-d H:i:s') . "\n\n";

    \Log::info('🔄 [B] UI: syncSelectedToLaravelA called', [
        'selected_count' => count($this->selectedOrders)
    ]);

    try {
        if (empty($this->selectedOrders)) {
            $this->syncStatus = '❌ Please select orders first!';
            $this->isSyncing = false;
            return;
        }

        // Show what we're syncing
        $orders = Order::whereIn('id', $this->selectedOrders)
            ->get(['id', 'order_number', 'customer_name', 'final_price']);

        $this->debugInfo .= "Selected orders:\n";
        foreach ($orders as $order) {
            $this->debugInfo .= "  • {$order->order_number}: {$order->customer_name} (Rp " .
                               number_format($order->final_price) . ")\n";
        }
        $this->debugInfo .= "\n";

        // Execute sync
        $results = $this->syncToLaravelA($this->selectedOrders);

        // Process results
        $successCount = 0;
        $failCount = 0;

        $this->debugInfo .= "Results:\n";
        foreach ($results as $result) {
            if ($result['status'] === 'success') {
                $successCount++;
                $this->debugInfo .= "✅ {$result['order_number']}: {$result['message']}\n";
                if ($result['transaction_id']) {
                    $this->debugInfo .= "   → Transaction ID: {$result['transaction_id']}\n";
                }
            } else {
                $failCount++;
                $this->debugInfo .= "❌ {$result['order_number'] }: {$result['error']}\n";
            }
        }

        // Update status
        if ($successCount > 0) {
            $this->syncStatus = "✅ Successfully synced {$successCount} order(s) to Laravel A";
            if ($failCount > 0) {
                $this->syncStatus .= ", {$failCount} failed";
            }

            // Suggest user to check Laravel A
            $this->debugInfo .= "\n💡 Please check Laravel A (Approve Transaksi) to see the synced transactions.\n";
            $this->debugInfo .= "   Look for transactions starting with 'B-'\n";

        } else {
            $this->syncStatus = "❌ Failed to sync all orders";
        }

        // Reset selection
        $this->selectedOrders = [];

        // Add completion time
        $this->debugInfo .= "\nSync completed at: " . now()->format('Y-m-d H:i:s');

    } catch (\Exception $e) {
        $this->syncStatus = '💥 Sync error: ' . $e->getMessage();
        $this->debugInfo .= "\n💥 Exception:\n" . $e->getTraceAsString();
        \Log::error('💥 [B] UI sync error: ' . $e->getMessage());
    }

    $this->isSyncing = false;
}
}
