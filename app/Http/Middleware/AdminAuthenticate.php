<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    protected int $timeout = 7200; // 2 hours in seconds

    public function handle(Request $request, Closure $next): Response
    {
        $admin = session('admin_user');

        if (! $admin) {
            return redirect()->route('admin.loginAdmin');
        }

        // Check session expiry
        $lastActivity = session('admin_last_activity');
        if ($lastActivity && (time() - $lastActivity) > $this->timeout) {
            $request->session()->forget('admin_user');
            $request->session()->forget('admin_last_activity');
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.loginAdmin');
        }

        // Update last activity timestamp
        session(['admin_last_activity' => time()]);

        return $next($request);
    }
}
