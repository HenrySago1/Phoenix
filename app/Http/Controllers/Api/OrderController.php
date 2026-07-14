<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    #[OA\Get(
        path: '/api/orders',
        operationId: 'getOrdersList',
        summary: 'Listar Pedidos',
        description: 'Retorna una lista de todos los pedidos registrados en el sistema.',
        tags: ['Pedidos']
    )]
    #[OA\Response(response: 200, description: 'Operación exitosa')]
    public function index()
    {
        return response()->json(Order::with('items')->get(), 200);
    }

    #[OA\Post(
        path: '/api/orders',
        operationId: 'storeOrder',
        summary: 'Crear Pedido',
        description: 'Crea un nuevo pedido con sus respectivos productos utilizando el OrderService transaccional.',
        tags: ['Pedidos']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['customer_id', 'items'],
            properties: [
                new OA\Property(property: 'customer_id', type: 'string', description: 'UUID del Cliente activo', example: '9a8b7c6d-5e4f-3a2b-1c0d-9e8f7a6b5c4d'),
                new OA\Property(
                    property: 'items',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'product_id', type: 'string', example: '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d'),
                            new OA\Property(property: 'quantity', type: 'integer', example: 2)
                        ]
                    )
                ),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Pedido creado exitosamente')]
    #[OA\Response(response: 422, description: 'Error de validación (Ej. Stock insuficiente, Cliente inactivo)')]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|uuid|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // El servicio valida si el cliente está activo, si hay stock y bloquea la fila
            $order = $this->orderService->createOrderWithItems($validated['customer_id'], $validated['items']);
            return response()->json($order->load('items'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    #[OA\Get(
        path: '/api/orders/{id}',
        operationId: 'getOrderById',
        summary: 'Consultar Pedido',
        description: 'Devuelve los detalles de un pedido específico y sus ítems.',
        tags: ['Pedidos']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'UUID del Pedido',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(response: 200, description: 'Operación exitosa')]
    #[OA\Response(response: 404, description: 'Pedido no encontrado')]
    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return response()->json($order, 200);
    }
}
