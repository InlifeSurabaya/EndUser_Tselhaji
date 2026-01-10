<?php

namespace Database\Seeders;

use App\Enum\UserSegmentEnum;
use App\Models\HargaSpesial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSegmentPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserSegmentEnum::cases() as $segment) {
            HargaSpesial::updateOrCreate(
                ['kategori_harga_spesial' => $segment->value],
                ['potongan_product' => 0]
            );
        }
    }
}
