<?php

namespace App\Http\Requests;

use App\Models\Budget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('budget')) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'period' => ['required', 'in:daily,weekly,monthly,annually'],
            'limit_amount' => ['required', 'numeric', 'min:0'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('categories', 'id')->where(fn($query) => $query
                    ->where('user_id', $this->user()->id)
                    ->where('type', 'expense')),
            ],
        ];
    }
}
