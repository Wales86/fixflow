<?php

namespace App\Services;

use App\Dto\RepairOrder\RepairOrderData;
use App\Dto\Vehicle\StoreVehicleData;
use App\Dto\Vehicle\UpdateVehicleData;
use App\Dto\Vehicle\VehicleData;
use App\Dto\Vehicle\VehicleEditPagePropsData;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleService
{
    public function __construct(
        public ClientService $clientService,
    ) {}

    public function store(StoreVehicleData $data): Vehicle
    {
        return Vehicle::create($data->all());
    }

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

    public function paginatedRepairHistory(Vehicle $vehicle): LengthAwarePaginator
    {
        return $vehicle->repairOrders()
            ->latest('created_at')
            ->paginate(15)
            ->through(fn ($repairOrder) => RepairOrderData::from($repairOrder));
    }

    public function prepareEditPageData(Vehicle $vehicle): VehicleEditPagePropsData
    {
        return new VehicleEditPagePropsData(
            vehicle: VehicleData::from($vehicle),
            clients: $this->clientService->getClientsForSelect(),
        );
    }

    public function update(Vehicle $vehicle, UpdateVehicleData $data): Vehicle
    {
        $vehicle->update($data->all());

        return $vehicle->fresh();
    }
}
