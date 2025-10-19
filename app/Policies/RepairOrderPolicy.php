<?php

namespace App\Policies;

use App\Models\RepairOrder;
use App\Models\User;

class RepairOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function view(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->hasAnyRole(['Owner', 'Office', 'Mechanic']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function update(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->hasAnyRole(['Owner', 'Office']);
    }

    public function delete(User $user, RepairOrder $repairOrder): bool
    {
        return $user->workshop_id === $repairOrder->workshop_id
            && $user->hasAnyRole(['Owner']);
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
