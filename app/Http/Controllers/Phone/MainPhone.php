<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class MainPhone extends Controller
{
    public function index()
    {
        return Inertia::render('Phone/Dashboard');
    }
}
