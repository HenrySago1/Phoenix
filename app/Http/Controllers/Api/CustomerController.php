<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CustomerController extends Controller
{
    #[OA\Get(
        path: '/api/customers',
        operationId: 'getCustomersList',
        summary: 'Listar Clientes Activos',
        description: 'Retorna una lista de los clientes que se encuentran activos.',
        tags: ['Clientes']
    )]
    #[OA\Response(response: 200, description: 'Operación exitosa')]
    public function index()
    {
        return response()->json(Customer::where('is_active', true)->get(), 200);
    }

    #[OA\Post(
        path: '/api/customers',
        operationId: 'storeCustomer',
        summary: 'Crear Cliente',
        description: 'Registra un nuevo cliente en el sistema.',
        tags: ['Clientes']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['full_name', 'email'],
            properties: [
                new OA\Property(property: 'full_name', type: 'string', example: 'Nicole Fernandez'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'nicole@phoenix.com'),
                new OA\Property(property: 'phone', type: 'string', example: '+59177777777'),
                new OA\Property(property: 'is_active', type: 'boolean', example: true),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Cliente creado')]
    #[OA\Response(response: 422, description: 'Error de validación de datos')]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $customer = Customer::create($validated);
        return response()->json($customer, 201);
    }

    #[OA\Get(
        path: '/api/customers/{id}',
        operationId: 'getCustomerById',
        summary: 'Consultar Cliente',
        description: 'Devuelve la información de un cliente utilizando su UUID.',
        tags: ['Clientes']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'UUID del Cliente',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(response: 200, description: 'Operación exitosa')]
    #[OA\Response(response: 404, description: 'Cliente no encontrado')]
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer, 200);
    }
}
