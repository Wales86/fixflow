<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return false;
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return false;
    }

    public function restore(User $user, Vehicle $vehicle): bool
    {
        return false;
    }

    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        return false;
    }
}
