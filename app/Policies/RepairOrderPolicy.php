<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\RepairOrder;
use App\Models\User;

class RepairOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::VIEW_REPAIR_ORDERS->value);
    }

    public function viewAnyMechanic(User $user): bool
    {
        return $user->can(UserPermission::VIEW_REPAIR_ORDERS_MECHANIC->value);
    }

    public function view(User $user, RepairOrder $repairOrder): bool
    {
        // Must be from same workshop
        if ($user->workshop_id !== $repairOrder->workshop_id) {
            return false;
        }

        // Owner and Office can view via permission
        // Mechanics can view repair orders they work on
        return $user->can(UserPermission::VIEW_REPAIR_ORDERS->value) || $user->hasRole('Mechanic');
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::CREATE_REPAIR_ORDERS->value);
    }

    public function update(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->can(UserPermission::UPDATE_REPAIR_ORDERS->value);
    }

    public function delete(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->can(UserPermission::DELETE_REPAIR_ORDERS->value);
    }

    public function updateStatus(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->can(UserPermission::UPDATE_REPAIR_ORDER_STATUS->value);
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
