<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Category;
use App\Models\Operation;
use Illuminate\Foundation\Http\FormRequest;

class StoreOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Operation::class) ?? false;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999999999.99'],
            'date_time' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'note' => ['nullable', 'string', 'max:500'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $categoryId = $this->input('category_id');
            $accountId = $this->input('account_id');
            $type = $this->input('type');
            $amount = (float) $this->input('amount');
            $userId = $this->user()->id;

            if ($categoryId) {
                $category = Category::where('id', $categoryId)
                    ->where('user_id', $userId)
                    ->first();

                if (! $category) {
                    $validator->errors()->add('category_id', 'La categoría no te pertenece.');
                } elseif ($category->type !== $type) {
                    $validator->errors()->add('category_id', 'El tipo de categoría debe coincidir con el tipo de operación.');
                }
            }

            if ($accountId) {
                $account = Account::where('id', $accountId)
                    ->where('user_id', $userId)
                    ->first();

                if (! $account) {
                    $validator->errors()->add('account_id', 'La cuenta no te pertenece.');
                } elseif ($type === 'expense' && $amount > 0 && (float) $account->current_balance < $amount) {
                    $validator->errors()->add('amount', 'El monto excede el saldo disponible de esta cuenta.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'El monto debe ser mayor a 0.',
            'amount.numeric' => 'El monto debe ser un número válido.',
            'category_id.required' => 'La categoría es requerida.',
            'account_id.required' => 'La cuenta es requerida.',
        ];
    }
}
