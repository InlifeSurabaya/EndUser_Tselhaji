<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RoamingCsvAnalysis extends Command
{
    protected $signature = 'roaming:csv
                            {file : Path to CSV file}
                            {--package_keyword= : Package keyword filter}
                            {--id_pelanggan= : ID Pelanggan filter}
                            {--package_name= : Package name filter}
                            {--grouping= : Grouping filter}
                            {--clusters=5 : Number of clusters (default: 5)}
                            {--has-headers : CSV has headers (default: auto-detect)}
                            {--recommend : Show personalized recommendations}
                            {--reminder : Show reminder messages}
                            {--sample=5 : Show sample data count (default: 5)}
                            {--no-debug : Disable debug output}
                            {--simple : Simple output - only 3 main sections}';

    protected $description = 'Analyze roaming CSV data with K-Means and Random Forest simulation';

    private $headers = [];
    private $hasHeaders = false;
    private $packageNames = [];
    private $totalDataLoaded = 0;
    private $executionStartTime;
    private $debugMode = true;
    private $simpleMode = false;

    public function handle()
    {
        $this->executionStartTime = microtime(true);
        $this->debugMode = !$this->option('no-debug');
        $this->simpleMode = $this->option('simple');

        $filepath = $this->argument('file');

        if (!$this->simpleMode) {
            $this->info("📊 ROAMING DATA ANALYSIS - CUSTOMER PURCHASE PATTERNS");
            $this->line(str_repeat("=", 80));
        }

        if (!file_exists($filepath)) {
            $this->error("❌ File not found: $filepath");
            return 1;
        }

        // Read CSV with header detection
        $data = $this->readCSVWithHeaders($filepath);

        if (empty($data)) {
            $this->error("❌ No data found in CSV file");
            $this->showFilePreview($filepath);
            return 1;
        }

        $this->totalDataLoaded = count($data);

        if (!$this->simpleMode) {
            $this->info("✅ Data loaded: " . $this->totalDataLoaded . " records");

            if ($this->hasHeaders) {
                $this->info("📝 Headers detected: " . implode(", ", array_slice($this->headers, 0, 8)) . "...");
            }

            // Tampilkan data structure analysis
            $this->showDataStructure($data);
        }

        // Filter data if options provided
        $filteredData = $this->filterData($data);

        // Tampilkan informasi filter yang diterapkan
        if (!$this->simpleMode) {
            $this->showAppliedFilters();
        }

        if (!empty($filteredData) && (count($filteredData) < count($data))) {
            if (!$this->simpleMode) {
                $this->info("🔍 Filtered records: " . count($filteredData) .
                           " (from " . count($data) . " total)");
            }
            $this->analyzeData($filteredData);
        } else {
            if (!$this->simpleMode) {
                $this->info("📋 Analyzing all data (" . count($data) . " records):");
            }
            $this->analyzeData($data);
        }

        // Generate dummy transactions and recommendations (only if not filtered)
        if (!$this->simpleMode && count($filteredData) === count($data)) {
            $this->generateDummyTransactions($data);
        }

        // Show personalized recommendations if option is set
        if ($this->option('recommend')) {
            $this->showPersonalizedRecommendations($data);
        }

        // Show reminder messages if option is set
        if ($this->option('reminder')) {
            $this->showReminderMessages($data);
        }

        if (!$this->simpleMode) {
            // Show execution summary
            $this->showExecutionSummary();
            $this->info("\n🎉 Analysis completed!");
        }

        return 0;
    }

    private function readCSVWithHeaders($filepath)
    {
        $data = [];
        $lineNumber = 0;

        if (($handle = fopen($filepath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 10000, ',')) !== false) {
                $lineNumber++;

                // Skip empty rows
                if (empty($row) || (count($row) === 1 && trim($row[0]) === '')) {
                    continue;
                }

                // Check if first row is header (ONLY ONCE)
                if ($lineNumber === 1) {
                    $this->hasHeaders = $this->detectHeaders($row);

                    if ($this->hasHeaders) {
                        $this->headers = $row;
                        $this->mapHeadersToStandardFormat();
                        continue; // Skip header row
                    }
                }

                // Process data row
                $processedRow = $this->processDataRow($row, $lineNumber);
                if ($processedRow) {
                    $data[] = $processedRow;

                    // Store package names for reference
                    if (!empty($processedRow['package_name'])) {
                        $this->packageNames[$processedRow['package_keyword']] = $processedRow['package_name'];
                    }
                }
            }
            fclose($handle);
        }

        return $data;
    }

    private function detectHeaders($row)
    {
        $firstCell = strtolower(trim($row[0] ?? ''));

        $headerPatterns = [
            'a.event_date', 'event_date', 'date', 'tanggal',
            'c.area', 'area', 'regional', 'wilayah',
            'id_pelanggan', 'customer_id', 'msisdn',
            'a.package_keyword', 'package_keyword', 'package_code',
            'b.grouping', 'grouping', 'package_name', 'nama_paket',
            'b.day', 'day', 'durasi',
            'b.quota_bns_mb', 'quota_bns_mb', 'quota',
            'rev', 'revenue', 'harga',
            'trx', 'transaction',
            'subs', 'subscription'
        ];

        foreach ($headerPatterns as $pattern) {
            if (str_contains($firstCell, $pattern)) {
                return true;
            }
        }

        return !is_numeric($firstCell) || strlen($firstCell) != 4;
    }

    private function mapHeadersToStandardFormat()
    {
        $headerMap = [
            'a.event_date' => 'event_date',
            'event_date' => 'event_date',
            'c.area' => 'area',
            'area' => 'area',
            'c.regional' => 'regional',
            'regional' => 'regional',
            'c.cluster' => 'cluster',
            'cluster' => 'cluster',
            'c.kabupaten' => 'kabupaten',
            'kabupaten' => 'kabupaten',
            'id_pelanggan' => 'id_pelanggan',
            'customer_id' => 'id_pelanggan',
            'msisdn' => 'id_pelanggan',
            'a.package_keyword' => 'package_keyword',
            'package_keyword' => 'package_keyword',
            'package_code' => 'package_keyword',
            'package_name' => 'package_name',
            'nama_paket' => 'package_name',
            'b.day' => 'day',
            'day' => 'day',
            'durasi' => 'day',
            'b.quota_bns_mb' => 'quota_bns_mb',
            'quota_bns_mb' => 'quota_bns_mb',
            'quota' => 'quota_bns_mb',
            'rev' => 'rev',
            'revenue' => 'rev',
            'harga' => 'rev',
            'trx' => 'trx',
            'transaction' => 'trx',
            'subs' => 'subs',
            'subscription' => 'subs',
        ];

        $normalizedHeaders = [];
        foreach ($this->headers as $index => $header) {
            $header = trim($header);
            $normalizedHeaders[$index] = $headerMap[$header] ?? $header;
        }

        $this->headers = $normalizedHeaders;

        if ($this->debugMode && !$this->simpleMode) {
            $this->info("🔍 HEADER MAPPING:");
            foreach ($this->headers as $index => $header) {
                $original = $this->headers[$index] ?? 'N/A';
                $this->line("  [$index] '{$original}' → '{$header}'");
            }
        }
    }

    private function processDataRow($row, $lineNumber)
    {
        if ($this->hasHeaders && !empty($this->headers)) {
            $item = [];

            foreach ($this->headers as $index => $headerName) {
                if (isset($row[$index])) {
                    $item[$headerName] = trim($row[$index]);
                } else {
                    $item[$headerName] = '';
                }
            }

            if ($this->debugMode && !$this->simpleMode && $lineNumber <= 3) {
                $this->line("DEBUG Row $lineNumber processing:");
                $this->line("  Raw row: " . implode(", ", array_slice($row, 0, 10)));
                $this->line("  Mapped item keys: " . implode(", ", array_keys($item)));
                if (isset($item['package_name'])) {
                    $this->line("  package_name found: " . $item['package_name']);
                }
            }

            return $this->convertToStandardFormat($item);
        }

        if (count($row) >= 13) {
            return [
                'event_date' => intval($row[0] ?? 0),
                'area' => trim($row[1] ?? ''),
                'regional' => trim($row[2] ?? ''),
                'cluster' => trim($row[3] ?? ''),
                'kabupaten' => trim($row[4] ?? ''),
                'id_pelanggan' => (string) ($row[5] ?? ''),
                'package_keyword' => intval($row[6] ?? 0),
                'package_name' => trim($row[7] ?? ''),
                'day' => intval($row[8] ?? 0),
                'quota_bns_mb' => intval($row[9] ?? 0),
                'rev' => floatval($row[10] ?? 0),
                'trx' => intval($row[11] ?? 0),
                'subs' => intval($row[12] ?? 0),
                'grouping' => 0,
            ];
        } elseif (count($row) >= 8) {
            return [
                'event_date' => intval($row[0] ?? 0),
                'area' => trim($row[1] ?? ''),
                'regional' => trim($row[2] ?? ''),
                'cluster' => trim($row[3] ?? ''),
                'kabupaten' => trim($row[4] ?? ''),
                'id_pelanggan' => (string) ($row[5] ?? ''),
                'package_keyword' => intval($row[6] ?? 0),
                'package_name' => trim($row[7] ?? ''),
                'day' => isset($row[8]) ? intval($row[8]) : 0,
                'quota_bns_mb' => isset($row[9]) ? intval($row[9]) : 0,
                'rev' => isset($row[10]) ? floatval($row[10]) : 0,
                'trx' => isset($row[11]) ? intval($row[11]) : 0,
                'subs' => isset($row[12]) ? intval($row[12]) : 0,
                'grouping' => 0,
            ];
        } else {
            if ($this->debugMode && !$this->simpleMode) {
                $this->warn("⚠️ Row $lineNumber has only " . count($row) . " columns (expected 13+)");
            }
            return null;
        }
    }

    private function convertToStandardFormat($item)
    {
        $packageName = $item['package_name'] ?? '';

        if (empty($packageName) && isset($item['package_keyword'])) {
            $packageName = $this->getPackageNameFromKeyword($item['package_keyword']);
        }

        return [
            'event_date' => intval($item['event_date'] ?? 0),
            'area' => $item['area'] ?? '',
            'regional' => $item['regional'] ?? '',
            'cluster' => $item['cluster'] ?? '',
            'kabupaten' => $item['kabupaten'] ?? '',
            'id_pelanggan' => (string) ($item['id_pelanggan'] ?? ''),
            'package_keyword' => intval($item['package_keyword'] ?? 0),
            'package_name' => $packageName,
            'grouping' => intval($item['grouping'] ?? 0),
            'day' => intval($item['day'] ?? 0),
            'quota_bns_mb' => intval($item['quota_bns_mb'] ?? 0),
            'rev' => floatval($item['rev'] ?? 0),
            'trx' => intval($item['trx'] ?? 0),
            'subs' => intval($item['subs'] ?? 0),
        ];
    }

    private function getPackageNameFromKeyword($keyword)
    {
        $packageMap = [
            69339 => 'SAUDI ARABIA (UMROH)',
            60034 => 'ROAMax CHINA',
            60053 => 'ROAMax EROPA',
            24071 => 'SAUDI ARABIA (UMROH)',
            58776 => 'ROAMING ASIA',
            58775 => 'ROAMING EROPA',
            58779 => 'ROAMING USA',
            69340 => 'ROAMING MIDDLE EAST',
        ];

        return $packageMap[$keyword] ?? 'Package-' . $keyword;
    }

    private function showAppliedFilters()
    {
        $appliedFilters = [];

        if ($this->option('package_keyword')) {
            $appliedFilters[] = "Package Keyword: " . $this->option('package_keyword');
        }

        if ($this->option('id_pelanggan')) {
            $appliedFilters[] = "ID Pelanggan: " . $this->option('id_pelanggan');
        }

        if ($this->option('package_name')) {
            $appliedFilters[] = "Package Name: " . $this->option('package_name');
        }

        if ($this->option('grouping')) {
            $appliedFilters[] = "Grouping: " . $this->option('grouping');
        }

        if (!empty($appliedFilters)) {
            $this->info("🎯 Applied Filters: " . implode(", ", $appliedFilters));
        }
    }

    private function filterData($data)
    {
        $filters = [];

        $packageKeywordFilter = $this->option('package_keyword');
        $idFilter = $this->option('id_pelanggan');
        $packageNameFilter = $this->option('package_name');
        $groupingFilter = $this->option('grouping');

        if (!$packageKeywordFilter && !$idFilter && !$packageNameFilter && !$groupingFilter) {
            return $data;
        }

        if ($packageKeywordFilter) {
            $filters[] = function($item) use ($packageKeywordFilter) {
                return $item['package_keyword'] == $packageKeywordFilter;
            };
        }

        if ($idFilter) {
            $filters[] = function($item) use ($idFilter) {
                return $item['id_pelanggan'] == $idFilter;
            };
        }

        if ($packageNameFilter) {
            $filters[] = function($item) use ($packageNameFilter) {
                return stripos($item['package_name'] ?? '', $packageNameFilter) !== false;
            };
        }

        if ($groupingFilter) {
            $filters[] = function($item) use ($groupingFilter) {
                return $item['grouping'] == $groupingFilter;
            };
        }

        return array_filter($data, function($item) use ($filters) {
            foreach ($filters as $filter) {
                if (!$filter($item)) {
                    return false;
                }
            }
            return true;
        });
    }

    private function analyzeData($data)
    {
        // Jika simple mode, langsung ke 3 step utama
        if ($this->simpleMode) {
            // 1. K-Means clustering
            $this->kMeansAnalysis($data);

            // 2. Personalized recommendations (jika option diaktifkan)
            if ($this->option('recommend')) {
                $this->showPersonalizedRecommendations($data);
            }

            // 3. Reminder messages (jika option diaktifkan)
            if ($this->option('reminder')) {
                $this->showReminderMessages($data);
            }
            return;
        }

        // Mode normal (tampilkan semua)
        $sampleCount = (int)$this->option('sample');
        $this->showSampleData($data, $sampleCount);

        $this->showStatistics($data);

        $this->kMeansAnalysis($data);

        $this->randomForestAnalysis($data);

        $this->showColumnMapping();
    }

    private function kMeansAnalysis($data)
    {
        $clusters = (int) $this->option('clusters');

        if ($this->simpleMode) {
            $this->info("\n🎯 1. K-MEANS CLUSTERING ANALYSIS");
            $this->line(str_repeat("=", 60));
        } else {
            $this->info("\n🎯 K-MEANS CLUSTERING - PURCHASE FREQUENCY PATTERNS ($clusters clusters):");
            $this->line(str_repeat("-", 80));
        }

        // Group data by customer
        $customerData = [];
        foreach ($data as $item) {
            $customerId = $item['id_pelanggan'];

            if (!isset($customerData[$customerId])) {
                $customerData[$customerId] = [
                    'id' => $customerId,
                    'purchase_count' => 0,
                    'total_revenue' => 0,
                    'avg_revenue' => 0,
                    'total_quota' => 0,
                    'avg_quota' => 0,
                    'total_days' => 0,
                    'avg_days' => 0,
                    'transactions' => 0,
                    'first_date' => $item['event_date'],
                    'last_date' => $item['event_date'],
                    'packages' => [],
                    'package_names' => [],
                    'regions' => [],
                    'areas' => []
                ];
            }

            $customerData[$customerId]['purchase_count'] += $item['trx'];
            $customerData[$customerId]['total_revenue'] += $item['rev'];
            $customerData[$customerId]['total_quota'] += $item['quota_bns_mb'];
            $customerData[$customerId]['total_days'] += $item['day'];
            $customerData[$customerId]['transactions']++;

            if ($item['event_date'] < $customerData[$customerId]['first_date']) {
                $customerData[$customerId]['first_date'] = $item['event_date'];
            }
            if ($item['event_date'] > $customerData[$customerId]['last_date']) {
                $customerData[$customerId]['last_date'] = $item['event_date'];
            }

            if (!in_array($item['package_keyword'], $customerData[$customerId]['packages'])) {
                $customerData[$customerId]['packages'][] = $item['package_keyword'];
            }

            if (!empty($item['package_name']) && !in_array($item['package_name'], $customerData[$customerId]['package_names'])) {
                $customerData[$customerId]['package_names'][] = $item['package_name'];
                $this->packageNames[$item['package_keyword']] = $item['package_name'];
            }

            if (!in_array($item['regional'], $customerData[$customerId]['regions'])) {
                $customerData[$customerId]['regions'][] = $item['regional'];
            }
            if (!in_array($item['area'], $customerData[$customerId]['areas'])) {
                $customerData[$customerId]['areas'][] = $item['area'];
            }
        }

        foreach ($customerData as $customerId => $customer) {
            $customerData[$customerId]['avg_revenue'] = $customer['total_revenue'] / $customer['transactions'];
            $customerData[$customerId]['avg_quota'] = $customer['total_quota'] / $customer['transactions'];
            $customerData[$customerId]['avg_days'] = $customer['total_days'] / $customer['transactions'];
            $customerData[$customerId]['customer_lifetime'] = $customer['last_date'] - $customer['first_date'];
            $customerData[$customerId]['unique_packages'] = count($customer['packages']);
        }

        $purchaseFrequencies = array_column($customerData, 'purchase_count');
        sort($purchaseFrequencies);

        $clusterRanges = [];
        if ($clusters >= 5) {
            $clusterRanges = [
                ['min' => 1, 'max' => 1, 'label' => 'One-Time Buyers'],
                ['min' => 2, 'max' => 3, 'label' => 'Occasional Buyers'],
                ['min' => 4, 'max' => 6, 'label' => 'Regular Buyers'],
                ['min' => 7, 'max' => 10, 'label' => 'Frequent Buyers'],
                ['min' => 11, 'max' => 9999, 'label' => 'Loyal Buyers']
            ];
        } else {
            $step = floor((max($purchaseFrequencies) - min($purchaseFrequencies)) / $clusters);
            for ($i = 0; $i < $clusters; $i++) {
                $min = $i * $step + 1;
                $max = ($i + 1) * $step;
                $clusterRanges[] = ['min' => $min, 'max' => $max, 'label' => "Cluster " . ($i + 1)];
            }
        }

        $clusteredData = [];
        foreach ($clusterRanges as $clusterIndex => $range) {
            $clusteredData[$clusterIndex] = [
                'label' => $range['label'],
                'customers' => [],
                'stats' => []
            ];

            foreach ($customerData as $customerId => $customer) {
                if ($customer['purchase_count'] >= $range['min'] && $customer['purchase_count'] <= $range['max']) {
                    $clusteredData[$clusterIndex]['customers'][$customerId] = $customer;
                }
            }

            $customers = $clusteredData[$clusterIndex]['customers'];
            if (!empty($customers)) {
                $clusteredData[$clusterIndex]['stats'] = [
                    'count' => count($customers),
                    'avg_purchases' => array_sum(array_column($customers, 'purchase_count')) / count($customers),
                    'avg_revenue' => array_sum(array_column($customers, 'avg_revenue')) / count($customers),
                    'avg_quota' => array_sum(array_column($customers, 'avg_quota')) / count($customers),
                    'avg_unique_packages' => array_sum(array_column($customers, 'unique_packages')) / count($customers),
                    'avg_lifetime' => array_sum(array_column($customers, 'customer_lifetime')) / count($customers)
                ];
            }
        }

        $tableData = [];
        $totalCustomers = count($customerData);

        foreach ($clusteredData as $clusterIndex => $cluster) {
            if (!empty($cluster['customers'])) {
                $stats = $cluster['stats'];
                $percentage = ($stats['count'] / $totalCustomers) * 100;

                $tableData[] = [
                    $cluster['label'],
                    number_format($stats['count']),
                    round($percentage, 1) . '%',
                    number_format($stats['avg_purchases'], 1) . 'x',
                    number_format($stats['avg_unique_packages'], 1),
                    'Rp ' . number_format($stats['avg_revenue']),
                    number_format($stats['avg_quota']) . ' MB',
                    number_format($stats['avg_lifetime']) . ' days'
                ];
            }
        }

        if ($this->simpleMode) {
            // Tampilkan tabel sederhana
            $this->table(
                ['Segment', 'Customers', '%', 'Avg Purchases', 'Unique Pkgs', 'Avg Revenue', 'Avg Quota', 'Avg Lifetime'],
                $tableData
            );

            // Tampilkan insight singkat
            $this->info("\n💡 KEY INSIGHTS:");
            foreach ($clusteredData as $clusterIndex => $cluster) {
                if (!empty($cluster['customers'])) {
                    $stats = $cluster['stats'];
                    $percentage = round(($stats['count'] / $totalCustomers) * 100, 1);

                    $this->line("• {$cluster['label']}: {$stats['count']} customers ({$percentage}%)");
                    $this->line("  Avg purchases: {$stats['avg_purchases']}x, Revenue: Rp " . number_format($stats['avg_revenue']));
                }
            }
        } else {
            $this->table(
                ['Segment', 'Customers', '%', 'Avg Purchases', 'Unique Pkgs', 'Avg Revenue', 'Avg Quota', 'Avg Lifetime'],
                $tableData
            );

            // Show detailed insights for each cluster
            $this->info("\n💡 CLUSTER INSIGHTS:");
            foreach ($clusteredData as $clusterIndex => $cluster) {
                if (!empty($cluster['customers'])) {
                    $stats = $cluster['stats'];
                    $this->line("\n  📊 " . $cluster['label'] . ":");
                    $this->line("    • Customers: " . number_format($stats['count']) . " (" . round(($stats['count'] / $totalCustomers) * 100, 1) . "%)");
                    $this->line("    • Avg Purchase Frequency: " . number_format($stats['avg_purchases'], 1) . "x");
                    $this->line("    • Avg Customer Lifetime: " . number_format($stats['avg_lifetime']) . " days");
                    $this->line("    • Avg Unique Packages Tried: " . number_format($stats['avg_unique_packages'], 1));

                    // Find most popular packages in this cluster
                    $packageCounts = [];
                    foreach ($cluster['customers'] as $customer) {
                        foreach ($customer['packages'] as $package) {
                            if (!isset($packageCounts[$package])) {
                                $packageCounts[$package] = 0;
                            }
                            $packageCounts[$package]++;
                        }
                    }

                    arsort($packageCounts);
                    $topPackages = array_slice($packageCounts, 0, 3, true);

                    if (!empty($topPackages)) {
                        $this->line("    • Top Packages:");
                        foreach ($topPackages as $packageId => $count) {
                            $packageName = $this->packageNames[$packageId] ?? 'Package-' . $packageId;
                            $this->line("      - " . $packageName . " (" . $count . " customers)");
                        }
                    }
                }
            }
        }
    }

    private function showPersonalizedRecommendations($data)
    {
        if ($this->simpleMode) {
            $this->info("\n🎯 2. PERSONALIZED RECOMMENDATIONS");
            $this->line(str_repeat("=", 60));
        } else {
            $this->info("\n🔔 PERSONALIZED RECOMMENDATION ENGINE:");
            $this->line(str_repeat("=", 60));
        }

        // Group data by customer - HANYA ambil 5 customer untuk contoh
        $customerData = [];
        $customerCount = 0;

        foreach ($data as $item) {
            $customerId = $item['id_pelanggan'];

            if (!isset($customerData[$customerId])) {
                if ($customerCount >= 10) { // Batasi hanya 10 customer untuk contoh
                    continue;
                }

                $customerData[$customerId] = [
                    'purchases' => [],
                    'total_spent' => 0,
                    'first_purchase' => null,
                    'last_purchase' => null,
                    'package_count' => 0
                ];
                $customerCount++;
            }

            $customerData[$customerId]['purchases'][] = [
                'date' => $item['event_date'],
                'package' => $item['package_keyword'],
                'package_name' => $item['package_name'] ?? 'Package-' . $item['package_keyword'],
                'price' => $item['rev'],
                'quota' => $item['quota_bns_mb'],
                'days' => $item['day']
            ];

            $customerData[$customerId]['total_spent'] += $item['rev'];
            $customerData[$customerId]['package_count']++;

            if (!$customerData[$customerId]['first_purchase'] || $item['event_date'] < $customerData[$customerId]['first_purchase']) {
                $customerData[$customerId]['first_purchase'] = $item['event_date'];
            }

            if (!$customerData[$customerId]['last_purchase'] || $item['event_date'] > $customerData[$customerId]['last_purchase']) {
                $customerData[$customerId]['last_purchase'] = $item['event_date'];
            }
        }

        // Categorize customers dan tampilkan hanya yang relevan
        $displayedCount = 0;
        foreach ($customerData as $customerId => $customer) {
            if ($displayedCount >= 5) { // Batasi hanya 5 customer yang ditampilkan
                break;
            }

            $purchaseCount = $customer['package_count'];
            $purchasePeriod = $customer['last_purchase'] - $customer['first_purchase'];

            // Hanya tampilkan jika memiliki pola yang menarik
            if ($purchaseCount >= 2 || ($purchaseCount == 1 && $customer['total_spent'] > 100000)) {
                $this->line("\n📞 Customer ID: " . substr($customerId, 0, 8) . "...");
                $this->line("📊 Purchase History: {$purchaseCount} purchases");
                $this->line("💰 Total Spent: Rp " . number_format($customer['total_spent']));

                if ($purchaseCount >= 3 && $purchasePeriod <= 300) {
                    $this->line("🏆 Category: LOYAL CUSTOMER");
                    $this->line("💡 Recommendation: Special bundle offer + 15% discount");
                    $this->line("   • Bundle: Multiple packages at 20% off");
                    $this->line("   • Benefit: Free 1GB bonus quota");

                } elseif ($purchaseCount == 2 && $purchasePeriod <= 200) {
                    $this->line("⭐ Category: REGULAR CUSTOMER");
                    $this->line("💡 Recommendation: Upgrade offer + 10% discount");
                    $this->line("   • Upgrade: Higher quota package");
                    $this->line("   • Benefit: Extra 500MB free quota");

                } elseif ($purchaseCount == 1) {
                    $this->line("🆕 Category: NEW CUSTOMER");
                    $this->line("💡 Recommendation: Welcome package + 15% discount on next purchase");
                    $this->line("   • Offer: Second purchase discount");
                    $this->line("   • Benefit: Travel insurance included");
                }

                // Show purchase pattern
                $packages = array_unique(array_column($customer['purchases'], 'package_name'));
                $this->line("🛒 Purchased: " . implode(", ", $packages));

                $displayedCount++;
            }
        }

        if ($displayedCount === 0) {
            $this->line("No significant customer patterns found for recommendations.");
        }
    }

    private function showReminderMessages($data)
    {
        if ($this->simpleMode) {
            $this->info("\n⏰ 3. REMINDER MESSAGES & CAMPAIGNS");
            $this->line(str_repeat("=", 60));
        } else {
            $this->info("\n🔔 AUTOMATIC REMINDER SYSTEM:");
            $this->line(str_repeat("=", 60));
        }

        // Analisis data untuk menentukan reminder
        $customerData = [];
        foreach ($data as $item) {
            $customerId = $item['id_pelanggan'];

            if (!isset($customerData[$customerId])) {
                $customerData[$customerId] = [
                    'last_purchase' => $item['event_date'],
                    'purchase_count' => 0,
                    'total_spent' => 0,
                    'package_names' => []
                ];
            }

            $customerData[$customerId]['purchase_count']++;
            $customerData[$customerId]['total_spent'] += $item['rev'];
            $customerData[$customerId]['last_purchase'] = max($customerData[$customerId]['last_purchase'], $item['event_date']);

            if (!in_array($item['package_name'], $customerData[$customerId]['package_names'])) {
                $customerData[$customerId]['package_names'][] = $item['package_name'];
            }
        }

        // Categorize customers for reminders
        $loyalCustomers = [];
        $inactiveCustomers = [];
        $newCustomers = [];

        $currentYear = 2025; // Asumsi tahun saat ini
        $monthsInactive = 3; // 3 bulan dianggap inactive

        foreach ($customerData as $customerId => $customer) {
            $monthsSinceLastPurchase = $currentYear - $customer['last_purchase']; // Ini simplified

            if ($customer['purchase_count'] >= 3) {
                $loyalCustomers[$customerId] = $customer;
            } elseif ($monthsSinceLastPurchase >= $monthsInactive) {
                $inactiveCustomers[$customerId] = $customer;
            } elseif ($customer['purchase_count'] == 1) {
                $newCustomers[$customerId] = $customer;
            }
        }

        if ($this->simpleMode) {
            // Tampilkan summary
            $this->line("\n📊 CUSTOMER SEGMENTS FOR REMINDERS:");
            $this->line("• Loyal Customers: " . count($loyalCustomers) . " customers");
            $this->line("• Inactive Customers (>3 months): " . count($inactiveCustomers) . " customers");
            $this->line("• New Customers: " . count($newCustomers) . " customers");

            // Tampilkan contoh reminder untuk setiap segment
            if (!empty($loyalCustomers)) {
                $this->line("\n💌 LOYAL CUSTOMER REMINDER EXAMPLE:");
                $this->line("   Subject: Special Reward for You! 🎁");
                $this->line("   Message: Thank you for being our loyal customer!");
                $this->line("   Offer: 20% discount + Free 2GB bonus");
                $this->line("   Timing: Send every 60 days");
            }

            if (!empty($inactiveCustomers)) {
                $this->line("\n💌 INACTIVE CUSTOMER REMINDER EXAMPLE:");
                $this->line("   Subject: We Miss You! 😊");
                $this->line("   Message: Haven't seen you in a while...");
                $this->line("   Offer: 25% welcome back discount");
                $this->line("   Timing: Send at 90 days inactivity");
            }

            if (!empty($newCustomers)) {
                $this->line("\n💌 NEW CUSTOMER REMINDER EXAMPLE:");
                $this->line("   Subject: Welcome & Thank You! 🎉");
                $this->line("   Message: Hope you enjoyed your first package!");
                $this->line("   Offer: 15% off next purchase");
                $this->line("   Timing: Send after 30 days");
            }
        } else {
            // Tampilkan detail reminder messages
            $this->showDetailedReminderMessages($customerData);
        }

        // Tampilkan campaign recommendations
        $this->line("\n🎯 RECOMMENDED CAMPAIGNS:");
        $this->line("1. Win-back Campaign: Target " . count($inactiveCustomers) . " inactive customers");
        $this->line("2. Loyalty Program: Reward " . count($loyalCustomers) . " loyal customers");
        $this->line("3. New Customer Onboarding: Engage " . count($newCustomers) . " new customers");
        $this->line("4. Cross-sell Campaign: Based on package preferences");
    }

    private function showDetailedReminderMessages($customerData)
    {
        // Untuk mode non-simple, tampilkan detail
        $sampleCustomers = array_slice($customerData, 0, 3, true);

        foreach ($sampleCustomers as $customerId => $customer) {
            $this->line("\n📱 REMINDER FOR: Customer " . substr($customerId, 0, 8) . "...");
            $this->line("📊 Stats: " . $customer['purchase_count'] . " purchases, Rp " .
                       number_format($customer['total_spent']) . " spent");

            if ($customer['purchase_count'] >= 3) {
                $this->line("💌 Message: Special loyalty offer - 20% discount on next purchase");
            } else {
                $this->line("💌 Message: Thank you for your purchase! Enjoy 15% off next time");
            }
        }
    }

    private function showSampleData($data, $count = 5)
    {
        if ($this->simpleMode) return;

        $sample = array_slice($data, 0, $count);

        if (empty($sample)) {
            $this->warn("No data to display");
            return;
        }

        $this->info("\n📋 SAMPLE DATA (First {$count} records):");
        $this->line(str_repeat("-", 120));

        $headers = ['No', 'ID', 'Package Code', 'Package Name', 'Days', 'Revenue', 'Quota', 'Region'];
        $rows = [];

        foreach ($sample as $index => $item) {
            $packageName = !empty($item['package_name'])
                ? (strlen($item['package_name']) > 25
                    ? substr($item['package_name'], 0, 22) . '...'
                    : $item['package_name'])
                : 'Pkg-' . $item['package_keyword'];

            $rows[] = [
                $index + 1,
                strlen($item['id_pelanggan']) > 8
                    ? substr($item['id_pelanggan'], 0, 8) . '...'
                    : $item['id_pelanggan'],
                $item['package_keyword'],
                $packageName,
                $item['day'] . 'd',
                'Rp ' . number_format($item['rev']),
                number_format($item['quota_bns_mb']) . 'MB',
                substr($item['regional'], 0, 10),
            ];
        }

        $this->table($headers, $rows);

        // Check package name availability
        $packageNames = array_column($sample, 'package_name');
        $hasPackageNames = count(array_filter($packageNames, fn($name) => !empty($name) && $name !== 'Package-0'));

        if ($hasPackageNames > 0) {
            $uniqueNames = array_unique(array_filter($packageNames));
            $this->info("✅ Package names detected: " . count($uniqueNames) . " unique names in sample");
        } else {
            $this->warn("⚠️ No package names found in sample data");
        }
    }

    private function showStatistics($data)
    {
        if ($this->simpleMode) return;

        $this->info("\n📈 BASIC STATISTICS:");
        $this->line(str_repeat("-", 50));

        if (empty($data)) {
            $this->line("  No data available");
            return;
        }

        $revenues = array_column($data, 'rev');
        $quotas = array_column($data, 'quota_bns_mb');
        $days = array_column($data, 'day');

        $customerPurchases = [];
        foreach ($data as $item) {
            $customerId = $item['id_pelanggan'];
            if (!isset($customerPurchases[$customerId])) {
                $customerPurchases[$customerId] = [
                    'count' => 0,
                    'total_rev' => 0,
                    'packages' => []
                ];
            }
            $customerPurchases[$customerId]['count'] += $item['trx'];
            $customerPurchases[$customerId]['total_rev'] += $item['rev'];
            $customerPurchases[$customerId]['packages'][] = $item['package_keyword'];
        }

        $purchaseCounts = array_column($customerPurchases, 'count');

        $stats = [
            ['Total Records', number_format(count($data))],
            ['Unique Customers', number_format(count($customerPurchases))],
            ['Avg Purchases/Customer', number_format(array_sum($purchaseCounts) / max(1, count($purchaseCounts)), 1) . 'x'],
            ['One-Time Customers', number_format(count(array_filter($purchaseCounts, fn($x) => $x == 1))) . ' (' .
                round(count(array_filter($purchaseCounts, fn($x) => $x == 1)) / max(1, count($purchaseCounts)) * 100, 1) . '%)'],
            ['Repeat Customers (2-5x)', number_format(count(array_filter($purchaseCounts, fn($x) => $x >= 2 && $x <= 5))) . ' (' .
                round(count(array_filter($purchaseCounts, fn($x) => $x >= 2 && $x <= 5)) / max(1, count($purchaseCounts)) * 100, 1) . '%)'],
            ['Frequent Customers (>5x)', number_format(count(array_filter($purchaseCounts, fn($x) => $x > 5))) . ' (' .
                round(count(array_filter($purchaseCounts, fn($x) => $x > 5)) / max(1, count($purchaseCounts)) * 100, 1) . '%)'],
            ['Total Revenue', 'Rp ' . number_format(array_sum($revenues))],
            ['Avg Revenue/Transaction', 'Rp ' . number_format(array_sum($revenues) / max(1, count($revenues)))],
            ['Avg Quota/Transaction', number_format(array_sum($quotas) / max(1, count($quotas))) . ' MB'],
            ['Avg Duration', number_format(array_sum($days) / max(1, count($days)), 1) . ' days'],
        ];

        foreach ($stats as $stat) {
            $this->line(sprintf("  %-25s : %s", $stat[0], $stat[1]));
        }
    }

    private function randomForestAnalysis($data)
    {
        if ($this->simpleMode) return;

        $this->info("\n🌲 RANDOM FOREST - PRODUCT RECOMMENDATION ENGINE:");
        $this->line(str_repeat("-", 70));

        // Group data by customer for recommendation analysis
        $customerData = [];
        $packageData = [];

        foreach ($data as $item) {
            $customerId = $item['id_pelanggan'];
            $packageId = $item['package_keyword'];

            // Customer data
            if (!isset($customerData[$customerId])) {
                $customerData[$customerId] = [
                    'packages' => [],
                    'total_spent' => 0,
                    'avg_revenue' => 0,
                    'regions' => [],
                    'purchase_count' => 0
                ];
            }

            if (!in_array($packageId, $customerData[$customerId]['packages'])) {
                $customerData[$customerId]['packages'][] = $packageId;
            }
            $customerData[$customerId]['total_spent'] += $item['rev'];
            $customerData[$customerId]['purchase_count']++;
            if (!in_array($item['regional'], $customerData[$customerId]['regions'])) {
                $customerData[$customerId]['regions'][] = $item['regional'];
            }

            // Package data
            if (!isset($packageData[$packageId])) {
                $packageData[$packageId] = [
                    'name' => $item['package_name'] ?? 'Package-' . $packageId,
                    'total_revenue' => 0,
                    'total_customers' => 0,
                    'avg_quota' => 0,
                    'avg_days' => 0,
                    'avg_price' => 0,
                    'customer_ids' => []
                ];
            }

            $packageData[$packageId]['total_revenue'] += $item['rev'];
            if (!in_array($customerId, $packageData[$packageId]['customer_ids'])) {
                $packageData[$packageId]['customer_ids'][] = $customerId;
                $packageData[$packageId]['total_customers']++;
            }

            // Update package averages
            $packageData[$packageId]['avg_quota'] = ($packageData[$packageId]['avg_quota'] + $item['quota_bns_mb']) / 2;
            $packageData[$packageId]['avg_days'] = ($packageData[$packageId]['avg_days'] + $item['day']) / 2;
            $packageData[$packageId]['avg_price'] = ($packageData[$packageId]['avg_price'] + $item['rev']) / 2;
        }

        // Calculate customer averages
        foreach ($customerData as $customerId => $customer) {
            $customerData[$customerId]['avg_revenue'] = $customer['total_spent'] / $customer['purchase_count'];
        }

        // Analyze package popularity and customer behavior
        $this->info("📊 PACKAGE ANALYSIS:");

        // Sort packages by popularity (number of customers)
        uasort($packageData, function ($a, $b) {
            return $b['total_customers'] - $a['total_customers'];
        });

        $topPackages = array_slice($packageData, 0, 10, true);

        $packageTable = [];
        foreach ($topPackages as $packageId => $package) {
            $packageTable[] = [
                $package['name'],
                number_format($package['total_customers']),
                'Rp ' . number_format($package['total_revenue']),
                'Rp ' . number_format($package['avg_price']),
                number_format($package['avg_quota']) . ' MB',
                number_format($package['avg_days']) . ' days',
                round(($package['total_customers'] / count($customerData)) * 100, 1) . '%'
            ];
        }

        $this->table(
            ['Package', 'Customers', 'Total Revenue', 'Avg Price', 'Avg Quota', 'Avg Days', 'Penetration'],
            $packageTable
        );

        // Generate recommendations based on customer purchase history
        $this->info("\n🎯 PERSONALIZED RECOMMENDATIONS:");

        // Analyze patterns for cross-selling opportunities
        $packageAssociations = [];

        foreach ($customerData as $customerId => $customer) {
            $packages = $customer['packages'];
            if (count($packages) > 1) {
                // Find package pairs purchased together
                for ($i = 0; $i < count($packages); $i++) {
                    for ($j = $i + 1; $j < count($packages); $j++) {
                        $pair = [$packages[$i], $packages[$j]];
                        sort($pair);
                        $key = $pair[0] . '-' . $pair[1];

                        if (!isset($packageAssociations[$key])) {
                            $packageAssociations[$key] = [
                                'package1' => $pair[0],
                                'package2' => $pair[1],
                                'count' => 0,
                                'customers' => []
                            ];
                        }

                        $packageAssociations[$key]['count']++;
                        if (!in_array($customerId, $packageAssociations[$key]['customers'])) {
                            $packageAssociations[$key]['customers'][] = $customerId;
                        }
                    }
                }
            }
        }

        // Sort associations by frequency
        uasort($packageAssociations, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        // Show top 5 package associations
        $this->info("\n🔗 TOP PACKAGE COMBINATIONS (Customers who bought both):");
        $topAssociations = array_slice($packageAssociations, 0, 5, true);

        $associationTable = [];
        foreach ($topAssociations as $key => $association) {
            $package1Name = $packageData[$association['package1']]['name'] ?? 'Package-' . $association['package1'];
            $package2Name = $packageData[$association['package2']]['name'] ?? 'Package-' . $association['package2'];

            $associationTable[] = [
                $package1Name,
                $package2Name,
                number_format($association['count']),
                round(($association['count'] / count($customerData)) * 100, 1) . '%'
            ];
        }

        $this->table(
            ['Package A', 'Package B', 'Customers', 'Penetration'],
            $associationTable
        );

        // Generate discount recommendations
        $this->info("\n💰 DISCOUNT RECOMMENDATION ENGINE:");

        // Analyze which packages need promotion
        $promotionCandidates = [];

        foreach ($packageData as $packageId => $package) {
            $penetration = ($package['total_customers'] / count($customerData)) * 100;
            $avgPrice = $package['avg_price'];
            $avgQuota = $package['avg_quota'];

            // Determine discount recommendation
            $discountReason = '';
            $discountPercentage = 0;

            if ($penetration < 5) {
                // Low penetration - aggressive discount
                $discountReason = 'Low market penetration';
                $discountPercentage = 20;
            } elseif ($avgPrice > 1000000) {
                // High price - moderate discount
                $discountReason = 'Premium pricing';
                $discountPercentage = 15;
            } elseif ($avgQuota > 20000 && $penetration < 15) {
                // High quota but low adoption
                $discountReason = 'High value, low adoption';
                $discountPercentage = 10;
            } elseif ($penetration < 20) {
                // Moderate penetration - small discount
                $discountReason = 'Growth opportunity';
                $discountPercentage = 5;
            }

            if ($discountPercentage > 0) {
                $promotionCandidates[$packageId] = [
                    'package' => $package['name'],
                    'current_price' => $avgPrice,
                    'recommended_price' => $avgPrice * (1 - ($discountPercentage / 100)),
                    'discount' => $discountPercentage,
                    'reason' => $discountReason,
                    'penetration' => $penetration,
                    'customers' => $package['total_customers']
                ];
            }
        }

        // Sort by discount percentage (highest first)
        uasort($promotionCandidates, function ($a, $b) {
            return $b['discount'] - $a['discount'];
        });

        $discountTable = [];
        foreach ($promotionCandidates as $candidate) {
            $discountTable[] = [
                $candidate['package'],
                'Rp ' . number_format($candidate['current_price']),
                'Rp ' . number_format($candidate['recommended_price']),
                $candidate['discount'] . '%',
                $candidate['reason'],
                round($candidate['penetration'], 1) . '%'
            ];
        }

        $this->table(
            ['Package', 'Current Price', 'Rec. Price', 'Discount', 'Reason', 'Penetration'],
            array_slice($discountTable, 0, 8)
        );

        // Generate targeted customer recommendations
        $this->info("\n🎯 TARGETED CUSTOMER RECOMMENDATIONS:");

        // Find customers who bought only one package
        $singlePackageCustomers = array_filter($customerData, function ($customer) {
            return count($customer['packages']) == 1;
        });

        if (!empty($singlePackageCustomers)) {
            $this->line("  • " . count($singlePackageCustomers) . " customers bought only 1 package");
            $this->line("  • Recommendation: Cross-sell based on package associations");

            // Example: For customers who bought package 69339, recommend associated packages
            $package69339Customers = array_filter($singlePackageCustomers, function ($customer) {
                return in_array(69339, $customer['packages']);
            });

            if (!empty($package69339Customers)) {
                $this->line("  • For " . count($package69339Customers) . " customers of Package 69339:");
                $this->line("    Recommend: Package 60034 (bought by " .
                    ($packageAssociations['69339-60034']['count'] ?? 0) . " customers together)");
            }
        }

        // Show upgrade opportunities
        $this->info("\n⬆️ UPGRADE OPPORTUNITIES:");

        // Find customers with low-quota packages who might upgrade
        $lowQuotaPackages = array_filter($packageData, function ($package) {
            return $package['avg_quota'] < 10000;
        });

        $upgradeOpportunities = 0;
        foreach ($customerData as $customerId => $customer) {
            foreach ($customer['packages'] as $packageId) {
                if (isset($lowQuotaPackages[$packageId])) {
                    $upgradeOpportunities++;
                    break;
                }
            }
        }

        $this->line("  • " . number_format($upgradeOpportunities) . " customers using low-quota packages");
        $this->line("  • Potential upgrade to higher-quota packages");
        $this->line("  • Recommended upgrade path: 5,000MB → 10,000MB → 20,000MB");

        // Summary of recommendations
        $this->info("\n💡 RECOMMENDATION SUMMARY:");
        $this->line("  1. Cross-sell based on package associations");
        $this->line("  2. Offer targeted discounts for low-penetration packages");
        $this->line("  3. Upgrade customers from low to high quota packages");
        $this->line("  4. Bundle popular package combinations");
        $this->line("  5. Personalize offers based on regional preferences");

        $estimatedUplift = count($singlePackageCustomers) * 0.3 * 500000; // 30% conversion at avg Rp 500k
        $this->line("\n💰 Estimated Revenue Uplift: Rp " . number_format($estimatedUplift));
    }

    private function showColumnMapping()
    {
        if ($this->simpleMode) return;

        if ($this->hasHeaders && $this->debugMode) {
            $this->info("\n🔍 CSV COLUMN STRUCTURE DETECTED:");
            $this->line("  Index → CSV Header → Mapped Field → Sample Data");
            $this->line(str_repeat("-", 90));

            // Berdasarkan data Anda
            $columnInfo = [
                0 => ['a.event_date', 'event_date', '2025'],
                1 => ['c.area', 'area', 'JAWA BALI'],
                2 => ['c.regional', 'regional', 'JATIM'],
                3 => ['c.cluster', 'cluster', 'KOTA SURABAYA'],
                4 => ['c.kabupaten', 'kabupaten', 'KOTA SURABAYA'],
                5 => ['id_pelanggan', 'id_pelanggan', '1888578110135'],
                6 => ['a.package_keyword', 'package_keyword', '69339'],
                7 => ['b.grouping → ACTUALLY package_name', 'package_name', 'SAUDI ARABIA (UMROH)'],
                8 => ['b.day', 'day', '12'],
                9 => ['b.quota_bns_mb', 'quota_bns_mb', '10000'],
                10 => ['rev', 'rev', '255050'],
                11 => ['trx', 'trx', '1'],
                12 => ['subs', 'subs', '1'],
            ];

            foreach ($columnInfo as $index => $info) {
                $this->line(sprintf("  [%2d] %-30s → %-15s → %s",
                    $index,
                    $info[0],
                    $info[1],
                    $info[2]));
            }

            $this->line("\n⚠️  IMPORTANT NOTE:");
            $this->line("   • Column 7 ('b.grouping') in your CSV actually contains PACKAGE NAMES");
            $this->line("   • There is NO separate 'grouping' column in your data");
            $this->line("   • Using column 7 as 'package_name' automatically");
        }
    }

    private function showExecutionSummary()
    {
        if ($this->simpleMode) return;

        $executionTime = microtime(true) - $this->executionStartTime;
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

        $this->info("\n📊 EXECUTION SUMMARY:");
        $this->line(str_repeat("-", 50));
        $this->line(sprintf("  %-25s : %s", "Total Data Processed", number_format($this->totalDataLoaded)));
        $this->line(sprintf("  %-25s : %.2f seconds", "Execution Time", $executionTime));
        $this->line(sprintf("  %-25s : %.2f MB", "Peak Memory Usage", $memoryUsage));
        $this->line(sprintf("  %-25s : %s", "Process ID", getmypid()));
        $this->line(sprintf("  %-25s : %s", "Timestamp", date('Y-m-d H:i:s')));
    }

    private function generateDummyTransactions($data)
    {
        if ($this->simpleMode) return;

        $this->info("\n🎯 GENERATING PERSONALIZED RECOMMENDATIONS BASED ON PURCHASE HISTORY:");
        $this->line(str_repeat("=", 80));

        // Tambahkan data dummy untuk 3 kondisi
        $dummyTransactions = [
            // KONDISI 1: Pelanggan yang sudah beli 3x dalam 3 bulan
            '144491638680966' => [
                'name' => 'Customer D (Loyal)',
                'purchase_count' => 3,
                'purchase_period' => '3 months',
                'transactions' => [
                    ['date' => '2025-01-10', 'package' => '24071', 'package_name' => 'SAUDI ARABIA (UMROH)', 'price' => 233750],
                    ['date' => '2025-02-15', 'package' => '69339', 'package_name' => 'SAUDI ARABIA (UMROH)', 'price' => 255000],
                    ['date' => '2025-03-20', 'package' => '60034', 'package_name' => 'ROAMax CHINA', 'price' => 382500]
                ],
                'condition' => 1
            ],

            // KONDISI 2: Pelanggan yang sudah beli 2x dalam 3 bulan
            '144560187199453' => [
                'name' => 'Customer E (Regular)',
                'purchase_count' => 2,
                'purchase_period' => '3 months',
                'transactions' => [
                    ['date' => '2025-02-20', 'package' => '69339', 'package_name' => 'SAUDI ARABIA (UMROH)', 'price' => 255000],
                    ['date' => '2025-03-25', 'package' => '60053', 'package_name' => 'ROAMax EROPA', 'price' => 340000]
                ],
                'condition' => 2
            ],

            // KONDISI 3: Pelanggan baru (tidak ada di data)
            '199999999999999' => [
                'name' => 'Customer F (New)',
                'purchase_count' => 1,
                'purchase_period' => 'First purchase',
                'transactions' => [
                    ['date' => '2025-04-01', 'package' => '69339', 'package_name' => 'SAUDI ARABIA (UMROH)', 'price' => 255000]
                ],
                'condition' => 3
            ]
        ];

        // Analisis pola pembelian
        $packagePatterns = $this->analyzePurchasePatterns($dummyTransactions);

        // Tampilkan rekomendasi untuk setiap kondisi
        $this->info("\n📋 RECOMMENDATION RESULTS:");

        foreach ($dummyTransactions as $customerId => $customer) {
            $this->line("\n" . str_repeat("-", 60));
            $this->line("🎯 Customer: {$customer['name']}");
            $this->line("📞 ID: {$customerId}");
            $this->line("📊 Purchase History: {$customer['purchase_count']}x in {$customer['purchase_period']}");

            // Tampilkan riwayat transaksi
            $this->line("\n🛒 Purchase History:");
            foreach ($customer['transactions'] as $index => $transaction) {
                $this->line("   {$transaction['date']}: {$transaction['package_name']} - Rp " . number_format($transaction['price']));
            }

            // Berikan rekomendasi berdasarkan kondisi
            switch ($customer['condition']) {
                case 1: // Sudah beli 3x
                    $this->showRecommendationCondition1($customer, $packagePatterns);
                    break;

                case 2: // Sudah beli 2x
                    $this->showRecommendationCondition2($customer, $packagePatterns);
                    break;

                case 3: // Pembeli pertama
                    $this->showRecommendationCondition3($customer);
                    break;
            }
        }
    }

    private function analyzePurchasePatterns($transactions)
    {
        $patterns = [
            'package_combinations' => [],
            'price_ranges' => [],
            'destination_preferences' => [],
            'purchase_frequency' => []
        ];

        foreach ($transactions as $customerId => $customer) {
            if ($customer['condition'] == 1 || $customer['condition'] == 2) {
                $packages = array_column($customer['transactions'], 'package');
                $packageNames = array_column($customer['transactions'], 'package_name');
                $prices = array_column($customer['transactions'], 'price');

                // Analisis kombinasi paket
                $packageKey = implode(',', $packages);
                if (!isset($patterns['package_combinations'][$packageKey])) {
                    $patterns['package_combinations'][$packageKey] = 0;
                }
                $patterns['package_combinations'][$packageKey]++;

                // Analisis range harga
                $avgPrice = array_sum($prices) / count($prices);
                $priceRange = floor($avgPrice / 50000) * 50000;
                $patterns['price_ranges'][$priceRange] = ($patterns['price_ranges'][$priceRange] ?? 0) + 1;

                // Analisis preferensi destinasi
                foreach ($packageNames as $packageName) {
                    if (strpos($packageName, 'SAUDI ARABIA') !== false) {
                        $patterns['destination_preferences']['Middle East'] = ($patterns['destination_preferences']['Middle East'] ?? 0) + 1;
                    } elseif (strpos($packageName, 'CHINA') !== false) {
                        $patterns['destination_preferences']['Asia'] = ($patterns['destination_preferences']['Asia'] ?? 0) + 1;
                    } elseif (strpos($packageName, 'EROPA') !== false) {
                        $patterns['destination_preferences']['Europe'] = ($patterns['destination_preferences']['Europe'] ?? 0) + 1;
                    }
                }

                // Analisis frekuensi
                $patterns['purchase_frequency'][$customer['purchase_count']] = ($patterns['purchase_frequency'][$customer['purchase_count']] ?? 0) + 1;
            }
        }

        return $patterns;
    }

    private function showRecommendationCondition1($customer, $patterns)
    {
        $this->info("\n🌟 PERSONALIZED RECOMMENDATION (Loyal Customer - 3 purchases):");

        // Analisis pola pembelian
        $packageTypes = [];
        $totalSpent = 0;
        $destinations = [];

        foreach ($customer['transactions'] as $transaction) {
            $totalSpent += $transaction['price'];

            if (strpos($transaction['package_name'], 'SAUDI ARABIA') !== false) {
                $destinations['Middle East'] = ($destinations['Middle East'] ?? 0) + 1;
            } elseif (strpos($transaction['package_name'], 'CHINA') !== false) {
                $destinations['Asia'] = ($destinations['Asia'] ?? 0) + 1;
            } elseif (strpos($transaction['package_name'], 'EROPA') !== false) {
                $destinations['Europe'] = ($destinations['Europe'] ?? 0) + 1;
            }
        }

        $avgSpent = $totalSpent / $customer['purchase_count'];
        $preferredDestination = array_keys($destinations, max($destinations))[0] ?? 'Middle East';

        // Rekomendasi berdasarkan pola
        $this->line("✅ Purchase Pattern Analysis:");
        $this->line("   • Average spending: Rp " . number_format($avgSpent));
        $this->line("   • Preferred destination: {$preferredDestination}");
        $this->line("   • Purchase frequency: Every 30 days");

        // Rekomendasi paket dengan harga murah
        $this->line("\n💰 AFFORDABLE PACKAGE RECOMMENDATIONS:");

        $recommendations = [
            [
                'package' => '24071',
                'name' => 'SAUDI ARABIA (UMROH) - ECONOMY',
                'original_price' => 233750,
                'discounted_price' => 210375, // 10% discount
                'discount' => '10%',
                'reason' => 'Matches your travel pattern to Middle East'
            ],
            [
                'package' => '60034',
                'name' => 'ROAMax CHINA - BUDGET',
                'original_price' => 382500,
                'discounted_price' => 344250, // 10% discount
                'discount' => '10%',
                'reason' => 'Asian destination with high quota'
            ],
            [
                'package' => '60053',
                'name' => 'ROAMax EROPA - VALUE',
                'original_price' => 340000,
                'discounted_price' => 306000, // 10% discount
                'discount' => '10%',
                'reason' => 'European package at discounted rate'
            ]
        ];

        foreach ($recommendations as $rec) {
            $this->line("   📦 {$rec['name']}");
            $this->line("     Original: Rp " . number_format($rec['original_price']));
            $this->line("     Discounted: Rp " . number_format($rec['discounted_price']) . " ({$rec['discount']} OFF)");
            $this->line("     Reason: {$rec['reason']}");
            $this->line("");
        }

        // Bundle recommendation
        $this->line("🎁 SPECIAL BUNDLE OFFER (Best Value):");
        $this->line("   ✈️ Middle East + Asia Combo Package");
        $this->line("   Price: Rp 450,000 (Save 20%)");
        $this->line("   Includes: Saudi Arabia 12 days + China 15 days");
        $this->line("   Validity: 6 months");

        // Loyalty benefits
        $this->line("\n🎫 LOYALTY BENEFITS:");
        $this->line("   • Priority customer support");
        $this->line("   • Free 1GB bonus quota on next purchase");
        $this->line("   • Early access to new packages");
    }

    private function showRecommendationCondition2($customer, $patterns)
    {
        $this->info("\n🌟 PERSONALIZED RECOMMENDATION (Regular Customer - 2 purchases):");

        // Analisis pola pembelian
        $destinations = [];
        foreach ($customer['transactions'] as $transaction) {
            if (strpos($transaction['package_name'], 'SAUDI ARABIA') !== false) {
                $destinations['Middle East'] = ($destinations['Middle East'] ?? 0) + 1;
            } elseif (strpos($transaction['package_name'], 'EROPA') !== false) {
                $destinations['Europe'] = ($destinations['Europe'] ?? 0) + 1;
            }
        }

        $preferredDestination = array_keys($destinations, max($destinations))[0] ?? 'Middle East';

        $this->line("✅ Purchase Pattern Analysis:");
        $this->line("   • Preferred destination: {$preferredDestination}");
        $this->line("   • Purchase interval: 35 days");

        // Rekomendasi paket dengan harga murah
        $this->line("\n💰 AFFORDABLE PACKAGE RECOMMENDATIONS:");

        $recommendations = [
            [
                'package' => '69339',
                'name' => 'SAUDI ARABIA (UMROH) - STANDARD',
                'original_price' => 255000,
                'discounted_price' => 229500, // 10% discount
                'discount' => '10%',
                'reason' => 'Continue your Middle East journey'
            ],
            [
                'package' => '60053',
                'name' => 'ROAMax EROPA - ECONOMY',
                'original_price' => 340000,
                'discounted_price' => 306000, // 10% discount
                'discount' => '10%',
                'reason' => 'Try European destination'
            ]
        ];

        foreach ($recommendations as $rec) {
            $this->line("   📦 {$rec['name']}");
            $this->line("     Original: Rp " . number_format($rec['original_price']));
            $this->line("     Discounted: Rp " . number_format($rec['discounted_price']) . " ({$rec['discount']} OFF)");
            $this->line("     Reason: {$rec['reason']}");
            $this->line("");
        }

        // Upgrade recommendation
        $this->line("⬆️ UPGRADE RECOMMENDATION:");
        $this->line("   ✈️ Premium Saudi Arabia Package");
        $this->line("   Price: Rp 300,000 (Better value)");
        $this->line("   Includes: 15GB quota, 15 days");
        $this->line("   Extra: Free hotspot sharing");
    }

    private function showRecommendationCondition3($customer)
    {
        $this->info("\n🎉 WELCOME NEW CUSTOMER!");
        $this->line("Selamat atas pembelian pertamamu! 🎊");

        $this->line("\n🌟 WELCOME OFFER:");
        $this->line("   • Discount 15% on next purchase");
        $this->line("   • Free 500MB bonus quota");
        $this->line("   • Travel insurance included");

        $this->line("\n💡 RECOMMENDED FOR YOU:");

        $recommendations = [
            [
                'package' => '69339',
                'name' => 'SAUDI ARABIA (UMROH) - STARTER',
                'price' => 255000,
                'reason' => 'Most popular package for first-time travelers'
            ],
            [
                'package' => '60034',
                'name' => 'ROAMax CHINA - BEGINNER',
                'price' => 382500,
                'reason' => 'Great value for Asian destinations'
            ],
            [
                'package' => '60053',
                'name' => 'ROAMax EROPA - DISCOVERY',
                'price' => 340000,
                'reason' => 'Explore Europe with confidence'
            ]
        ];

        foreach ($recommendations as $rec) {
            $this->line("   📦 {$rec['name']}");
            $this->line("     Price: Rp " . number_format($rec['price']));
            $this->line("     Reason: {$rec['reason']}");
            $this->line("");
        }

        $this->line("🎁 FIRST-TIME BUYER SPECIAL:");
        $this->line("   Buy any 2 packages, get 20% off total!");
        $this->line("   Valid for 30 days from first purchase");
    }

    private function showDataStructure($data)
    {
        if ($this->simpleMode) return;

        $this->info("\n🔍 DATA STRUCTURE ANALYSIS:");
        $this->line(str_repeat("-", 60));

        $sample = $data[0] ?? [];
        if ($sample) {
            $this->line("First record structure:");
            foreach ($sample as $key => $value) {
                $this->line(sprintf("  %-20s : %s (%s)",
                    $key,
                    $value,
                    gettype($value)));
            }
        }

        // Check package_name availability
        $withPackageName = count(array_filter($data, fn($item) => !empty($item['package_name'])));
        $total = count($data);

        $this->line("\n📦 Package Name Statistics:");
        $this->line("  Total records: " . number_format($total));
        $this->line("  With package name: " . number_format($withPackageName) .
            " (" . round(($withPackageName / $total) * 100, 1) . "%)");

        if ($withPackageName > 0) {
            // Tampilkan unique package names
            $packageNames = array_unique(array_column($data, 'package_name'));
            $packageNames = array_filter($packageNames, fn($name) => !empty($name));

            $this->line("  Unique package names: " . count($packageNames));
            $this->line("  First 5 package names: " . implode(", ", array_slice($packageNames, 0, 5)));
        }
    }

    private function showFilePreview($filepath)
    {
        if ($this->simpleMode) return;

        $this->info("\n📄 FILE PREVIEW (first 5 lines):");
        $this->line(str_repeat("-", 80));

        if (($handle = fopen($filepath, 'r')) !== false) {
            $lineCount = 0;
            while (($line = fgets($handle)) !== false && $lineCount < 5) {
                $lineCount++;
                $this->line("Line $lineCount: " . trim($line));
            }
            fclose($handle);
        }
    }
}
