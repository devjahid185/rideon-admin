<?php

namespace App\Http\Middleware;

use App\Models\ApiHit;
use Closure;
use Illuminate\Http\Request;

class LogApiHitToMongo
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('api/*')) {
            ApiHit::create([
                'route' => $request->route()?->getName() ?? $request->path(),
                'method' => $request->method(),
                'user_id' => optional($request->user())->id,
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'meta' => [
                    'query' => $request->query(),
                    'status' => $response->getStatusCode(),
                ],
                'requested_at' => now(),
            ]);
        }

        return $response;
    }
}
