<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ManagerMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->is_manager) {
            abort(403, 'Anda bukan manager');
        }

        return $next($request);
    }
}