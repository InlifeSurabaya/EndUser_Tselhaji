<?php

namespace App\Http\Requests\Api;

use App\Enum\TransactionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class GetAllTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => 'nullable|integer|between:1,100',
            'sort' => 'nullable|string|in:asc,desc',
            'filter_status' => 'nullable|string|in:' . TransactionStatusEnum::DENY->value . ',' . TransactionStatusEnum::SETTLEMENT->value . ',' . TransactionStatusEnum::PROSES->value, '.' . TransactionStatusEnum::PENDING->value,
        ];
    }
}
