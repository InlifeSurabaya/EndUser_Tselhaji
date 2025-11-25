<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function updateStatusTransaction($transactionNumber = null, $status = null)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($transactionNumber);

            $transaction->update([
                'status' => $status,
            ]);

            DB::commit();
            return $transaction;
        } catch (\Throwable $e) {
            DB::rollBack();
        }
    }

    public function getAllTransaction($perPage = 10, $sort = null, $filterStatus = null)
    {
        $query = Transaction::query();

        if ($sort) {
            $query->orderBy('created_at', $sort);
        }

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        return $query->paginate($perPage);
    }
}
