<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function createOrderItem(Order $order, array $data)
    {
        return $order->items()->create($data);
    }

    public function updateOrderTotal(Order $order, float $total)
    {
        $order->update(['total' => $total]);
    }
}
