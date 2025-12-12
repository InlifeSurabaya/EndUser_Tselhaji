<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GenerateNumberTrait
{
    /**
     * Boot the trait.
     * Automatically generate the number when a new model is created.
     */
    public static function bootGeneratesInvoiceNumber(): void
    {
        static::creating(function (Model $model) {
            // Cek untuk memastikan nomor tidak di-overwrite jika sudah di-set manual
            if (empty($model->{$model->getInvoiceNumberField()})) {
                $model->{$model->getInvoiceNumberField()} = $model->generateInvoiceNumber();
            }
        });
    }

    /**
     * Generate a unique, random number.
     * Format: PREFIX/YYYYMM/RANDOM_STRING
     * Example: INV/202510/A83B1N9X
     * This function loops until a unique number is found.
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = $this->getInvoicePrefix();
        $length = $this->getRandomStringLength();
        $field = $this->getInvoiceNumberField();
        $datePart = date('Ym');

        do {
            // Generate string acak (alpha-numeric) dan ubah ke huruf besar
            $randomPart = Str::upper(Str::random($length));

            // Gabungkan menjadi nomor baru
            $newNumber = "{$prefix}/{$datePart}/{$randomPart}";

            // Cek apakah nomor ini sudah ada di database
            $exists = self::where($field, $newNumber)->exists();

        } while ($exists); // Ulangi jika nomor sudah ada

        // Kembalikan nomor yang unik
        return $newNumber;
    }

    /**
     * Get the number field name from the model.
     * Default: 'invoice_number'
     */
    protected function getInvoiceNumberField(): string
    {
        return property_exists($this, 'invoiceNumberField') ? $this->invoiceNumberField : 'invoice_number';
    }

    /**
     * Get the prefix for the number from the model.
     * Default: 'INV'
     */
    protected function getInvoicePrefix(): string
    {
        return property_exists($this, 'invoicePrefix') ? $this->invoicePrefix : 'INV';
    }

    /**
     * Get the length for the random string from the model.
     * Default: 8
     */
    protected function getRandomStringLength(): int
    {
        // Ganti nama properti dari 'invoicePadding' ke 'randomLength' jika Anda mau
        return property_exists($this, 'randomLength') ? $this->randomLength : 8;
    }
}
