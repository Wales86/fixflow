<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\Mechanic;
use App\Models\User;

class MechanicPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::VIEW_MECHANICS->value);
    }

    public function view(User $user, Mechanic $mechanic): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::CREATE_MECHANICS->value);
    }

    public function update(User $user, Mechanic $mechanic): bool
    {
        return false;
    }

    public function delete(User $user, Mechanic $mechanic): bool
    {
        return false;
    }

    public function restore(User $user, Mechanic $mechanic): bool
    {
        return false;
    }

    public function forceDelete(User $user, Mechanic $mechanic): bool
    {
        return false;
    }
}
