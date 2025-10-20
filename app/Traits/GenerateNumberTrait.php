<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait GenerateNumberTrait
{
    /**
     * Boot the trait.
     * Automatically generate the invoice number when a new model is created.
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
     * Generate a unique, sequential invoice number.
     * Format: PREFIX/YYYYMM/SEQUENTIAL_NUMBER
     * Example: INV/202510/0001
     * The sequential number resets every month.
     *
     * @return string
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = $this->getInvoicePrefix();
        $padding = $this->getInvoicePadding();
        $field = $this->getInvoiceNumberField();
        $datePart = date('Ym');

        // Find the last record for the current month
        $lastRecord = self::where($field, 'LIKE', "{$prefix}/{$datePart}/%")
            ->orderBy($field, 'desc')
            ->first();

        $sequence = 1; // Default sequence

        if ($lastRecord) {
            // Extract the sequence number from the last record
            $lastNumber = explode('/', $lastRecord->{$field});
            $lastSequence = (int) end($lastNumber);
            $sequence = $lastSequence + 1;
        }

        // Pad the sequence with leading zeros
        $paddedSequence = str_pad($sequence, $padding, '0', STR_PAD_LEFT);

        // Construct the final invoice number
        return "{$prefix}/{$datePart}/{$paddedSequence}";
    }

    /**
     * Get the invoice number field name from the model.
     * Default: 'invoice_number'
     *
     * @return string
     */
    protected function getInvoiceNumberField(): string
    {
        return property_exists($this, 'invoiceNumberField') ? $this->invoiceNumberField : 'invoice_number';
    }

    /**
     * Get the prefix for the invoice number from the model.
     * Default: 'INV'
     *
     * @return string
     */
    protected function getInvoicePrefix(): string
    {
        return property_exists($this, 'invoicePrefix') ? $this->invoicePrefix : 'INV';
    }

    /**
     * Get the padding length for the sequence from the model.
     * Default: 4 (e.g., 0001)
     *
     * @return int
     */
    protected function getInvoicePadding(): int
    {
        return property_exists($this, 'invoicePadding') ? $this->invoicePadding : 4;
    }
}
