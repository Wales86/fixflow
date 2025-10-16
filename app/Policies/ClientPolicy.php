<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function delete(User $user, Client $client): bool
    {
        return false;
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
