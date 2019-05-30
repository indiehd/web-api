<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Contracts\FeaturableModelInterface;

class Artist extends Model implements FeaturableModelInterface
{
    protected $guarded = ['id'];

    public function catalogable()
    {
        return $this->morphOne(CatalogEntity::class, 'catalogable');
    }

    public function profile()
    {
        return $this->morphOne(Profile::class, 'profilable');
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function songs()
    {
        return $this->hasManyThrough(Song::class, Album::class);
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function user()
    {
        return $this->catalogable->user();
    }

    public function scopeFeaturable(Builder $query): Builder
    {
        return $query->whereHas('albums', function ($query) {
            $query->where('is_active', 1)
                ->whereHas('songs', function ($query) {
                    $query->where('is_active', 1);
                });
        })
        ->whereDoesntHave('featureds', function ($query) {
            // "... where created_at is more recent than 180 days ago."
            // The goal is to exclude Artists who have been Featured in the
            // past 180 days.

            $query->where('created_at', '>', Carbon::now()->subDays(180)->toDateTimeString());
        });
    }

    public function featureds(): MorphMany
    {
        return $this->morphMany(Featured::class, 'featurable');
    }
}
