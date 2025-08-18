<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

class ResponseHelper
{
    protected static function baseMeta(int $code = Response::HTTP_OK, string $status = 'success', ?string $message = null): array
    {
        return [
            'meta' => [
                'code' => $code,
                'status' => $status,
                'message' => $message,
                'pagination' => null
            ],
            'data' => null,
        ];
    }

    public static function success(mixed $data = null, ?string $message = null, int $code = Response::HTTP_OK, bool $withPagination = false): JsonResponse
    {
        $response = self::baseMeta($code, 'success', $message);

        if ($withPagination && $data instanceof LengthAwarePaginator) {
            $response['meta']['pagination'] = [
                'current_page' => $data->currentPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
                'last_page'    => $data->lastPage(),
            ];
            $response['data'] = $data->items();
        } elseif ($withPagination && $data instanceof Paginator) {
            $response['meta']['pagination'] = [
                'current_page' => $data->currentPage(),
                'per_page'     => $data->perPage(),
            ];
            $response['data'] = $data->items();
        } else {
            $response['data'] = $data;
            unset($response['meta']['pagination']);
        }

        return response()->json($response, $code);
    }

    /**
     * Return an error response.
     * If you prefer throwing HttpResponseException, replace the return with a throw.
     */
    public static function error(?string $message = 'Something went wrong', int $code = Response::HTTP_BAD_REQUEST, mixed $data = null): JsonResponse
    {
        $response = self::baseMeta($code, 'error', $message);
        $response['data'] = $data;
        unset($response['meta']['pagination']);

        return response()->json($response, $code);

     
    }
}
