<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDebtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('debt')) ?? false;
    }

    public function rules(): array
    {
        return [
            'lender' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isEmpty()) {
                $totalAmount = (float) $this->total_amount;
                $paidAmount = (float) ($this->paid_amount ?? 0);

                if ($paidAmount > $totalAmount) {
                    $validator->errors()->add('paid_amount', 'El monto pagado no puede ser mayor al monto total.');
                }
            }
        });
    }
}
