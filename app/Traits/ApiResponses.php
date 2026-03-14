<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponses
{
    protected function successResponse($data, int $status = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    protected function errorResponse(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (! empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    protected function paginatedResponse(ResourceCollection $collection): JsonResponse
    {
        $paginated = $collection->response()->getData(true);

        return response()->json([
            'success' => true,
            'data' => $paginated['data'],
            'meta' => [
                'current_page' => $paginated['meta']['current_page'] ?? null,
                'last_page' => $paginated['meta']['last_page'] ?? null,
                'per_page' => $paginated['meta']['per_page'] ?? null,
                'total' => $paginated['meta']['total'] ?? null,
            ],
        ]);
    }

    protected function domain(): \App\Models\Domain
    {
        return request()->attributes->get('domain');
    }
}
