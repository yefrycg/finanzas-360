<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToUser
{
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}