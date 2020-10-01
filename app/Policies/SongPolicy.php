<?php

namespace App\Policies;

use App\Song;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SongPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the song.
     *
     * @param  \App\User  $user
     * @param  \App\Song  $song
     * @return mixed
     */
    public function view(?User $user, Song $song)
    {
        return $song->is_active || $user->is($song->artist->user);
    }

    /**
     * Determine whether the user can create songs.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->entities()->get()->isNotEmpty();
    }

    /**
     * Determine whether the user can update the song.
     *
     * @param  \App\User  $user
     * @param  \App\Song  $song
     * @return mixed
     */
    public function update(User $user, Song $song)
    {
        // The User must own the Song.

        return $user->is($song->album->artist->user);
    }

    /**
     * Determine whether the user can delete the song.
     *
     * @param  \App\User  $user
     * @param  \App\Song  $song
     * @return mixed
     */
    public function delete(User $user, Song $song)
    {
        // The User must own the Song.

        return $user->is($song->album->artist->user);
    }

    /**
     * Determine whether the user can restore the song.
     *
     * @param  \App\User  $user
     * @param  \App\Song  $song
     * @return mixed
     */
    public function restore(User $user, Song $song)
    {
        return $user->is($song->album->artist->user);
    }

    /**
     * Determine whether the user can permanently delete the song.
     *
     * @param  \App\User  $user
     * @param  \App\Song  $song
     * @return mixed
     */
    public function forceDelete(User $user, Song $song)
    {
        return $user->is($song->album->artist->user);
    }
}
