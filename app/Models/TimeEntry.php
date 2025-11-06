<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;

class TimeEntry extends Model
{
    use HasFactory, LogsActivity;

    protected static function booted(): void
    {
        static::addGlobalScope('workshop', function (Builder $builder) {
            if (auth()->check() && auth()->user()->workshop_id) {
                $builder->whereHas('repairOrder');
            }
        });
    }

    protected $fillable = [
        'repair_order_id',
        'mechanic_id',
        'duration_minutes',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['duration_minutes', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function getDurationHoursAttribute(): float
    {
        return round($this->duration_minutes / 60, 2);
    }
}
