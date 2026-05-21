<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DeviceManagement extends Controller
{
    //
    public function index()
    {
        return Inertia::render('Member/PhoneNotRegister');
    }

    public function registerViaQR()
    {
        return Inertia::render('Member/PhoneRegisterViaQR');
    }
}
