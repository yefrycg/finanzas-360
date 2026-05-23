<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'type', 'current_balance', 'credit_limit', 'color', 'icon', 'user_id'])]
class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory, BelongsToUser;

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Account $account) {
            if (! $account->color) {
                $account->fill([
                    'color' => static::getColorByType($account->type),
                ]);
            }
            if (! $account->icon) {
                $account->fill([
                    'icon' => static::getIconByType($account->type),
                ]);
            }
        });
    }

    public static function getColorByType(string $type): string
    {
        return match ($type) {
            'general_account' => '#6B7280',
            'cash' => '#22C55E',
            'checking_account' => '#3B82F6',
            'credit_card' => '#EF4444',
            'savings_account' => '#A855F7',
            default => '#6B7280',
        };
    }

    public static function getIconByType(string $type): string
    {
        return match ($type) {
            'general_account' => 'fas fa-wallet',
            'cash' => 'fas fa-money-bill',
            'checking_account' => 'fas fa-building-columns',
            'credit_card' => 'fas fa-credit-card',
            'savings_account' => 'fas fa-piggy-bank',
            default => 'fas fa-wallet',
        };
    }

    public static function getTypeLabel(string $type): string
    {
        return match ($type) {
            'general_account' => 'General',
            'cash' => 'Efectivo',
            'checking_account' => 'Cuenta Corriente',
            'credit_card' => 'Tarjeta de Crédito',
            'savings_account' => 'Ahorros',
            default => $type,
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
