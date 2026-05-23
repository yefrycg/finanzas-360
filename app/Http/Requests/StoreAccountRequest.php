<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Account::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:general_account,cash,checking_account,credit_card,savings_account'],
            'current_balance' => ['required', 'numeric', 'min:0'],
            'credit_limit' => ['nullable', 'numeric', 'min:0', 'required_if:type,credit_card'],
        ];
    }
}
