<?php

namespace App\Presentation\Http\Responses;

trait ResponseTrait
{
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'status'  => 'Success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse($message = null, $code = 400)
    {
        return response()->json([
            'status'  => 'Error',
            'message' => $message,
            'data'    => null,
        ], $code);
    }
}
