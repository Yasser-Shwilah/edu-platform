<?php

namespace App\Traits;

trait ResponseTrait
{
    protected function successResponse($message = '', $data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message = '', $errors = [], $code = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
