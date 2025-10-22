<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view clients');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->can('view clients');
    }

    public function create(User $user): bool
    {
        return $user->can('create clients');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->can('update clients');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->can('delete clients');
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
