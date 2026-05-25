<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use App\Models\Absence;
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

        // Riwayat pemakaian device ini dari absensi
        $usages = Absence::where('device_id', $id)
            ->orderByDesc('time_absence')
            ->take(50)
            ->get()
            ->map(fn ($a) => [
                'name' => $a->name,
                'nik' => $a->nik,
                'login' => $a->time_absence->format('Y-m-d H:i'),
                'note' => $a->catatan,
            ]);

        // User terakhir yang pakai device ini
        $latestUser = $usages->first();

        return Inertia::render('Phone/PhoneDetail', [
            'phone' => $phone,
            'usages' => $usages->values(),
            'latestUser' => $latestUser,
        ]);
    }
}
