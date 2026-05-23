<?php

namespace App\Policies;

use App\Models\Debt;
use App\Models\User;

class DebtPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Debt $debt): bool
    {
        return $debt->user_id === $user->id;
    }

    public function delete(User $user, Debt $debt): bool
    {
        return $debt->user_id === $user->id;
    }
}
