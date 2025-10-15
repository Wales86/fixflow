<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkshop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use BelongsToWorkshop, HasFactory, SoftDeletes;

    protected $fillable = [
        'workshop_id',
        'client_id',
        'make',
        'model',
        'year',
        'vin',
        'registration_number',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function repairOrders(): HasMany
    {
        return $this->hasMany(RepairOrder::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }
}
