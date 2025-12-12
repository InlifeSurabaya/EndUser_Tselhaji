<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetAllTransactionRequest;
use App\Http\Requests\Api\UpdateStatusPesananRequest;
use App\Http\Resources\Api\FailedResponseResource;
use App\Http\Resources\Api\SuccessResponseResource;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function updateStatusPesanan(UpdateStatusPesananRequest $request)
    {
        try {
            $response = $this->transactionService->updateStatusTransaction($request->transaction_number, $request->status);

            return new SuccessResponseResource($response);
        } catch (\Throwable $e) {
            Log::error('Failed to update status pesanan: ' . $e->getMessage());

            return new FailedResponseResource('Failed to update status pesanan');
        }
    }


    public function getAllTransaction(GetAllTransactionRequest $request)
    {
        try {
            Log::info('getall transaction called');
            $data = $this->transactionService->getAllTransaction($request->per_page, $request->sort, $request->filter_status );

            return new SuccessResponseResource($data);
        } catch (\Throwable $e) {
            Log::error('Failed to get all transaction: ' . $e->getMessage());

            return new FailedResponseResource('Failed to get all transaction: ' . $e->getMessage());
        }
    }
}
