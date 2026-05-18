<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DetailPhone extends Controller
{
    public function index(string $id)
    {
        return Inertia::render('Phone/PhoneDetail',[
            'id'=>$id
        ]);
    }
}
