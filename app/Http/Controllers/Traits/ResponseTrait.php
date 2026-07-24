<?php

namespace App\Http\Controllers\Traits;

trait ResponseTrait
{
    public function successResponse($code = '', $message = '', $data = '')
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
            'error' => '',
        ], $code);
    }

    public function addSuccessResponse($code = '', $message = '', $data = '')
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
            'error' => '',
        ], $code);
    }

    public function errorResponse($code = '', $message = '', $data = '')
    {
        return response()->json([
            'status' => $code,
            'ResponseCode' => $code,
            'message' => $message,
            'data' => null,
            'error' => $message,
        ], $code);
    }

    public function addErrorResponse($code = '', $message = '', $data = '')
    {
        return response()->json([
            'status' => $code,
            'ResponseCode' => $code,
            'message' => $message,
            'data' => null,
            'error' => $message,
        ], $code);
    }

    public function errorComputing($validator)
    {
        $err_container = '';
        $statusCode = 401; // default

        foreach ($validator->errors()->getMessages() as $field => $error) {
            if (! $err_container) {
                $err_container = $error[0];
            } else {
                $err_container .= ','.$error[0];
            }

            // Check if token validation failed
            if ($field === 'token') {
                $statusCode = 419; // token expired/invalid
            }
        }

        return response()->json([
            'status' => $statusCode,
            'ResponseCode' => $statusCode,
            'Result' => 'false',
            'ResponseMsg' => $err_container,
            'message' => $err_container,
            'data' => [],
            'error' => $err_container,
        ], $statusCode);
    }
}
