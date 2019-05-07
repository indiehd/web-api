<?php

namespace App\Observers;

use App\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the order item "created" event.
     *
     * @param  \App\OrderItem  $orderItem
     * @return void
     */
    public function created(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the order item "updated" event.
     *
     * @param  \App\OrderItem  $orderItem
     * @return void
     */
    public function updated(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the order item "deleted" event.
     *
     * @param  \App\OrderItem  $orderItem
     * @return void
     */
    public function deleted(OrderItem $orderItem)
    {
        // If this was the last Order Item to be deleted, then delete the Order.

        if ($orderItem->order->items->isEmpty()) {
            $orderItem->order->delete();
        }
    }

    /**
     * Handle the order item "restored" event.
     *
     * @param  \App\OrderItem  $orderItem
     * @return void
     */
    public function restored(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the order item "force deleted" event.
     *
     * @param  \App\OrderItem  $orderItem
     * @return void
     */
    public function forceDeleted(OrderItem $orderItem)
    {
        //
    }
}
