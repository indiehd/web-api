<?php

namespace App\Policies;

use App\Album;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
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
     * Determine whether the user can view the album.
     *
     * @param  \App\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function view(?User $user, Album $album)
    {
        return $album->is_active || $user && $user->is($album->artist->user);
    }

    /**
     * Determine whether the user can create albums.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->entities()->get()->isNotEmpty();
    }

    /**
     * Determine whether the user can update the album.
     *
     * @param  \App\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function update(User $user, Album $album)
    {
        // The User must own the Album.

        return $user->is($album->artist->user);
    }

    /**
     * Determine whether the user can delete the album.
     *
     * @param  \App\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function delete(User $user, Album $album)
    {
        // The User must own the Album.

        return $user->is($album->artist->user);
    }

    /**
     * Determine whether the user can restore the album.
     *
     * @param  \App\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function restore(User $user, Album $album)
    {
        return $user->is($album->artist->user);
    }

    /**
     * Determine whether the user can permanently delete the album.
     *
     * @param  \App\User  $user
     * @param  \App\Album  $album
     * @return mixed
     */
    public function forceDelete(User $user, Album $album)
    {
        return $user->is($album->artist->user);
    }
}
