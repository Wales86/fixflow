<?php

namespace App\Services;

use App\Dto\TimeTracking\MechanicSelectOptionData;
use App\Models\Mechanic;
use App\Models\User;
use Spatie\LaravelData\DataCollection;

class MechanicService
{
    /**
     * Get active mechanics for sharing in Inertia middleware.
     * Returns null if user doesn't have permission to create time entries.
     */
    public function getActiveMechanicsForSharing(?User $user): ?DataCollection
    {
        if (! $user || ! $user->can('create_time_entries')) {
            return null;
        }

        $mechanics = Mechanic::query()
            ->where('workshop_id', $user->workshop_id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return MechanicSelectOptionData::collect($mechanics, DataCollection::class);
    }
}
