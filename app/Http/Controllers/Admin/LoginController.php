<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/LoginAdmin');
    }

    public function authenticate(Request $request): JsonResponse
    {
        //        $request->validate([
        //            'username' => ['required', 'string'],
        //            'password' => ['required', 'string'],
        //        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && $user->password === $request->password) {
            $request->session()->regenerate();

            session([
                'admin_user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                ],
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['message' => 'Invalid credentials'], 422);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
