<?php

namespace Database\Seeders;

use App\Models\CategoryCountryProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryCountryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryCountryProduct::factory()->count(10)->create();
    }
}
