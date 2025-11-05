<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\InternalNote;
use App\Models\User;

class InternalNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::VIEW_INTERNAL_NOTES->value);
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
        return $user->can(UserPermission::VIEW_INTERNAL_NOTES->value) || $user->hasRole('Mechanic');
    }

    public function create(User $user): bool
    {
        return $user->can(UserPermission::CREATE_INTERNAL_NOTES->value);
    }

    public function update(User $user, InternalNote $internalNote): bool
    {
        $notable = $internalNote->notable;

        if (! $notable || ! isset($notable->workshop_id)) {
            return false;
        }

        return $user->workshop_id === $notable->workshop_id
            && $user->can(UserPermission::UPDATE_INTERNAL_NOTES->value);
    }

    public function delete(User $user, InternalNote $internalNote): bool
    {
        $notable = $internalNote->notable;

        if (! $notable || ! isset($notable->workshop_id)) {
            return false;
        }

        return $user->workshop_id === $notable->workshop_id
            && $user->can(UserPermission::DELETE_INTERNAL_NOTES->value);
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
