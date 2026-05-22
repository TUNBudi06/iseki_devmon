<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use App\Models\PhoneList;
use Inertia\Inertia;

class MainPhone extends Controller
{
    public function index()
    {
        $devices = PhoneList::with('brand')
            ->where('approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Phone/Dashboard', [
            'devices' => $devices,
        ]);
    }
}
