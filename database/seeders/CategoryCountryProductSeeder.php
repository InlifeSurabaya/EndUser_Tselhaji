<?php

namespace Database\Seeders;

use App\Models\CategoryCountryProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Rinvex\Country\CountryLoader;

class CategoryCountryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = CountryLoader::countries();

        foreach ($countries as $code => $country) {
            CategoryCountryProduct::create([
                'name' => is_array($country) ? ($country['name']['common'] ?? $country['name']) : $country,
                'country_code' => strtolower($code),
            ]);
        }
    }
}
