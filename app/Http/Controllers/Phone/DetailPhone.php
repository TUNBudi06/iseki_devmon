<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use App\Models\PhoneList;
use Inertia\Inertia;

class DetailPhone extends Controller
{
    public function index(string $id)
    {
        $phone = PhoneList::with('brand')
            ->where('model_id', $id)
            ->where('approved', true)
            ->firstOrFail();

        return Inertia::render('Phone/PhoneDetail', [
            'phone' => $phone,
        ]);
    }
}
