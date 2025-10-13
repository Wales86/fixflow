<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InternalNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_order_id',
        'content',
        'author_id',
        'author_type',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }

    public function author(): MorphTo
    {
        return $this->morphTo();
    }
}
