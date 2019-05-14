<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
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

    public function scopeFeaturable($query)
    {
        return $query->has('profile')
            ->whereHas('albums', function ($query) {
                $query->where('is_active', 1);
            })
            ->whereDoesntHave('featureds', function ($query) {
                $query->where('created_at', '<', Carbon::now()->subDays(180));
            });
    }

    public function featureds()
    {
        return $this->morphMany(Featured::class, 'featurable');
    }
}
