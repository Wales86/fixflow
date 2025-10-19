<?php

namespace App\Services;

use App\Dto\Common\SelectOptionData;
use App\Dto\RepairOrder\RepairOrderCreatePageData;
use App\Dto\RepairOrder\RepairOrderListItemData;
use App\Dto\RepairOrder\VehicleSelectionData;
use App\Enums\RepairOrderStatus;
use App\Models\RepairOrder;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\DataCollection;

class RepairOrderService
{
    public function paginatedList(array $filters = []): LengthAwarePaginator
    {
        $query = RepairOrder::query()
            ->with(['vehicle', 'client']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('problem_description', 'like', "%{$search}%")
                    ->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                        $vehicleQuery->where('make', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('registration_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['sort'])) {
            $direction = $filters['direction'] ?? 'asc';
            $query->orderBy($filters['sort'], $direction);
        } else {
            $query->latest('created_at');
        }

        return $query->paginate(15)
            ->through(fn ($repairOrder) => RepairOrderListItemData::from($repairOrder));
    }

    public function createFormData(?int $preselectedVehicleId = null): RepairOrderCreatePageData
    {
        $vehicles = Vehicle::query()
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->get();

        return new RepairOrderCreatePageData(
            vehicles: VehicleSelectionData::collect(items: $vehicles, into: DataCollection::class),
            statuses: SelectOptionData::collect(items: RepairOrderStatus::options(), into: DataCollection::class),
            preselected_vehicle_id: $preselectedVehicleId,
        );
    }
}
