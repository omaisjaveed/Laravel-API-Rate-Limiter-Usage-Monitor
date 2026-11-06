<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        return response()->json([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Wireless Mouse',
                    'price' => 29.99,
                    'currency' => 'USD',
                ],
                [
                    'id' => 2,
                    'name' => 'Mechanical Keyboard',
                    'price' => 89.50,
                    'currency' => 'USD',
                ],
                [
                    'id' => 3,
                    'name' => 'USB-C Hub',
                    'price' => 45.00,
                    'currency' => 'USD',
                ],
            ],
            'meta' => [
                'client' => $client->name,
                'endpoint' => '/api/v1/products',
            ],
        ]);
    }

    public function show(Request $request, int $product): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        return response()->json([
            'data' => [
                'id' => $product,
                'name' => 'Sample Product #'.$product,
                'price' => 19.99,
                'currency' => 'USD',
                'in_stock' => true,
            ],
            'meta' => [
                'client' => $client->name,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => [
                'id' => random_int(100, 999),
                'name' => $validated['name'],
                'price' => (float) $validated['price'],
                'currency' => strtoupper($validated['currency'] ?? 'USD'),
            ],
            'meta' => [
                'client' => $client->name,
                'action' => 'create',
            ],
        ], 201);
    }

    public function update(Request $request, int $product): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => [
                'id' => $product,
                'name' => $validated['name'] ?? 'Sample Product #'.$product,
                'price' => isset($validated['price']) ? (float) $validated['price'] : 19.99,
                'currency' => strtoupper($validated['currency'] ?? 'USD'),
            ],
            'meta' => [
                'client' => $client->name,
                'action' => 'update',
            ],
        ]);
    }

    public function destroy(Request $request, int $product): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        return response()->json([
            'message' => 'Product deleted successfully.',
            'data' => [
                'id' => $product,
                'deleted' => true,
            ],
            'meta' => [
                'client' => $client->name,
                'action' => 'delete',
            ],
        ]);
    }
}
