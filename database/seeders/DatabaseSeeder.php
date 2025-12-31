<?php

namespace Database\Seeders;

use App\Enum\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(10)->create();

        $this->call([
            CreateRole::class,
            CategoryCountryProductSeeder::class,
            RealProductSeeder::class,
            UserProfileSeeder::class,
            VoucherSeeder::class,
        ]);

        // User account
        $user = User::create([
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole(RoleEnum::USER->value);

        // Super admin account
        $superAdmin = User::create([
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);
    }
}
