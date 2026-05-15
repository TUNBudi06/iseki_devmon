<?php

namespace App\Http\Middleware\User;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangingSessionTimeConfigUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('session.lifetime') == 120) {
            config(['session.lifetime' => 720]);
        }

        return $next($request);
    }
}
