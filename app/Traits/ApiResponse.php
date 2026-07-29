<?php

namespace App\Traits;

trait ApiResponse
{
    public function success($data = null, $message = null, $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'code' => $status,
        ], $status);
    }

    public function error($message = 'Something went wrong', $errors = null, $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $errors,
            'code' => $status,
        ], $status);
    }
}
