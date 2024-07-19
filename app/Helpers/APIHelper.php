<?php

namespace App\Helpers;

class APIHelper {
    public static function successResponse($status = 'success', $statusCode = 200, $message = null, $data = [], $paginate = null) {
        if (!is_null($paginate)) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'paginate' => [
                    'total' => $paginate['total'],
                    'current_page' => $paginate['current_page'],
                    'limit' => $paginate['limit']
                ],
                'data' => $data
            ], $statusCode);
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function errorResponse($status = 'failed', $statusCode = 500, $message = 'Internal Server Error') {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $statusCode);
    }
}