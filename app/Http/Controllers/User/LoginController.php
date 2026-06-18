<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Employee;
use App\Models\PhoneList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function index(string $deviceId)
    {
        $device = PhoneList::where('model_id', $deviceId)->first();

        if (! $device || ! $device->approved || ! $device->registered) {
            return redirect()->route('home');
        }

        // Jika sudah ada absensi hari ini, langsung ke dashboard
        $hasAbsenceToday = Absence::where('device_id', $deviceId)
            ->whereDate('time_absence', today())
            ->exists();

        if ($hasAbsenceToday) {
            return redirect()->route('user.dashboard', ['device_id' => $deviceId]);
        }

        return Inertia::render('Member/LoginMember', [
            'device' => [
                'model_id' => $device->model_id,
                'model_name' => $device->model_name,
                'imei' => $device->imei,
                'mac_address' => $device->mac_address,
                'approved' => $device->approved,
                'registered' => $device->registered,
            ],
            'error' => null,
        ]);
    }

    public function inputForm(string $deviceId)
    {
        $device = PhoneList::where('model_id', $deviceId)->first();
        $error = null;

        if (! $device) {
            $error = 'Perangkat tidak ditemukan.';
        } elseif (! $device->approved) {
            $error = 'Perangkat belum disetujui oleh admin. Silakan tunggu persetujuan.';
        } elseif (! $device->registered) {
            $error = 'Perangkat belum terdaftar. Silakan daftarkan perangkat terlebih dahulu.';
        }

        return Inertia::render('Member/LoginMemberInput', [
            'device' => $device ? [
                'model_id' => $device->model_id,
                'model_name' => $device->model_name,
                'imei' => $device->imei,
                'mac_address' => $device->mac_address,
                'approved' => $device->approved,
                'registered' => $device->registered,
            ] : null,
            'error' => $error,
        ]);
    }

    public function store(Request $request, string $deviceId): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:6'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $device = PhoneList::where('model_id', $deviceId)->first();

        if (! $device || ! $device->approved || ! $device->registered) {
            return response()->json(['message' => 'Perangkat tidak valid atau belum disetujui.'], 422);
        }

        $employee = Employee::where('nik', (int) $validated['nik'])->first();

        if (! $employee) {
            return response()->json(['message' => 'NIK tidak ditemukan dalam data karyawan.'], 422);
        }

        if ($employee->password !== $validated['password']) {
            return response()->json(['message' => 'Password salah.'], 422);
        }

        Absence::create([
            'device_id' => $deviceId,
            'nik' => $validated['nik'],
            'name' => $employee->nama,
            'time_absence' => now(),
        ]);

        return response()->json(['success' => true, 'device_id' => $deviceId]);
    }

    public function dashboard(string $deviceId)
    {
        $device = PhoneList::where('model_id', $deviceId)->first();

        if (! $device || ! $device->approved || ! $device->registered) {
            return redirect()->route('user.deviceNotRegister');
        }

        return Inertia::render('Member/DashboardMember', [
            'deviceLatest' => function () use ($deviceId) {
                $abs = Absence::where('device_id', $deviceId)
                    ->whereDate('time_absence', today())
                    ->latest('time_absence')
                    ->first();
                if (! $abs) {
                    return null;
                }

                return [
                    'id' => $abs->id,
                    'nik' => $abs->nik,
                    'name' => $abs->name,
                    'time_absence' => $abs->time_absence->format('d/m/Y H:i:s'),
                    'date_absence' => $abs->time_absence->format('d F Y'),
                    'catatan' => $abs->catatan,
                ];
            },
            'currentDevice' => [
                'model_id' => $device->model_id,
                'model_name' => $device->model_name,
                'hash_token' => $device->hash_token,
            ],
            'hasAbsence' => fn () => PhoneList::where('approved', true)
                ->where('registered', true)
                ->with('brand')
                ->get()
                ->filter(fn ($d) => Absence::whereDate('time_absence', today())
                    ->where('device_id', $d->model_id)
                    ->exists()
                )
                ->values()
                ->map(fn ($d) => [
                    'model_id' => $d->model_id,
                    'model_name' => $d->model_name,
                    'brand_name' => $d->brand?->name,
                    'latest_user_name' => Absence::whereDate('time_absence', today())
                        ->where('device_id', $d->model_id)
                        ->latest('time_absence')
                        ->first()?->name,
                    'latest_user_nik' => Absence::whereDate('time_absence', today())
                        ->where('device_id', $d->model_id)
                        ->latest('time_absence')
                        ->first()?->nik,
                    'latest_time' => Absence::whereDate('time_absence', today())
                        ->where('device_id', $d->model_id)
                        ->latest('time_absence')
                        ->first()?->time_absence?->format('H:i'),
                ]),
            'noAbsence' => fn () => PhoneList::where('approved', true)
                ->where('registered', true)
                ->with('brand')
                ->get()
                ->reject(fn ($d) => Absence::whereDate('time_absence', today())
                    ->where('device_id', $d->model_id)
                    ->exists()
                )
                ->values()
                ->map(fn ($d) => [
                    'model_id' => $d->model_id,
                    'model_name' => $d->model_name,
                    'brand_name' => $d->brand?->name,
                ]),
        ]);
    }

    public function updateCatatan(Request $request, string $deviceId)
    {
        $validated = $request->validate([
            'absence_id' => ['required', 'integer', 'exists:absences,id'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $absence = Absence::findOrFail($validated['absence_id']);

        if ($absence->device_id !== $deviceId) {
            return back()->withErrors(['error' => 'Absensi tidak valid.']);
        }

        $absence->update(['catatan' => $validated['catatan']]);

        return back();
    }
}
