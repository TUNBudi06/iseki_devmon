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

        // Riwayat pengecekan device
        $checks = $phone->deviceChecks()
            ->orderByDesc('created_at')
            ->take(50)
            ->get()
            ->map(fn ($c) => [
                'date' => $c->created_at->format('Y-m-d H:i'),
                'user' => $c->checked_by_name,
                'username' => $c->checked_by_username,
                'status' => $c->imei_ok || $c->mac_ok ? 'ok' : 'info',
                'imei_ok' => $c->imei_ok,
                'mac_ok' => $c->mac_ok,
                'note' => $c->keterangan,
                'foto' => $c->foto,
            ]);

        // User terakhir yang pakai device ini
        $latestUser = $usages->first();

        return Inertia::render('Phone/PhoneDetail', [
            'phone' => $phone,
            'usages' => $usages->values(),
            'latestUser' => $latestUser,
            'checks' => $checks->values(),
        ]);
    }
}
