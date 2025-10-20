<?php

namespace App\Policies;

use App\Models\InternalNote;
use App\Models\User;

class InternalNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function view(User $user, InternalNote $internalNote): bool
    {
        $notable = $internalNote->notable;

        if (! $notable || ! isset($notable->workshop_id)) {
            return false;
        }

        return $user->workshop_id === $notable->workshop_id
            && $user->hasAnyRole(['Owner', 'Office', 'Mechanic']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Owner', 'Office']);
    }

    public function update(User $user, InternalNote $internalNote): bool
    {
        $notable = $internalNote->notable;

        if (! $notable || ! isset($notable->workshop_id)) {
            return false;
        }

        return $user->workshop_id === $notable->workshop_id
            && $user->hasAnyRole(['Owner', 'Office']);
    }

    public function delete(User $user, InternalNote $internalNote): bool
    {
        $notable = $internalNote->notable;

        if (! $notable || ! isset($notable->workshop_id)) {
            return false;
        }

        return $user->workshop_id === $notable->workshop_id
            && $user->hasRole('Owner');
    }

    public function restore(User $user, InternalNote $internalNote): bool
    {
        return false;
    }

    public function forceDelete(User $user, InternalNote $internalNote): bool
    {
        return false;
    }
}
