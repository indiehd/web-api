<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface FeaturableModelInterface
{
    public function scopeFeaturable(Builder $query): Builder;

    public function featureds(): MorphMany;
}
