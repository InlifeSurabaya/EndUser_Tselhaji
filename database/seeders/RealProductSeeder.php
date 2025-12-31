<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class RealProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/data-product.csv');

        if (!File::exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan di: $csvPath");
            return;
        }

        // Baca file CSV
        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file);

        $uniqueProducts = [];

        while (($row = fgetcsv($file)) !== false) {

            $name = $row[7] ?? 'Unknown Product';
            $validity = (int) ($row[8] ?? 7);
            $quotaMb = (int) ($row[9] ?? 0);
            $price = (int) ($row[10] ?? 0);

            // Konversi MB ke GB
            $quotaGb = $quotaMb > 0 ? ceil($quotaMb / 1024) : 0;

            $uniqueKey = "{$name}-{$price}-{$quotaGb}-{$validity}";

            if (!isset($uniqueProducts[$uniqueKey])) {
                $uniqueProducts[$uniqueKey] = [
                    'country_id'    => rand(1, 250),
                    'name'          => $name,
                    'detail'        => "Paket Roaming Internasional {$name} dengan masa aktif {$validity} hari.",
                    'price'         => $price,
                    'quota_amount'  => $quotaGb,
                    'quota_type'    => 'GB',
                    'validity_days' => $validity,
                    'discount'      => 0,
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        fclose($file);

        // 3. Insert data ke Database
        // Menggunakan chunk agar lebih hemat memori jika data banyak
        $chunks = array_chunk($uniqueProducts, 100);

        foreach ($chunks as $chunk) {
            Product::insert($chunk);
        }

        $this->command->info('Berhasil import ' . count($uniqueProducts) . ' produk unik dari CSV.');
    }
}
