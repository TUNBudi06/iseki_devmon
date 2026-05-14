<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function loginAdminPanel()
    {
        return Inertia::render('Account/AdminLoginPage');
    }

    public function loginAdmin(Request $req)
    {
        $data = $req->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = User::where('username', $data['username'])->first();
        if (! $admin || ! ($admin->password == $data['password'])) {
            return response()->json(['errors' => ['username' => 'Username atau password tidak valid']], 422);
        }

        session()->put('ID', $admin->id);
        session()->put('name', $admin->name);
        session()->put('role', 'admin');

        return response()->json(['message' => 'Login berhasil']);
    }

    public function logoutAdmin()
    {
        session()->invalidate();

        return redirect()->route('home');
    }
}
