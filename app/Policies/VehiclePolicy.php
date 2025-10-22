<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::VIEW_VEHICLES->value);
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        // Must be from same workshop
        if ($user->workshop_id !== $vehicle->workshop_id) {
            return false;
        }

        // Owner and Office can view via permission
        // Mechanics can view vehicles they work on (assigned repair orders)
        return $user->can(UserPermission::VIEW_VEHICLES->value) || $user->hasRole('Mechanic');
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::CREATE_VEHICLES->value);
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->workshop_id === $vehicle->workshop_id
            && $user->can(UserPermission::UPDATE_VEHICLES->value);
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->workshop_id === $vehicle->workshop_id
            && $user->can(UserPermission::DELETE_VEHICLES->value);
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
