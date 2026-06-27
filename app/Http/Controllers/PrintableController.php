<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\PhoneList;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PrintableController extends Controller
{
    /**
     * Select devices for QR printing.
     */
    public function selectDevices(): Response
    {
        return Inertia::render('Admin/PrintQrSelect', [
            'phoneLists' => fn () => PhoneList::with('brand')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->take(500)
                ->get(),
            'departemenOptions' => fn () => Departemen::orderBy('id')->get(),
        ]);
    }

    /**
     * Print QR codes for selected devices.
     * Accepts comma-separated device IDs via ?ids= query param.
     */
    public function index(Request $request): Response
    {
        $ids = $request->query('ids');

        if ($ids) {
            $idArray = array_map('intval', explode(',', $ids));
            $device = PhoneList::with('brand')
                ->whereIn('id', $idArray)
                ->whereNull('deleted_at')
                ->orderByRaw('FIELD(id, '.implode(',', $idArray).')')
                ->get();
        } else {
            $device = PhoneList::with('brand')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->limit(16)
                ->get();
        }

        return Inertia::render('Print/PrintQr', [
            'data' => $device,
        ]);
    }
}
