<?php

namespace App\Policies;

use App\Models\Operation;
use App\Models\User;

class OperationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Operation $operation): bool
    {
        return $operation->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Operation $operation): bool
    {
        return $operation->user_id === $user->id;
    }

    public function delete(User $user, Operation $operation): bool
    {
        return $operation->user_id === $user->id;
    }
}
