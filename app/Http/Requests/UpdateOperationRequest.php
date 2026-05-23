<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Category;
use App\Models\Operation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $operation = $this->route('operation');

        if (! $operation instanceof Operation) {
            return false;
        }

        return $this->user()?->can('update', $operation) ?? false;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
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
            $operation = $this->route('operation');
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

            if ($accountId && $type === 'expense') {
                $account = Account::where('id', $accountId)
                    ->where('user_id', $userId)
                    ->first();

                if (! $account) {
                    $validator->errors()->add('account_id', 'La cuenta no te pertenece.');
                    return;
                }

                $effectiveBalance = (float) $account->current_balance;

                if ($operation && $operation->account_id == $accountId) {
                    if ($operation->type === 'income') {
                        $effectiveBalance -= (float) $operation->amount;
                    } else {
                        $effectiveBalance = (float) $account->current_balance + (float) $operation->amount;
                    }
                }

                if ($amount > $effectiveBalance) {
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
