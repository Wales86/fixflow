<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Tenant;

class Workshop extends Tenant
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function mechanics(): HasMany
    {
        return $this->hasMany(Mechanic::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function repairOrders(): HasMany
    {
        return $this->hasMany(RepairOrder::class);
    }
}
