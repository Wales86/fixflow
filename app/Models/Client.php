<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkshop;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use BelongsToWorkshop, HasFactory, SoftDeletes;

    protected $fillable = [
        'workshop_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'address_street',
        'address_city',
        'address_postal_code',
        'address_country',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function repairOrders(): HasManyThrough
    {
        return $this->hasManyThrough(RepairOrder::class, Vehicle::class);
    }

    protected function name(): Attribute
    {
        return Attribute::get(
            fn () => $this->first_name.' '.$this->last_name
        );
    }
}
