<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToWorkshop
{
    protected static function bootBelongsToWorkshop(): void
    {
        static::addGlobalScope('workshop', function (Builder $builder) {
            if (auth()->check() && auth()->user()->workshop_id) {
                $builder->where($builder->getModel()->getTable().'.workshop_id', auth()->user()->workshop_id);
            }
        });

        static::creating(function (Model $model) {
            if (auth()->check() && auth()->user()->workshop_id && ! $model->workshop_id) {
                $model->workshop_id = auth()->user()->workshop_id;
            }
        });
    }
}
