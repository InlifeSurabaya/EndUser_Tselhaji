<?php

namespace Database\Seeders;

use App\Livewire\User\UserProfile;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenereateAccountAndTransaction extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::create([
            'email' => 'user1@example.com',
            'password' => Hash::make('12345678'),
            'is_new' => 0,
        ]);

        UserProfile::create([
            'user_id' => $user1->id,
            'fullname' => 'User Satu',
            'phone' => '0811111111',
        ]);

        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);

            $order = Order::create([
                'uuid' => Str::uuid(),
                'order_number' => 'ORD-U1-' . Str::random(8),
                'user_id' => $user1->id,
                'product_id' => 1,
                'category_country_product_id' => 1,
                'original_price' => 100000,
                'discount_amount' => 0,
                'final_price' => 100000,
                'status' => 'settlement',
                'settlement_time' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            Transaction::create([
                'transaction_number' => 'TRX-U1-' . Str::random(10),
                'order_id' => $order->id,
                'user_id' => $user1->id,
                'gross_amount' => 100000,
                'net_amount' => 100000,
                'payment_type' => 'qris',
                'status' => 'settlement',
                'transaction_time' => $date,
                'settlement_time' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        $user2 = User::create([
            'email' => 'user2@example.com',
            'password' => Hash::make('12345678'),
            'is_new' => 0,
        ]);

        UserProfile::create([
            'user_id' => $user2->id,
            'fullname' => 'User Dua',
            'phone' => '0822222222',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $date = Carbon::now()->subMonth()->addDays($i * 3);

            $order = Order::create([
                'uuid' => Str::uuid(),
                'order_number' => 'ORD-U2-' . Str::random(8),
                'user_id' => $user2->id,
                'product_id' => 1,
                'category_country_product_id' => 1,
                'original_price' => 150000,
                'discount_amount' => 10000,
                'final_price' => 140000,
                'status' => 'settlement',
                'settlement_time' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            Transaction::create([
                'transaction_number' => 'TRX-U2-' . Str::random(10),
                'order_id' => $order->id,
                'user_id' => $user2->id,
                'gross_amount' => 150000,
                'net_amount' => 140000,
                'payment_type' => 'qris',
                'status' => 'settlement',
                'transaction_time' => $date,
                'settlement_time' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | AKUN 3 - Akun baru (tanpa transaksi)
        |--------------------------------------------------------------------------
        */
        $user3 = User::create([
            'email' => 'user3@example.com',
            'password' => Hash::make('12345678'),
            'is_new' => 1,
        ]);

        UserProfile::create([
            'user_id' => $user3->id,
            'fullname' => 'User Tiga',
            'phone' => '0833333333',
        ]);
    }
}
