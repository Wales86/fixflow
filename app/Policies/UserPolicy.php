<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::VIEW_USERS->value);
    }

    public function view(User $user, User $model): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::CREATE_USERS->value);
    }

    public function update(User $user, User $model): bool
    {
        return $user->workshop_id === $model->workshop_id
            && $user->can(UserPermission::UPDATE_USERS->value);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->workshop_id === $model->workshop_id
            && $user->can(UserPermission::DELETE_USERS->value);
    }

    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
