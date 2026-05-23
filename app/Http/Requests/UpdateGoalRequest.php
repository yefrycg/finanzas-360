<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('goal')) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isEmpty()) {
                $category = $this->user()->categories()->find($this->input('category_id'));
                if (! $category) {
                    $validator->errors()->add('category_id', 'La categoría seleccionada debe pertenecerte.');
                }

                $goal = $this->route('goal');
                $currentAmount = (float) ($this->input('current_amount') ?? $goal->current_amount);
                $targetAmount = (float) $this->input('target_amount');

                if ($currentAmount > $targetAmount) {
                    $validator->errors()->add('current_amount', 'El monto ahorrado no puede ser mayor al objetivo.');
                }

                if ($targetAmount < $goal->current_amount) {
                    $validator->errors()->add('target_amount', 'El monto objetivo no puede ser menor al monto actual ahorrado.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'target_amount.min' => 'El monto objetivo debe ser mayor a 0.',
        ];
    }
}
