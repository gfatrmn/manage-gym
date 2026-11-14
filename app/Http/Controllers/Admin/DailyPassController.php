<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\DailyGuest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DailyPassController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim($request->string('q')->value());

        $query = DailyGuest::query()->latest('visit_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $dailyPasses = $query->paginate(10)->withQueryString()->through(fn (DailyGuest $guest) => [
            'id' => $guest->id,
            'full_name' => $guest->full_name,
            'phone' => $guest->phone ?: '-',
            'payment_amount' => $guest->payment_amount,
            'payment_amount_formatted' => RouteHelpers::formatCurrency($guest->payment_amount),
            'payment_method' => strtoupper((string) $guest->payment_method),
            'visit_at' => $guest->visit_at?->translatedFormat('d M Y, H:i') ?? $guest->created_at?->translatedFormat('d M Y, H:i'),
        ]);

        return Inertia::render('Admin/DailyPasses', [
            'pageTitle' => 'Daily Pass - Arena Gym',
            'activePage' => 'daily-passes',
            'dailyPasses' => $dailyPasses,
            'search' => $search,
            'todayCount' => DailyGuest::query()->whereDate('created_at', today())->count(),
            'todayRevenue' => RouteHelpers::formatCurrency((int) DailyGuest::query()->whereDate('created_at', today())->sum('payment_amount')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'payment_method' => ['required', 'string', 'max:30'],
            'payment_amount' => ['nullable', 'integer', 'min:1'],
        ]);

        DailyGuest::query()->create([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'payment_amount' => (int) ($validated['payment_amount'] ?? 30000),
            'payment_method' => $validated['payment_method'],
            'visit_at' => now(),
        ]);

        return redirect()->route('admin.daily-passes')->with('status', 'Data kunjungan daily pass berhasil ditambahkan.');
    }

    public function destroy(DailyGuest $dailyPass): RedirectResponse
    {
        $dailyPass->delete();

        return redirect()->route('admin.daily-passes')->with('status', 'Data kunjungan daily pass berhasil dihapus.');
    }
}

