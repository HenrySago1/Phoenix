<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\Customer;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository
    ) {}

    public function createOrderWithItems(Customer $customer, array $itemsData)
    {
        if (!$customer->is_active) {
            throw new Exception("El cliente debe estar activo para realizar un pedido.");
        }

        if (empty($itemsData)) {
            throw new Exception("El pedido debe contener al menos 1 producto.");
        }

        return DB::transaction(function () use ($customer, $itemsData) {
            $order = $this->orderRepository->createOrder([
                'customer_id' => $customer->id,
                'order_date' => now(),
                'status' => OrderStatusEnum::PENDING->value,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($itemsData as $item) {
                if ($item['quantity'] <= 0) {
                    throw new Exception("La cantidad debe ser mayor a 0.");
                }

                // Bloqueo pesimista para evitar race conditions
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->firstOrFail();

                if ($product->stock < $item['quantity']) {
                    throw new Exception("Stock insuficiente para el producto: {$product->name}");
                }

                $product->decrement('stock', $item['quantity']);
                
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $this->orderRepository->createOrderItem($order, [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $this->orderRepository->updateOrderTotal($order, $total);

            return $order;
        });
    }
}
