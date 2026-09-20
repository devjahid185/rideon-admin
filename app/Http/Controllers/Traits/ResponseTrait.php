<?php

namespace App\Http\Controllers\Traits;

trait ResponseTrait
{
    private function logApiError($code, $message, $data = null, $context = [])
    {
        if ((int) $code < 400) {
            return;
        }

        \Log::warning('api_error_response', array_merge([
            'status' => (int) $code,
            'message' => $message,
            'route' => optional(request()->route())->uri(),
            'method' => request()->method(),
            'path' => request()->path(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'has_request_token' => request()->filled('token'),
            'has_x_auth_token' => request()->headers->has('x-auth-token'),
            'has_bearer_token' => request()->bearerToken() !== null,
            'auth_user_id' => optional(request()->user())->id,
            'data' => is_scalar($data) || is_null($data) ? $data : '[non-scalar]',
        ], $context));
    }

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
        $this->logApiError($code, $message, $data);

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
        $this->logApiError($code, $message, $data);

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

        $this->logApiError($statusCode, $err_container, null, [
            'validation_errors' => $validator->errors()->toArray(),
        ]);

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
