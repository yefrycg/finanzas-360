<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        $account = $this->route('account');

        if (! $account instanceof Account) {
            return false;
        }

        return $this->user()?->can('update', $account) ?? false;
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
