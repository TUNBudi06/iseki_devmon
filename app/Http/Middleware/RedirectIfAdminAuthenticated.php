<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('admin_user')) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
