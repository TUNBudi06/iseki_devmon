<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class redirectIfHadLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('name') && session()->has('role') && session()->get('role') == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
