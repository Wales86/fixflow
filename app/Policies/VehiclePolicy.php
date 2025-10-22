<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view vehicles');
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        // Must be from same workshop
        if ($user->workshop_id !== $vehicle->workshop_id) {
            return false;
        }

        // Owner and Office can view via permission
        // Mechanics can view vehicles they work on (assigned repair orders)
        return $user->can('view vehicles') || $user->hasRole('Mechanic');
    }

    public function create(User $user): bool
    {
        return $user->can('create vehicles');
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->workshop_id === $vehicle->workshop_id
            && $user->can('update vehicles');
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->workshop_id === $vehicle->workshop_id
            && $user->can('delete vehicles');
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
