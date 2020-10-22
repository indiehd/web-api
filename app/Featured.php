<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

use App\Contracts\ArtistRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Featured extends Model
{
    use HasFactory;

    protected $fillable = [
        'featurable_id',
        'featurable_type',
    ];

    public function featurable()
    {
        return $this->morphTo();
    }

    public function scopeArtists($query)
    {
        $artist = resolve(ArtistRepositoryInterface::class);

        return $query->where('featurable_type', $artist->class())
            // "... where created_at is more recent than 7 days ago."
            ->where('created_at', '>', Carbon::now()->subDays(7)->toDateTimeString())
            // Order by creation time, descending, and group by all selected
            // columns to ensure that only the most recent Feature is returned
            // for any given featurable_id and featurable_type.
            ->orderBy('created_at', 'desc')
            ->groupBy(['id', 'featurable_id', 'featurable_type', 'created_at', 'updated_at']);
    }
}
