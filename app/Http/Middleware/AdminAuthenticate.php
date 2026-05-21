<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = session('admin_user');

        if (! $admin) {
            return redirect()->route('admin.loginAdmin');
        }

        return $next($request);
    }
}
