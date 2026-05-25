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
        $error = null;

        if (! $device) {
            $error = 'Perangkat tidak ditemukan.';
        } elseif (! $device->approved) {
            $error = 'Perangkat belum disetujui oleh admin. Silakan tunggu persetujuan.';
        } elseif (! $device->registered) {
            $error = 'Perangkat belum terdaftar. Silakan daftarkan perangkat terlebih dahulu.';
        }

        return Inertia::render('Member/LoginMember', [
            'device' => $device ? [
                'model_id' => $device->model_id,
                'model_name' => $device->model_name,
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

        // Latest absence on THIS device today
        $deviceLatestAbsence = Absence::where('device_id', $deviceId)
            ->whereDate('time_absence', today())
            ->latest('time_absence')
            ->first();

        // All devices for the monitoring tables
        $allDevices = PhoneList::where('approved', true)
            ->where('registered', true)
            ->with('brand')
            ->get();

        $absentDeviceIds = Absence::whereDate('time_absence', today())
            ->pluck('device_id')
            ->unique()
            ->toArray();

        $hasAbsence = $allDevices->filter(fn ($d) => in_array($d->model_id, $absentDeviceIds));
        $noAbsence = $allDevices->filter(fn ($d) => ! in_array($d->model_id, $absentDeviceIds));

        return Inertia::render('Member/DashboardMember', [
            'deviceLatest' => $deviceLatestAbsence ? [
                'nik' => $deviceLatestAbsence->nik,
                'name' => $deviceLatestAbsence->name,
                'time_absence' => $deviceLatestAbsence->time_absence->format('H:i:s'),
                'date_absence' => $deviceLatestAbsence->time_absence->format('d F Y'),
            ] : null,
            'currentDevice' => [
                'model_id' => $device->model_id,
                'model_name' => $device->model_name,
            ],
            'hasAbsence' => $hasAbsence->values()->map(fn ($d) => [
                'model_id' => $d->model_id,
                'model_name' => $d->model_name,
                'brand_name' => $d->brand?->name,
            ]),
            'noAbsence' => $noAbsence->values()->map(fn ($d) => [
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
