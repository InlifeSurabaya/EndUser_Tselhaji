<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Clusterers\Seeders\PlusPlus;
use Rubix\ML\Classifiers\RandomForest;
use Rubix\ML\Classifiers\ClassificationTree;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;

class RoamingAnalysis extends Component
{
    use WithFileUploads;

    public $file;
    public $kmeansResults = [];
    public $randomForestResults = [];
    public $uploaded = false;
    public $totalRecords = 0;
    public $packageFilter = 149;
    public $idPelangganFilter = '81708';
    public $groupingFilter = 49;
    public $clusters = 3;
    public $uploadedData = [];
    public $loading = false;

    protected $rules = [
        'file' => 'required|file'
    ];

    protected $messages = [
        'file.required' => 'Silakan pilih file terlebih dahulu',
        'file.file' => 'File tidak valid'
    ];

    public function render()
    {
        return view('livewire.roaming-analysis');
    }

    public function uploadFile()
    {
        $this->validate();

        // Validasi extension
        $allowedExtensions = ['xlsx', 'xls', 'csv', 'txt'];
        $extension = strtolower($this->file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            $this->addError('file', 'File harus berupa Excel (.xlsx, .xls) atau CSV (.csv)');
            return;
        }

        $this->loading = true;

        try {
            // Clear previous data
            $this->uploadedData = [];

            // Import data
            if (in_array($extension, ['xlsx', 'xls'])) {
                $data = Excel::toArray([], $this->file);
            } else {
                $data = $this->readCSV($this->file->getRealPath());
            }

            // Process data
            if (!empty($data[0])) {
                $rows = $data[0];

                // Determine if we have headers
                $hasHeaders = false;
                if (!empty($rows[0][0]) && !is_numeric($rows[0][0])) {
                    $hasHeaders = true;
                }

                $startRow = $hasHeaders ? 1 : 0;

                for ($i = $startRow; $i < count($rows); $i++) {
                    $row = $rows[$i];

                    // Pastikan row memiliki cukup kolom
                    if (count($row) >= 10) {
                        $this->uploadedData[] = [
                            'event_date' => $this->cleanValue($row[0] ?? 0),
                            'area' => $row[1] ?? '',
                            'regional' => $row[2] ?? '',
                            'cluster' => $row[3] ?? '',
                            'kabupaten' => $row[4] ?? '',
                            'id_pelanggan' => (string) $this->cleanValue($row[5] ?? ''),
                            'package_keyword' => intval($this->cleanValue($row[6] ?? 0)),
                            'package_name' => $row[7] ?? '',
                            'grouping' => intval($this->cleanValue($row[8] ?? 0)),
                            'day' => intval($this->cleanValue($row[9] ?? 0)),
                            'quota_bns_mb' => intval($this->cleanValue($row[10] ?? 0)),
                            'rev' => floatval($this->cleanValue($row[11] ?? 0)),
                            'trx' => intval($this->cleanValue($row[12] ?? 0)),
                            'subs' => intval($this->cleanValue($row[13] ?? 0)),
                        ];
                    }
                }
            }

            $this->totalRecords = count($this->uploadedData);
            $this->uploaded = true;

            session()->flash('success', '✅ Data berhasil diupload! Total ' . $this->totalRecords . ' records.');

        } catch (\Exception $e) {
            session()->flash('error', '❌ Error: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    private function readCSV($filepath)
    {
        $rows = [];
        if (($handle = fopen($filepath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return [$rows];
    }

    private function cleanValue($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            // Remove non-breaking spaces and other invisible characters
            $value = preg_replace('/^\pZ+|\pZ+$/u', '', $value);
            $value = str_replace(['$', ',', '"', "'"], '', $value);
        }
        return $value;
    }

    private function filterData()
    {
        return collect($this->uploadedData)->filter(function ($item) {
            return $item['package_keyword'] == $this->packageFilter ||
                   $item['id_pelanggan'] == $this->idPelangganFilter ||
                   $item['grouping'] == $this->groupingFilter;
        })->values();
    }

    public function runKMeans()
    {
        if (!$this->uploaded) {
            session()->flash('error', '⚠️ Silakan upload data terlebih dahulu');
            return;
        }

        $filteredData = $this->filterData();

        if ($filteredData->count() < $this->clusters) {
            session()->flash('error', '❌ Data tidak cukup untuk clustering. Data ditemukan: ' . $filteredData->count());
            return;
        }

        $this->loading = true;

        try {
            // Prepare features for K-Means
            $samples = [];
            $labels = [];

            foreach ($filteredData as $data) {
                $samples[] = [
                    floatval($data['rev']),
                    floatval($data['quota_bns_mb']),
                    floatval($data['day']),
                    floatval($data['trx']),
                ];
                $labels[] = $data['id_pelanggan'];
            }

            // Create dataset
            $dataset = new Unlabeled($samples);

            // Create K-Means clusterer
            $kmeans = new KMeans($this->clusters, 100, 1.0, new PlusPlus());

            // Train and predict
            $kmeans->train($dataset);
            $predictions = $kmeans->predict($dataset);

            // Organize results
            $this->kmeansResults = [];
            for ($i = 0; $i < count($predictions); $i++) {
                $this->kmeansResults[] = [
                    'id_pelanggan' => $labels[$i],
                    'cluster' => $predictions[$i],
                    'rev' => $samples[$i][0],
                    'quota' => $samples[$i][1],
                    'day' => $samples[$i][2],
                    'trx' => $samples[$i][3],
                    'package_name' => $filteredData[$i]['package_name'] ?? 'N/A',
                    'area' => $filteredData[$i]['area'] ?? 'N/A',
                ];
            }

            session()->flash('success', '✅ K-Means clustering berhasil! ' . count($this->kmeansResults) . ' data diproses.');

        } catch (\Exception $e) {
            session()->flash('error', '❌ Error dalam K-Means: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function runRandomForest()
    {
        if (!$this->uploaded) {
            session()->flash('error', '⚠️ Silakan upload data terlebih dahulu');
            return;
        }

        $filteredData = $this->filterData();

        if ($filteredData->count() < 10) {
            session()->flash('error', '❌ Data tidak cukup untuk Random Forest. Minimal 10 data.');
            return;
        }

        $this->loading = true;

        try {
            // Prepare features and labels
            $samples = [];
            $labels = [];

            // Create classification: 1 = high revenue, 0 = low revenue
            $revenueThreshold = $filteredData->avg('rev');

            foreach ($filteredData as $data) {
                $samples[] = [
                    floatval($data['quota_bns_mb']),
                    floatval($data['day']),
                    floatval($data['trx']),
                    floatval($data['subs']),
                    floatval($data['grouping']),
                ];

                // Binary classification based on revenue
                $labels[] = $data['rev'] > $revenueThreshold ? 'high_revenue' : 'low_revenue';
            }

            // Create labeled dataset
            $dataset = new Labeled($samples, $labels);

            // Split dataset (80% training, 20% testing)
            $split = $dataset->stratifiedSplit(0.8);
            $training = $split[0];
            $testing = $split[1];

            // Create Random Forest classifier
            $estimator = new RandomForest(new ClassificationTree(10), 100, 0.1);

            // Train the model
            $estimator->train($training);

            // Make predictions
            $predictions = $estimator->predict($testing);

            // Calculate accuracy
            $actual = $testing->labels();
            $correct = 0;

            for ($i = 0; $i < count($predictions); $i++) {
                if ($predictions[$i] == $actual[$i]) {
                    $correct++;
                }
            }

            $accuracy = count($predictions) > 0 ? ($correct / count($predictions)) * 100 : 0;

            // Get sample predictions
            $samplePredictions = [];
            $testSamples = $testing->samples();

            for ($i = 0; $i < min(5, count($predictions)); $i++) {
                $samplePredictions[] = [
                    'actual' => $actual[$i],
                    'predicted' => $predictions[$i],
                    'quota' => $testSamples[$i][0],
                    'day' => $testSamples[$i][1],
                ];
            }

            // Prepare results
            $this->randomForestResults = [
                'accuracy' => round($accuracy, 2),
                'total_samples' => count($samples),
                'training_samples' => count($training),
                'testing_samples' => count($testing),
                'revenue_threshold' => round($revenueThreshold, 2),
                'sample_predictions' => $samplePredictions,
                'feature_importance' => [
                    'quota_bns_mb' => 'Quota Bonus MB',
                    'day' => 'Duration (Days)',
                    'trx' => 'Transaction Count',
                    'subs' => 'Subscriptions',
                    'grouping' => 'Grouping Category',
                ],
            ];

            session()->flash('success', '✅ Random Forest berhasil dijalankan! Akurasi: ' . $accuracy . '%');

        } catch (\Exception $e) {
            session()->flash('error', '❌ Error dalam Random Forest: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function exportKMeans()
    {
        $data = $this->kmeansResults;
        $filename = 'kmeans_results_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Header with BOM for Excel
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID Pelanggan', 'Cluster', 'Revenue', 'Quota (MB)', 'Days', 'Transactions', 'Package', 'Area']);

            // Data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row['id_pelanggan'],
                    $row['cluster'],
                    $row['rev'],
                    $row['quota'],
                    $row['day'],
                    $row['trx'],
                    $row['package_name'],
                    $row['area'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetAll()
    {
        $this->resetExcept(['packageFilter', 'idPelangganFilter', 'groupingFilter', 'clusters']);
        session()->flash('info', 'Sistem telah direset');
    }


}
