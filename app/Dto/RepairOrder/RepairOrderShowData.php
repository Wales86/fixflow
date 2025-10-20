<?php

namespace App\Dto\RepairOrder;

use App\Dto\Common\MediaData;
use App\Enums\RepairOrderStatus;
use App\Models\RepairOrder;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RepairOrderShowData extends Data
{
    public function __construct(
        public int $id,
        public int $vehicle_id,
        public RepairOrderStatus $status,
        public string $problem_description,
        public ?string $started_at,
        public ?string $finished_at,
        public int $total_time_minutes,
        public string $created_at,
        public RepairOrderVehicleData $vehicle,
        public RepairOrderClientData $client,
        #[DataCollectionOf(MediaData::class)]
        public DataCollection $images,
    ) {}

    public static function fromRepairOrder(RepairOrder $repairOrder): self
    {
        $images = MediaData::collect(
            $repairOrder->getMedia('images')->map(fn ($media) => [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
                'size' => $media->size,
            ]),
            DataCollection::class
        );

        return new self(
            id: $repairOrder->id,
            vehicle_id: $repairOrder->vehicle_id,
            status: $repairOrder->status,
            problem_description: $repairOrder->problem_description,
            started_at: $repairOrder->started_at,
            finished_at: $repairOrder->finished_at,
            total_time_minutes: $repairOrder->total_time_minutes,
            created_at: $repairOrder->created_at,
            vehicle: RepairOrderVehicleData::from($repairOrder->vehicle),
            client: RepairOrderClientData::from($repairOrder->client),
            images: $images,
        );
    }
}
