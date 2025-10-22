<?php

namespace App\Policies;

use App\Models\RepairOrder;
use App\Models\User;

class RepairOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view repair orders');
    }

    public function view(User $user, RepairOrder $repairOrder): bool
    {
        // Must be from same workshop
        if ($user->workshop_id !== $repairOrder->workshop_id) {
            return false;
        }

        // Owner and Office can view via permission
        // Mechanics can view repair orders they work on
        return $user->can('view repair orders') || $user->hasRole('Mechanic');
    }

    public function create(User $user): bool
    {
        return $user->can('create repair orders');
    }

    public function update(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->can('update repair orders');
    }

    public function delete(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->can('delete repair orders');
    }

    public function updateStatus(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->can('update repair order status');
    }

    public function restore(User $user, RepairOrder $repairOrder): bool
    {
        return false;
    }

    public function forceDelete(User $user, RepairOrder $repairOrder): bool
    {
        return false;
    }
}
