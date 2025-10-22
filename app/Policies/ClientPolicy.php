<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::VIEW_CLIENTS->value);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->can(UserPermission::VIEW_CLIENTS->value);
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::CREATE_CLIENTS->value);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->can(UserPermission::UPDATE_CLIENTS->value);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->can(UserPermission::DELETE_CLIENTS->value);
    }

    public function restore(User $user, Client $client): bool
    {
        return false;
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }
}
