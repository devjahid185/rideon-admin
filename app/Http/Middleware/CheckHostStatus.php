<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckHostStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('appUser')->user();
        if (! $user) {

            return redirect()->route('vendor.login');
        }

        if ((string) $user->host_status !== '1') {
            if ((string) $user->host_status === '2') {
                session()->flash('status', 'Your host request is pending. Please wait for admin approval.');

                return redirect()->route('vendor.login');
            }

            return redirect()->route('vendor.hostRequestForm');
        }

        return $next($request);
    }
}
