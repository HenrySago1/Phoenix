<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        operationId: 'getProductsList',
        summary: 'Listar Productos Activos',
        description: 'Retorna una lista de los productos disponibles y activos.',
        tags: ['Productos']
    )]
    #[OA\Response(response: 200, description: 'Operación exitosa')]
    public function index()
    {
        return response()->json(Product::where('is_active', true)->get(), 200);
    }

    #[OA\Post(
        path: '/api/products',
        operationId: 'storeProduct',
        summary: 'Crear Producto',
        description: 'Registra un nuevo producto en el inventario.',
        tags: ['Productos']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'price', 'stock'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Teclado Mecánico'),
                new OA\Property(property: 'description', type: 'string', example: 'Teclado RGB switch blue'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 150.50),
                new OA\Property(property: 'stock', type: 'integer', example: 10),
                new OA\Property(property: 'is_active', type: 'boolean', example: true),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Producto creado')]
    #[OA\Response(response: 422, description: 'Error de validación de datos')]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    #[OA\Get(
        path: '/api/products/{id}',
        operationId: 'getProductById',
        summary: 'Consultar Producto',
        description: 'Devuelve la información de un producto utilizando su UUID.',
        tags: ['Productos']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'UUID del Producto',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(response: 200, description: 'Operación exitosa')]
    #[OA\Response(response: 404, description: 'Producto no encontrado')]
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product, 200);
    }
}
