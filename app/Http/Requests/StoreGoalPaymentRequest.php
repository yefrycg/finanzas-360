<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('goal')) ?? false;
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

            $goal = $this->route('goal');
            if (! $goal) {
                return;
            }

            if ($goal->status === 'completed') {
                $validator->errors()->add('payment_amount', 'Esta meta ya está completada.');

                return;
            }

            $paymentAmount = (float) $this->input('payment_amount');
            $remainingAmount = (float) $goal->target_amount - (float) ($goal->current_amount ?? 0);

            if ($paymentAmount > $remainingAmount) {
                $validator->errors()->add(
                    'payment_amount',
                    'El pago no puede ser mayor al monto restante ('.number_format($remainingAmount, 0, ',', '.').').'
                );
            }
        });
    }
}
