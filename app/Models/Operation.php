<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Database\Factories\OperationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['amount', 'date_time', 'type', 'note', 'user_id', 'category_id', 'account_id'])]
class Operation extends Model
{
    /** @use HasFactory<OperationFactory> */
    use BelongsToUser, HasFactory;

    protected function casts(): array
    {
        return [
            'date_time' => 'datetime',
            'amount' => 'decimal:2',
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

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
