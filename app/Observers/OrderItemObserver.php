<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        if ($orderItem->product) {
            $orderItem->product->decrement('stock', $orderItem->quantity);
        }
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        // Handle quantity changes if needed
        if ($orderItem->isDirty('quantity')) {
            $diff = $orderItem->quantity - $orderItem->getOriginal('quantity');
            if ($orderItem->product) {
                if ($diff > 0) {
                    $orderItem->product->decrement('stock', $diff);
                } elseif ($diff < 0) {
                    $orderItem->product->increment('stock', abs($diff));
                }
            }
        }
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        if ($orderItem->product) {
            $orderItem->product->increment('stock', $orderItem->quantity);
        }
    }

    /**
     * Handle the OrderItem "restored" event.
     */
    public function restored(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     */
    public function forceDeleted(OrderItem $orderItem): void
    {
        //
    }
}
