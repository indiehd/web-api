<?php

namespace App\Policies;

use App\User;
use App\Label;
use Illuminate\Auth\Access\HandlesAuthorization;

class LabelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the label.
     *
     * @param  \App\User  $user
     * @param  \App\Label  $label
     * @return mixed
     */
    public function view(User $user, Label $label)
    {
        //
    }

    /**
     * Determine whether the user can create labels.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // Any User is permitted to create a Label, because Labels are inactive
        // and therefor "provisional", by default; they must be approved to be
        // seen in the public Catalog.

        // Furthermore, any given User is able to be associated with an
        // unlimited number of Labels, so there is no reason ever to deny this
        // request.

        return true;
    }

    /**
     * Determine whether the user can update the label.
     *
     * @param  \App\User  $user
     * @param  \App\Label  $label
     * @return mixed
     */
    public function update(User $user, Label $label)
    {
        // The User must own the Label.

        return $label->user->id === $user->id;
    }

    /**
     * Determine whether the user can delete the label.
     *
     * @param  \App\User  $user
     * @param  \App\Label  $label
     * @return mixed
     */
    public function delete(User $user, Label $label)
    {
        // The User must own the Label.

        return $label->catalogable->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the label.
     *
     * @param  \App\User  $user
     * @param  \App\Label  $label
     * @return mixed
     */
    public function restore(User $user, Label $label)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the label.
     *
     * @param  \App\User  $user
     * @param  \App\Label  $label
     * @return mixed
     */
    public function forceDelete(User $user, Label $label)
    {
        //
    }
}
