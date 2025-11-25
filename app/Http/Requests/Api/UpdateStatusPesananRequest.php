<?php

namespace App\Http\Requests\Api;

use App\Enum\TransactionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusPesananRequest extends FormRequest
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
            'transaction_number' => 'required|string|exists:transactions,transaction_number',
            'status' => 'required|in:' . TransactionStatusEnum::DENY->value . ',' . TransactionStatusEnum::SETTLEMENT   ->value,
        ];
    }
}
