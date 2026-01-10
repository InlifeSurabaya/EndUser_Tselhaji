<?php

namespace App\Enum;

enum UserSegmentEnum: string
{
    // User sudah membeli > 5 tahun
    case LOYAL = 'loyal';

    // user yang membeli 2x sebulan
    case ACTIVE = 'active';

    // user baru
    case NEW = 'new';



    public function label(): string
    {
        return match($this) {
            self::LOYAL => 'Pelanggan Setia (Loyal)',
            self::ACTIVE => 'Pelanggan Rutin (Aktif)',
            self::NEW  => 'Pelanggan Baru',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOYAL => 'bg-purple-100 text-purple-800', // Warna Premium
            self::ACTIVE => 'bg-blue-100 text-blue-800',     // Warna Bisnis/Aktif
            self::NEW  => 'bg-green-100 text-green-800',   // Warna Start/Mulai
        };
    }

    public function description(): string
    {
        return match($this) {
            self::LOYAL => 'User yang telah melakukan pembelian lebih dari 5x pada tahun sebelumnya.',
            self::ACTIVE => 'User yang rutin bertransaksi minimal 2x dalam satu bulan.',
            self::NEW => 'User yang baru mendaftar dan belum pernah melakukan transaksi.',
        };
    }
}
