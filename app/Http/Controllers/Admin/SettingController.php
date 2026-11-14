<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\GymSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        $settings = GymSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Admin/Settings', [
            'settings' => array_merge([
                'gym_name'       => 'Arena Gym & Fitness',
                'gym_phone'      => '0812-3456-7890',
                'gym_address'    => 'Jl. Arena Olahraga No. 10, Jakarta Selatan',
                'gym_email'      => 'contact@arenagym.local',
                'invoice_prefix' => 'INV',
                'timezone'       => 'Asia/Jakarta',
                'invoice_footer' => 'Terima kasih atas kunjungan Anda di Arena Gym. Semangat latihan!',
            ], $settings),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gym_name'       => ['required', 'string', 'max:255'],
            'gym_phone'      => ['nullable', 'string', 'max:50'],
            'gym_address'    => ['nullable', 'string', 'max:500'],
            'gym_email'      => ['nullable', 'email', 'max:255'],
            'invoice_prefix' => ['required', 'string', 'max:10'],
            'timezone'       => ['required', 'string', 'max:50'],
            'invoice_footer' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            GymSetting::set($key, $value);
        }

        ActivityLog::record(
            action: 'update_settings',
            description: 'Admin memperbarui profil dan pengaturan sistem gym'
        );

        return back()->with('status', 'Pengaturan sistem & profil gym berhasil disimpan.');
    }
}
