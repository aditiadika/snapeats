<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        // Generate order number
        $order->update([
            'order_number' => 'SNP-'.str_pad($order->id, 6, '0', STR_PAD_LEFT),
        ]);
    }

    public function updating(Order $order)
    {
        if ($order->isDirty('status')) {
            // Log status changes
            ActivityLog::create([
                'order_id' => $order->id,
                'action' => 'status_change',
                'description' => 'Status changed from '.$order->getOriginal('status').' to '.$order->status,
            ]);
        }
    }
}
