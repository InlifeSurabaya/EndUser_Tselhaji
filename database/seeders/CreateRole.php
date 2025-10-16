<?php

namespace Database\Seeders;

use App\Enum\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RoleEnum::cases() as $case) {
            Role::create([
                'name' => $case->value,
            ]);
        }
    }
}
