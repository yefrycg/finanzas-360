<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use DatabaseFactories\DebtFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lender', 'total_amount', 'paid_amount', 'start_date', 'end_date', 'status', 'user_id'])]
class Debt extends Model
{
    /** @use HasFactory<DebtFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->total_amount - (float) ($this->paid_amount ?? 0);
    }

    public function getProgressAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 0;
        }

        return ((float) ($this->paid_amount ?? 0) / (float) $this->total_amount) * 100;
    }

    public function recalculateStatus(): void
    {
        $this->status = ($this->paid_amount ?? 0) >= $this->total_amount ? 'paid' : 'no_paid';
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Debt $debt) {
            if (is_null($debt->paid_amount)) {
                $debt->paid_amount = 0;
            }
            $debt->recalculateStatus();
        });

        static::updating(function (Debt $debt) {
            $debt->recalculateStatus();
        });
    }
}
