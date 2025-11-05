<?php

namespace App\Models;

use App\Enums\RepairOrderStatus;
use App\Models\Concerns\BelongsToWorkshop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RepairOrder extends Model implements HasMedia
{
    use BelongsToWorkshop, HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    protected $fillable = [
        'workshop_id',
        'vehicle_id',
        'status',
        'problem_description',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RepairOrderStatus::class,
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'problem_description', 'started_at', 'finished_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function client(): HasOneThrough
    {
        return $this->hasOneThrough(Client::class, Vehicle::class, 'id', 'id', 'vehicle_id', 'client_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function internalNotes(): MorphMany
    {
        return $this->morphMany(InternalNote::class, 'notable');
    }

    public function getTotalTimeMinutesAttribute(): int
    {
        return $this->timeEntries()->sum('duration_minutes');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
            ->useDisk('minio');
    }
}
