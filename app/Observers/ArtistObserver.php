<?php

namespace App\Observers;

use DB;
use App\Artist;

class ArtistObserver
{
    /**
     * Handle the artist "created" event.
     *
     * @param  \App\Artist  $artist
     * @return void
     */
    public function created(Artist $artist)
    {
        //
    }

    /**
     * Handle the artist "updated" event.
     *
     * @param  \App\Artist  $artist
     * @return void
     */
    public function updated(Artist $artist)
    {
        //
    }

    /**
     * Handle the artist "deleting" event.
     *
     * @param  \App\Artist  $artist
     * @return void
     */
    public function deleting(Artist $artist)
    {
        DB::transaction(function () use ($artist) {
            $artist->albums()->each(function ($model) {
                $model->delete();
            });
        });
    }

    /**
     * Handle the artist "deleted" event.
     *
     * @param  \App\Artist  $artist
     * @return void
     */
    public function deleted(Artist $artist)
    {
        //
    }

    /**
     * Handle the artist "restored" event.
     *
     * @param  \App\Artist  $artist
     * @return void
     */
    public function restored(Artist $artist)
    {
        //
    }

    /**
     * Handle the artist "force deleted" event.
     *
     * @param  \App\Artist  $artist
     * @return void
     */
    public function forceDeleted(Artist $artist)
    {
        //
    }
}
