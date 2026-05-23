<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Database\Factories\GoalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'target_amount', 'current_amount', 'due_date', 'status', 'user_id', 'category_id'])]
class Goal extends Model
{
    /** @use HasFactory<GoalFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->target_amount - (float) ($this->current_amount ?? 0);
    }

    public function getProgressAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return ((float) ($this->current_amount ?? 0) / (float) $this->target_amount) * 100;
    }

    public function recalculateStatus(): void
    {
        $this->status = ($this->current_amount ?? 0) >= $this->target_amount ? 'completed' : 'pending';
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Goal $goal) {
            if (is_null($goal->current_amount)) {
                $goal->current_amount = 0;
            }
            $goal->recalculateStatus();
        });

        static::updating(function (Goal $goal) {
            $goal->recalculateStatus();
        });
    }
}
