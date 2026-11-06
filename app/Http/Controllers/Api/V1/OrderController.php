<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        return response()->json([
            'data' => [
                [
                    'id' => 1001,
                    'status' => 'paid',
                    'total' => 119.49,
                    'currency' => 'USD',
                ],
                [
                    'id' => 1002,
                    'status' => 'pending',
                    'total' => 45.00,
                    'currency' => 'USD',
                ],
            ],
            'meta' => [
                'client' => $client->name,
                'endpoint' => '/api/v1/orders',
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        $validated = $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['nullable', Rule::in(['pending', 'paid', 'cancelled'])],
        ]);

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => [
                'id' => random_int(2000, 9999),
                'status' => $validated['status'] ?? 'pending',
                'total' => (float) $validated['total'],
                'currency' => strtoupper($validated['currency'] ?? 'USD'),
            ],
            'meta' => [
                'client' => $client->name,
                'action' => 'create',
            ],
        ], 201);
    }

    public function update(Request $request, int $order): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        $validated = $request->validate([
            'total' => ['sometimes', 'required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['sometimes', 'required', Rule::in(['pending', 'paid', 'cancelled'])],
        ]);

        return response()->json([
            'message' => 'Order updated successfully.',
            'data' => [
                'id' => $order,
                'status' => $validated['status'] ?? 'pending',
                'total' => isset($validated['total']) ? (float) $validated['total'] : 0,
                'currency' => strtoupper($validated['currency'] ?? 'USD'),
            ],
            'meta' => [
                'client' => $client->name,
                'action' => 'update',
            ],
        ]);
    }

    public function destroy(Request $request, int $order): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->attributes->get('api_client');

        return response()->json([
            'message' => 'Order deleted successfully.',
            'data' => [
                'id' => $order,
                'deleted' => true,
            ],
            'meta' => [
                'client' => $client->name,
                'action' => 'delete',
            ],
        ]);
    }
}
