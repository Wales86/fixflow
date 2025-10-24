<?php

namespace App\Services;

use App\Dto\Mechanic\GetMechanicsData;
use App\Dto\Mechanic\MechanicData;
use App\Dto\Mechanic\StoreMechanicData;
use App\Dto\Mechanic\UpdateMechanicData;
use App\Dto\TimeTracking\MechanicSelectOptionData;
use App\Models\Mechanic;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\DataCollection;

class MechanicService
{
    /**
     * Get active mechanics for sharing in Inertia middleware.
     * Returns null if user doesn't have permission to create time entries or internal notes.
     */
    public function getActiveMechanicsForSharing(?User $user): ?DataCollection
    {
        if (! $user || ! ($user->can('create_time_entries') || $user->can('create_internal_notes'))) {
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

    public function getMechanics(GetMechanicsData $data): LengthAwarePaginator
    {
        $query = Mechanic::query()
            ->withCount('timeEntries');

        if ($data->active !== null) {
            $query->where('is_active', $data->active);
        }

        $query->orderBy('first_name')
            ->orderBy('last_name');

        return $query->paginate(15)
            ->through(fn ($mechanic) => MechanicData::from($mechanic));
    }

    public function create(StoreMechanicData $data): Mechanic
    {
        return Mechanic::create($data->all());
    }

    public function update(Mechanic $mechanic, UpdateMechanicData $data): Mechanic
    {
        $mechanic->update($data->all());

        return $mechanic->fresh();
    }
}
