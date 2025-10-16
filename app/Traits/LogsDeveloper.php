<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogsDeveloper
{
    /**
     * Mencatat exception dengan detail teknis lengkap ke channel 'critical_errors'.
     *
     * @param \Throwable $e Exception yang ditangkap.
     * @param array $additionalContext Informasi kustom tambahan dari developer.
     * @return void
     */
    protected function logErrorForDeveloper(\Throwable $e, array $additionalContext = []): void
    {
        $context = [
            // --- Konteks Permintaan & Pengguna ---
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id() ?? 'Guest',
            'input_data' => request()->except(['password', 'password_confirmation']), // Menyaring data sensitif

            // --- Konteks Detail Exception ---
            'exception_class' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),

            // --- Konteks Tambahan dari Developer ---
            'custom_context' => $additionalContext,
        ];

        // Mengirim semua informasi ini ke channel log yang sudah kita siapkan
        Log::channel('critical_errors')->error("An exception occurred", $context);
    }
}
