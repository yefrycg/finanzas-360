<?php

namespace App\Http\Requests;

use App\Models\Goal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Goal::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('user_id', $this->user()->id)),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isEmpty()) {
                $currentAmount = (float) ($this->input('current_amount') ?? 0);
                $targetAmount = (float) $this->input('target_amount');

                if ($currentAmount > $targetAmount) {
                    $validator->errors()->add('current_amount', 'El monto ahorrado no puede ser mayor al objetivo.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'target_amount.min' => 'El monto objetivo debe ser mayor a 0.',
            'due_date.after_or_equal' => 'La fecha debe ser hoy o posterior.',
        ];
    }
}
