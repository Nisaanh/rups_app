<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        if (!Auth::user()->can($permission)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}