<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Database\Factories\BudgetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

#[Fillable(['name', 'period', 'limit_amount', 'user_id'])]
class Budget extends Model
{
    /** @use HasFactory<BudgetFactory> */
    use HasFactory, BelongsToUser;

    protected function casts(): array
    {
        return [
            'limit_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'budget_category');
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function currentPeriodDateRange(?Carbon $now = null): array
    {
        $now = ($now ?? now())->copy();

        return match ($this->period) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'weekly' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'annually' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }
}
