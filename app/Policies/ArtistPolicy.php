<?php

namespace App\Policies;

use App\Artist;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtistPolicy
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
     * Determine whether the user can view the artist.
     *
     * @param  \App\User  $user
     * @param  \App\Artist  $artist
     * @return mixed
     */
    public function view(?User $user, Artist $artist)
    {
        return true;
    }

    /**
     * Determine whether the user can create artists.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the artist.
     *
     * @param  \App\User  $user
     * @param  \App\Artist  $artist
     * @return mixed
     */
    public function update(User $user, Artist $artist)
    {
        return $user->is($artist->user);
    }

    /**
     * Determine whether the user can delete the artist.
     *
     * @param  \App\User  $user
     * @param  \App\Artist  $artist
     * @return mixed
     */
    public function delete(User $user, Artist $artist)
    {
        // The User must own the Artist.

        return $user->is($artist->user);
    }

    /**
     * Determine whether the user can restore the artist.
     *
     * @param  \App\User  $user
     * @param  \App\Artist  $artist
     * @return mixed
     */
    public function restore(User $user, Artist $artist)
    {
        return $user->is($artist->user);
    }

    /**
     * Determine whether the user can permanently delete the artist.
     *
     * @param  \App\User  $user
     * @param  \App\Artist  $artist
     * @return mixed
     */
    public function forceDelete(User $user, Artist $artist)
    {
        return $user->is($artist->user);
    }
}
