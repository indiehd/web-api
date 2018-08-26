<?php

namespace App\Observers;

use App\Genre;

class GenreObserver
{
    /**
     * Handle the genre "created" event.
     *
     * @param  \App\Genre  $genre
     * @return void
     */
    public function created(Genre $genre)
    {
        //
    }

    /**
     * Handle the genre "updated" event.
     *
     * @param  \App\Genre  $genre
     * @return void
     */
    public function updated(Genre $genre)
    {
        //
    }

    /**
     * Handle the genre "deleting" event.
     *
     * @param  \App\Genre  $genre
     * @return void
     */
    public function deleting(Genre $genre)
    {
        $genre->albums()->detach();
    }

    /**
     * Handle the genre "deleted" event.
     *
     * @param  \App\Genre  $genre
     * @return void
     */
    public function deleted(Genre $genre)
    {
        //
    }

    /**
     * Handle the genre "restored" event.
     *
     * @param  \App\Genre  $genre
     * @return void
     */
    public function restored(Genre $genre)
    {
        //
    }

    /**
     * Handle the genre "force deleted" event.
     *
     * @param  \App\Genre  $genre
     * @return void
     */
    public function forceDeleted(Genre $genre)
    {
        //
    }
}
