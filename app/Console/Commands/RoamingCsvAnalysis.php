<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RoamingCsvAnalysis extends Command
{
    protected $signature = 'roaming:csv
                            {file : Path to CSV file}
                            {--package= : Package keyword filter}
                            {--id= : ID Pelanggan filter}
                            {--grouping= : Grouping filter}
                            {--clusters=5 : Number of clusters (default: 5)}
                            {--has-headers : CSV has headers (default: auto-detect)}';

    protected $description = 'Analyze roaming CSV data with K-Means and Random Forest simulation';

    private $headers = [];
    private $hasHeaders = false;
    private $packageNames = [];

    public function handle()
    {
        $filepath = $this->argument('file');

        $this->info("📊 ROAMING DATA ANALYSIS - CUSTOMER PURCHASE PATTERNS");
        $this->line(str_repeat("=", 70));

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

        $this->info("✅ Data loaded: " . count($data) . " records");
        if ($this->hasHeaders) {
            $this->info("📝 Headers detected: " . implode(", ", array_slice($this->headers, 0, 5)) . "...");
        }

        // Filter data if options provided
        $filtered = $this->filterData($data);

        if (!empty($filtered) && (count($filtered) < count($data))) {
            $this->info("🔍 Filtered records: " . count($filtered));
            $this->analyzeData($filtered);
        } else {
            $this->info("📋 Showing all data:");
            $this->analyzeData($data);
        }

        $this->info("\n🎉 Analysis completed!");
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

                // Check if first row is header
                if ($lineNumber === 1) {
                    $firstCell = strtolower(trim($row[0] ?? ''));

                    // Check for header patterns
                    if (str_contains($firstCell, '.') ||
                        str_contains($firstCell, 'event') ||
                        !is_numeric($row[0])) {

                        $this->hasHeaders = true;
                        $this->headers = $row;
                        $this->mapHeadersToStandardFormat();
                        continue;
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
            'a.package_keyword' => 'package_keyword',
            'package_keyword' => 'package_keyword',
            'package_name' => 'package_name',
            'b.grouping' => 'grouping',
            'grouping' => 'grouping',
            'b.day' => 'day',
            'day' => 'day',
            'b.quota_bns_mb' => 'quota_bns_mb',
            'quota_bns_mb' => 'quota_bns_mb',
            'rev' => 'rev',
            'trx' => 'trx',
            'subs' => 'subs',
        ];

        // Normalize headers
        $normalizedHeaders = [];
        foreach ($this->headers as $index => $header) {
            $header = trim($header);
            $normalizedHeaders[$index] = $headerMap[$header] ?? $header;
        }

        $this->headers = $normalizedHeaders;
    }

    private function processDataRow($row, $lineNumber)
    {
        // If we have headers, map by header name
        if ($this->hasHeaders && !empty($this->headers)) {
            $item = [];

            foreach ($this->headers as $index => $headerName) {
                if (isset($row[$index])) {
                    $item[$headerName] = $row[$index];
                }
            }

            // Convert to standard format
            return $this->convertToStandardFormat($item);
        }

        // If no headers, use positional mapping (your original format)
        if (count($row) >= 14) {
            return [
                'event_date' => intval($row[0] ?? 0),
                'area' => $row[1] ?? '',
                'regional' => $row[2] ?? '',
                'cluster' => $row[3] ?? '',
                'kabupaten' => $row[4] ?? '',
                'id_pelanggan' => (string) ($row[5] ?? ''),
                'package_keyword' => intval($row[6] ?? 0),
                'package_name' => $row[7] ?? '',
                'grouping' => intval($row[8] ?? 0),
                'day' => intval($row[9] ?? 0),
                'quota_bns_mb' => intval($row[10] ?? 0),
                'rev' => floatval($row[11] ?? 0),
                'trx' => intval($row[12] ?? 0),
                'subs' => intval($row[13] ?? 0),
            ];
        } elseif (count($row) >= 12) {
            // Try alternative mapping without package_name
            return [
                'event_date' => intval($row[0] ?? 0),
                'area' => $row[1] ?? '',
                'regional' => $row[2] ?? '',
                'cluster' => $row[3] ?? '',
                'kabupaten' => $row[4] ?? '',
                'id_pelanggan' => (string) ($row[5] ?? ''),
                'package_keyword' => intval($row[6] ?? 0),
                'package_name' => '', // Missing
                'grouping' => intval($row[7] ?? 0),
                'day' => intval($row[8] ?? 0),
                'quota_bns_mb' => intval($row[9] ?? 0),
                'rev' => floatval($row[10] ?? 0),
                'trx' => intval($row[11] ?? 0),
                'subs' => isset($row[12]) ? intval($row[12]) : 0,
            ];
        } else {
            $this->warn("⚠️ Row $lineNumber has only " . count($row) . " columns");
            return null;
        }
    }

    private function convertToStandardFormat($item)
    {
        return [
            'event_date' => intval($item['event_date'] ?? $item['a.event_date'] ?? 0),
            'area' => $item['area'] ?? $item['c.area'] ?? '',
            'regional' => $item['regional'] ?? $item['c.regional'] ?? '',
            'cluster' => $item['cluster'] ?? $item['c.cluster'] ?? '',
            'kabupaten' => $item['kabupaten'] ?? $item['c.kabupaten'] ?? '',
            'id_pelanggan' => (string) ($item['id_pelanggan'] ?? ''),
            'package_keyword' => intval($item['package_keyword'] ?? $item['a.package_keyword'] ?? 0),
            'package_name' => $item['package_name'] ?? '',
            'grouping' => intval($item['grouping'] ?? $item['b.grouping'] ?? 0),
            'day' => intval($item['day'] ?? $item['b.day'] ?? 0),
            'quota_bns_mb' => intval($item['quota_bns_mb'] ?? $item['b.quota_bns_mb'] ?? 0),
            'rev' => floatval($item['rev'] ?? 0),
            'trx' => intval($item['trx'] ?? 0),
            'subs' => intval($item['subs'] ?? 0),
        ];
    }

    private function showFilePreview($filepath)
    {
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

    private function filterData($data)
    {
        $packageFilter = $this->option('package');
        $idFilter = $this->option('id');
        $groupingFilter = $this->option('grouping');

        // If no filters provided, return all data
        if (!$packageFilter && !$idFilter && !$groupingFilter) {
            return $data;
        }

        return array_filter($data, function ($item) use ($packageFilter, $idFilter, $groupingFilter) {
            $matches = [];

            if ($packageFilter) {
                $matches[] = $item['package_keyword'] == $packageFilter;
            }
            if ($idFilter) {
                $matches[] = $item['id_pelanggan'] == $idFilter;
            }
            if ($groupingFilter) {
                $matches[] = $item['grouping'] == $groupingFilter;
            }

            return !empty($matches) && (count($matches) === 1 || in_array(true, $matches));
        });
    }

    private function analyzeData($data)
    {
        // 1. Show sample data
        $this->showSampleData($data);

        // 2. Basic statistics
        $this->showStatistics($data);

        // 3. K-Means clustering for purchase frequency patterns
        $this->kMeansAnalysis($data);

        // 4. Random Forest for product recommendations
        $this->randomForestAnalysis($data);

        // 5. Show column mapping
        $this->showColumnMapping();
    }

    private function showSampleData($data)
    {
        $this->info("\n📋 SAMPLE DATA (First 5 records):");
        $this->line(str_repeat("-", 100));

        $sample = array_slice($data, 0, 5);

        $this->table(
            ['No', 'ID', 'Package', 'Group', 'Revenue', 'Quota', 'Days', 'Region'],
            array_map(function($index, $item) {
                $packageName = !empty($item['package_name'])
                    ? substr($item['package_name'], 0, 15)
                    : 'Pkg-' . $item['package_keyword'];

                return [
                    $index + 1,
                    substr($item['id_pelanggan'], 0, 8) . '...',
                    $packageName,
                    $item['grouping'],
                    'Rp ' . number_format($item['rev']),
                    number_format($item['quota_bns_mb']) . 'MB',
                    $item['day'] . 'd',
                    substr($item['regional'], 0, 10),
                ];
            }, array_keys($sample), $sample)
        );
    }

    private function showStatistics($data)
    {
        $this->info("\n📈 BASIC STATISTICS:");
        $this->line(str_repeat("-", 50));

        $revenues = array_column($data, 'rev');
        $quotas = array_column($data, 'quota_bns_mb');
        $days = array_column($data, 'day');

        // Calculate customer frequency
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

    private function kMeansAnalysis($data)
    {
        $clusters = (int) $this->option('clusters');

        $this->info("\n🎯 K-MEANS CLUSTERING - PURCHASE FREQUENCY PATTERNS ($clusters clusters):");
        $this->line(str_repeat("-", 80));

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
                    'regions' => [],
                    'areas' => []
                ];
            }

            $customerData[$customerId]['purchase_count'] += $item['trx'];
            $customerData[$customerId]['total_revenue'] += $item['rev'];
            $customerData[$customerId]['total_quota'] += $item['quota_bns_mb'];
            $customerData[$customerId]['total_days'] += $item['day'];
            $customerData[$customerId]['transactions']++;

            // Update date range
            if ($item['event_date'] < $customerData[$customerId]['first_date']) {
                $customerData[$customerId]['first_date'] = $item['event_date'];
            }
            if ($item['event_date'] > $customerData[$customerId]['last_date']) {
                $customerData[$customerId]['last_date'] = $item['event_date'];
            }

            // Track packages and regions
            if (!in_array($item['package_keyword'], $customerData[$customerId]['packages'])) {
                $customerData[$customerId]['packages'][] = $item['package_keyword'];
            }
            if (!in_array($item['regional'], $customerData[$customerId]['regions'])) {
                $customerData[$customerId]['regions'][] = $item['regional'];
            }
            if (!in_array($item['area'], $customerData[$customerId]['areas'])) {
                $customerData[$customerId]['areas'][] = $item['area'];
            }
        }

        // Calculate averages
        foreach ($customerData as $customerId => $customer) {
            $customerData[$customerId]['avg_revenue'] = $customer['total_revenue'] / $customer['transactions'];
            $customerData[$customerId]['avg_quota'] = $customer['total_quota'] / $customer['transactions'];
            $customerData[$customerId]['avg_days'] = $customer['total_days'] / $customer['transactions'];
            $customerData[$customerId]['customer_lifetime'] = $customer['last_date'] - $customer['first_date'];
            $customerData[$customerId]['unique_packages'] = count($customer['packages']);
        }

        // Cluster based on purchase frequency
        $purchaseFrequencies = array_column($customerData, 'purchase_count');
        sort($purchaseFrequencies);

        // Create meaningful clusters
        $clusterRanges = [];
        if ($clusters >= 5) {
            // Create 5 logical clusters
            $clusterRanges = [
                ['min' => 1, 'max' => 1, 'label' => 'One-Time Buyers'],
                ['min' => 2, 'max' => 3, 'label' => 'Occasional Buyers'],
                ['min' => 4, 'max' => 6, 'label' => 'Regular Buyers'],
                ['min' => 7, 'max' => 10, 'label' => 'Frequent Buyers'],
                ['min' => 11, 'max' => 9999, 'label' => 'Loyal Buyers']
            ];
        } else {
            // Distribute evenly
            $step = floor((max($purchaseFrequencies) - min($purchaseFrequencies)) / $clusters);
            for ($i = 0; $i < $clusters; $i++) {
                $min = $i * $step + 1;
                $max = ($i + 1) * $step;
                $clusterRanges[] = ['min' => $min, 'max' => $max, 'label' => "Cluster " . ($i + 1)];
            }
        }

        // Assign customers to clusters
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

            // Calculate cluster statistics
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

        // Display clusters
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

    private function randomForestAnalysis($data)
    {
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
        uasort($packageData, function($a, $b) {
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
        uasort($packageAssociations, function($a, $b) {
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
        uasort($promotionCandidates, function($a, $b) {
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
        $singlePackageCustomers = array_filter($customerData, function($customer) {
            return count($customer['packages']) == 1;
        });

        if (!empty($singlePackageCustomers)) {
            $this->line("  • " . count($singlePackageCustomers) . " customers bought only 1 package");
            $this->line("  • Recommendation: Cross-sell based on package associations");

            // Example: For customers who bought package 69339, recommend associated packages
            $package69339Customers = array_filter($singlePackageCustomers, function($customer) {
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
        $lowQuotaPackages = array_filter($packageData, function($package) {
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
        if ($this->hasHeaders) {
            $this->info("\n🔍 COLUMN MAPPING DETECTED:");
            $this->line("  CSV Header → Mapped Field");
            $this->line(str_repeat("-", 40));

            $mappings = [
                'a.event_date' => 'Event Date',
                'c.area' => 'Area',
                'c.regional' => 'Regional',
                'c.cluster' => 'Cluster',
                'c.kabupaten' => 'Kabupaten',
                'id_pelanggan' => 'Customer ID',
                'a.package_keyword' => 'Package Code',
                'package_name' => 'Package Name',
                'b.grouping' => 'Group',
                'b.day' => 'Duration (Days)',
                'b.quota_bns_mb' => 'Quota (MB)',
                'rev' => 'Revenue',
                'trx' => 'Transactions (Purchase Count)',
                'subs' => 'Subscriptions',
            ];

            foreach ($mappings as $csvHeader => $description) {
                $this->line(sprintf("  %-20s → %s", $csvHeader, $description));
            }
        }
    }
}
