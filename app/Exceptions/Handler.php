<?php

namespace App\Exceptions;

use App\Http\Controllers\Traits\ResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

    protected $levels = [];

    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function unauthenticated($request, AuthenticationException $exception)
    {

        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->addSuccessResponse(498, trans('front.Unauthenticated_or_token_expired.'), []);
        }

        return redirect()->guest(route('login'));
    }
}
