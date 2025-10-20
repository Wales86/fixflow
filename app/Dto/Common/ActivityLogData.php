<?php

namespace App\Dto\Common;

use Spatie\Activitylog\Models\Activity;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ActivityLogData extends Data
{
    public function __construct(
        public int $id,
        public string $description,
        public ?string $event,
        public ?array $properties,
        public string $created_at,
        public ?ActivityLogCauserData $causer = null,
    ) {}

    public static function fromActivity(Activity $activity): self
    {
        return new self(
            id: $activity->id,
            description: $activity->description,
            event: $activity->event,
            properties: $activity->properties?->toArray(),
            created_at: $activity->created_at->format('Y-m-d H:i:s'),
            causer: $activity->causer ? ActivityLogCauserData::from([
                'id' => $activity->causer->id,
                'name' => $activity->causer->name,
                'type' => class_basename($activity->causer_type),
            ]) : null,
        );
    }
}
