<?php

namespace App\Services;

use App\Dto\Common\MediaData;
use App\Dto\Common\SelectOptionData;
use App\Dto\RepairOrder\RepairOrderCreatePageData;
use App\Dto\RepairOrder\RepairOrderEditPagePropsData;
use App\Dto\RepairOrder\RepairOrderFormData;
use App\Dto\RepairOrder\RepairOrderListItemData;
use App\Dto\RepairOrder\RepairOrderShowPagePropsData;
use App\Dto\RepairOrder\StoreRepairOrderData;
use App\Dto\RepairOrder\UpdateRepairOrderData;
use App\Dto\RepairOrder\UpdateRepairOrderStatusData;
use App\Dto\RepairOrder\VehicleSelectionData;
use App\Enums\RepairOrderStatus;
use App\Exceptions\CannotDeleteRepairOrderWithTimeEntriesException;
use App\Models\RepairOrder;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
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

    public function store(StoreRepairOrderData $data): RepairOrder
    {
        $repairOrder = RepairOrder::create([
            'vehicle_id' => $data->vehicle_id,
            'problem_description' => $data->description,
            'status' => RepairOrderStatus::New,
        ]);

        if (! empty($data->attachments)) {
            foreach ($data->attachments as $attachment) {
                $repairOrder->addMedia($attachment)->toMediaCollection('images');
            }
        }

        return $repairOrder->fresh();
    }

    public function prepareDataForEditPage(RepairOrder $repairOrder): RepairOrderEditPagePropsData
    {
        $vehicles = Vehicle::query()
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->get();

        $images = $repairOrder->getMedia('images')->map(function ($media) {
            return new MediaData(
                id: $media->id,
                name: $media->name,
                url: $media->getUrl(),
                mime_type: $media->mime_type,
                size: $media->size,
            );
        });

        $repairOrderForm = new RepairOrderFormData(
            id: $repairOrder->id,
            vehicle_id: $repairOrder->vehicle_id,
            status: $repairOrder->status,
            problem_description: $repairOrder->problem_description,
            images: MediaData::collect(items: $images, into: DataCollection::class),
        );

        return new RepairOrderEditPagePropsData(
            repairOrder: $repairOrderForm,
            vehicles: VehicleSelectionData::collect(items: $vehicles, into: DataCollection::class),
            statuses: SelectOptionData::collect(items: RepairOrderStatus::options(), into: DataCollection::class),
        );
    }

    public function update(RepairOrder $repairOrder, UpdateRepairOrderData $data): RepairOrder
    {
        $updateData = [];

        if ($data->description !== null) {
            $updateData['problem_description'] = $data->description;
        }

        if ($data->status !== null) {
            $updateData['status'] = $data->status;
        }

        $repairOrder->update($updateData);

        return $repairOrder->fresh();
    }

    public function updateStatus(RepairOrder $repairOrder, UpdateRepairOrderStatusData $data): RepairOrder
    {
        $repairOrder->update([
            'status' => $data->status,
        ]);

        return $repairOrder->fresh();
    }

    public function prepareShowData(RepairOrder $repairOrder): RepairOrderShowPagePropsData
    {
        $repairOrder->load([
            'vehicle.client',
            'timeEntries.mechanic',
            'internalNotes.author',
            'activities.causer',
        ]);

        $user = Auth::user();

        $images = $repairOrder->getMedia('images')->map(fn ($media) => MediaData::from([
            'id' => $media->id,
            'name' => $media->name,
            'url' => $media->getUrl(),
            'mime_type' => $media->mime_type,
            'size' => $media->size,
        ]));

        $internalNotes = $repairOrder->internalNotes->map(fn ($note) => array_merge(
            $note->toArray(),
            ['author' => $note->author ? array_merge(
                $note->author->toArray(),
                ['type' => class_basename($note->author_type)]
            ) : null]
        ));

        $activityLog = $repairOrder->activities->map(fn ($activity) => [
            'id' => $activity->id,
            'description' => $activity->description,
            'event' => $activity->event,
            'properties' => $activity->properties?->toArray(),
            'created_at' => $activity->created_at,
            'causer' => $activity->causer,
        ]);

        return RepairOrderShowPagePropsData::from([
            'order' => [
                'id' => $repairOrder->id,
                'vehicle_id' => $repairOrder->vehicle_id,
                'status' => $repairOrder->status,
                'problem_description' => $repairOrder->problem_description,
                'started_at' => $repairOrder->started_at,
                'finished_at' => $repairOrder->finished_at,
                'total_time_minutes' => $repairOrder->total_time_minutes,
                'created_at' => $repairOrder->created_at,
                'vehicle' => $repairOrder->vehicle,
                'client' => $repairOrder->client,
                'images' => $images,
            ],
            'time_entries' => $repairOrder->timeEntries,
            'internal_notes' => $internalNotes,
            'activity_log' => $activityLog,
            'can_edit' => $user?->can('update', $repairOrder) ?? false,
            'can_delete' => $user?->can('delete', $repairOrder) ?? false,
        ]);
    }

    /**
     * @throws CannotDeleteRepairOrderWithTimeEntriesException
     */
    public function deleteRepairOrder(RepairOrder $repairOrder): void
    {
        if ($repairOrder->timeEntries()->exists()) {
            throw new CannotDeleteRepairOrderWithTimeEntriesException(
                'Cannot delete repair order with time entries.'
            );
        }

        $repairOrder->delete();
    }
}
