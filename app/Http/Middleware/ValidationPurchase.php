<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidationPurchase
{
    public function handle(Request $request, Closure $next)
    {

        if (! $request->query('isValidated', false)) {
            return redirect()->route('installer.purchaseValidation-error');
        }

        return $next($request);
    }
}
