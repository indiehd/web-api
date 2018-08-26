<?php

namespace App\Observers;

use App\Label;

class LabelObserver
{
    /**
     * Handle the label "created" event.
     *
     * @param  \App\Label  $label
     * @return void
     */
    public function created(Label $label)
    {
        //
    }

    /**
     * Handle the label "updated" event.
     *
     * @param  \App\Label  $label
     * @return void
     */
    public function updated(Label $label)
    {
        //
    }

    /**
     * Handle the label "deleting" event.
     *
     * @param  \App\Label  $label
     * @return void
     */
    public function deleting(Label $label)
    {
        $label->artists()->update(['label_id' => null]);
    }

    /**
     * Handle the label "deleted" event.
     *
     * @param  \App\Label  $label
     * @return void
     */
    public function deleted(Label $label)
    {
        //
    }

    /**
     * Handle the label "restored" event.
     *
     * @param  \App\Label  $label
     * @return void
     */
    public function restored(Label $label)
    {
        //
    }

    /**
     * Handle the label "force deleted" event.
     *
     * @param  \App\Label  $label
     * @return void
     */
    public function forceDeleted(Label $label)
    {
        //
    }
}
