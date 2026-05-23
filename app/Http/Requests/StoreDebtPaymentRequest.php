<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('debt')) ?? false;
    }

    public function rules(): array
    {
        return [
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $debt = $this->route('debt');
            if (! $debt) {
                return;
            }

            if ($debt->status === 'paid') {
                $validator->errors()->add('payment_amount', 'Esta deuda ya está pagada.');

                return;
            }

            $paymentAmount = (float) $this->input('payment_amount');
            $remainingAmount = (float) $debt->total_amount - (float) ($debt->paid_amount ?? 0);

            if ($paymentAmount > $remainingAmount) {
                $validator->errors()->add(
                    'payment_amount',
                    'El pago no puede ser mayor al saldo restante (' . number_format($remainingAmount, 0, ',', '.') . ').'
                );
            }
        });
    }
}
