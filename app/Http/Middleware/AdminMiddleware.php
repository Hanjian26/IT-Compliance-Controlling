<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- penting!

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $level)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // validasi level sesuai parameter dari route: 'level:1' atau 'level:2'
        if ((string) Auth::user()->level !== (string) $level) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
