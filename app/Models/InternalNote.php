<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InternalNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'notable_type',
        'notable_id',
        'content',
        'author_id',
        'author_type',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    public function author(): MorphTo
    {
        return $this->morphTo();
    }
}
