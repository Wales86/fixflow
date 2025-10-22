<?php

namespace App\Policies;

use App\Models\InternalNote;
use App\Models\User;

class InternalNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view internal notes');
    }

    public function view(User $user, InternalNote $internalNote): bool
    {
        $notable = $internalNote->notable;

        if (! $notable || ! isset($notable->workshop_id)) {
            return false;
        }

        // Must be from same workshop
        if ($user->workshop_id !== $notable->workshop_id) {
            return false;
        }

        // Owner and Office can view via permission
        // Mechanics can view notes on items they work on
        return $user->can('view internal notes') || $user->hasRole('Mechanic');
    }

    public function create(User $user): bool
    {
        return $user->can('create internal notes');
    }

    public function update(User $user, InternalNote $internalNote): bool
    {
        $notable = $internalNote->notable;

        if (! $notable || ! isset($notable->workshop_id)) {
            return false;
        }

        return $user->workshop_id === $notable->workshop_id
            && $user->can('update internal notes');
    }

    public function delete(User $user, InternalNote $internalNote): bool
    {
        $notable = $internalNote->notable;

        if (! $notable || ! isset($notable->workshop_id)) {
            return false;
        }

        return $user->workshop_id === $notable->workshop_id
            && $user->can('delete internal notes');
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
