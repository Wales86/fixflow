<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\TimeEntry;
use App\Models\User;

class TimeEntryPolicy
{
    public function viewAny(User $user): bool
    {
        // Everyone can view time entries (through RepairOrder view)
        return true;
    }

    public function view(User $user, TimeEntry $timeEntry): bool
    {
        // Must be from same workshop
        return $user->workshop_id === $timeEntry->repairOrder->workshop_id;
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::CREATE_TIME_ENTRIES->value);
    }

    public function update(User $user, TimeEntry $timeEntry): bool
    {
        return $user->workshop_id === $timeEntry->repairOrder->workshop_id
            && $user->can(UserPermission::UPDATE_TIME_ENTRIES->value);
    }

    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        return $user->workshop_id === $timeEntry->repairOrder->workshop_id
            && $user->can(UserPermission::DELETE_TIME_ENTRIES->value);
    }

    public function restore(User $user, TimeEntry $timeEntry): bool
    {
        return false;
    }

    public function forceDelete(User $user, TimeEntry $timeEntry): bool
    {
        return false;
    }
}
