<?php

namespace App\Http\Controllers\Traits;

trait ResponseTrait
{
    private function logApiError($code, $message, $data = null, $context = [])
    {
        if ((int) $code < 400) {
            return;
        }

        $payload = array_merge([
            'status' => (int) $code,
            'message' => $message,
            'route' => optional(request()->route())->uri(),
            'method' => request()->method(),
            'path' => request()->path(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'has_request_token' => request()->filled('token'),
            'request_token_preview' => $this->responseSafeTokenPreview((string) request()->input('token', '')),
            'request_token_length' => strlen((string) request()->input('token', '')),
            'has_x_auth_token' => request()->headers->has('x-auth-token'),
            'x_auth_token_preview' => $this->responseSafeTokenPreview((string) request()->header('x-auth-token', '')),
            'has_bearer_token' => request()->bearerToken() !== null,
            'bearer_token_preview' => $this->responseSafeTokenPreview((string) request()->bearerToken()),
            'auth_user_id' => optional(request()->user())->id,
            'data' => is_scalar($data) || is_null($data) ? $data : '[non-scalar]',
        ], $context);

        \Log::warning('api_error_response', $payload);
        $this->writeResponseApiDebugLog('api_error_response', $payload);
    }

    private function writeResponseApiDebugLog($event, array $payload)
    {
        try {
            $line = json_encode(array_merge([
                'time' => now()->toDateTimeString(),
                'event' => $event,
            ], $payload), JSON_UNESCAPED_SLASHES).PHP_EOL;

            file_put_contents(storage_path('logs/api-token-debug.log'), $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            // Keep API responses working even if debug logging cannot write.
        }
    }

    private function responseSafeTokenPreview($token)
    {
        $token = (string) $token;
        if ($token === '') {
            return '';
        }

        if (strlen($token) <= 12) {
            return substr($token, 0, 3).'...';
        }

        return substr($token, 0, 6).'...'.substr($token, -6);
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
