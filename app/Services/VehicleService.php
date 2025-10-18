<?php

namespace App\Services;

use App\Dto\Vehicle\VehicleData;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleService
{
    public function paginatedList(array $filters = []): LengthAwarePaginator
    {
        $query = Vehicle::query()
            ->with('client')
            ->withCount('repairOrders');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['sort'])) {
            $direction = $filters['direction'] ?? 'asc';
            $query->orderBy($filters['sort'], $direction);
        } else {
            $query->latest('created_at');
        }

        return $query->paginate(15)
            ->through(fn ($vehicle) => VehicleData::from($vehicle));
    }
}
