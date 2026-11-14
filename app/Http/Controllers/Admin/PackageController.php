<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MembershipPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PackageController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $packages = MembershipPackage::query()
            ->withCount('memberships')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('facilities', 'like', "%{$search}%");
            })
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('is_active', $status === 'active');
            })
            ->orderByDesc('is_active')
            ->orderBy('duration_days')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Packages', [
            'packages' => $packages,
            'filters'  => [
                'q'      => $search,
                'status' => $status,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:membership_packages,name'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'price'         => ['required', 'integer', 'min:0'],
            'description'   => ['nullable', 'string'],
            'facilities'    => ['nullable', 'string'],
            'is_active'     => ['boolean'],
        ]);

        $package = MembershipPackage::create([
            'name'          => $validated['name'],
            'duration_days' => (int) $validated['duration_days'],
            'price'         => (int) $validated['price'],
            'description'   => $validated['description'] ?? null,
            'facilities'    => $validated['facilities'] ?? null,
            'is_active'     => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(
            action: 'create_package',
            entityType: MembershipPackage::class,
            entityId: $package->id,
            description: "Admin membuat paket membership baru: {$package->name}",
            newValues: $package->toArray()
        );

        return back()->with('status', "Paket {$package->name} berhasil ditambahkan.");
    }

    public function update(Request $request, MembershipPackage $package): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:membership_packages,name,' . $package->id],
            'duration_days' => ['required', 'integer', 'min:1'],
            'price'         => ['required', 'integer', 'min:0'],
            'description'   => ['nullable', 'string'],
            'facilities'    => ['nullable', 'string'],
            'is_active'     => ['boolean'],
        ]);

        $oldValues = $package->toArray();

        $package->update([
            'name'          => $validated['name'],
            'duration_days' => (int) $validated['duration_days'],
            'price'         => (int) $validated['price'],
            'description'   => $validated['description'] ?? null,
            'facilities'    => $validated['facilities'] ?? null,
            'is_active'     => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(
            action: 'update_package',
            entityType: MembershipPackage::class,
            entityId: $package->id,
            description: "Admin memperbarui paket membership: {$package->name}",
            oldValues: $oldValues,
            newValues: $package->toArray()
        );

        return back()->with('status', "Paket {$package->name} berhasil diperbarui.");
    }

    public function toggle(MembershipPackage $package): RedirectResponse
    {
        $package->update(['is_active' => ! $package->is_active]);

        $statusLabel = $package->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::record(
            action: 'toggle_package_status',
            entityType: MembershipPackage::class,
            entityId: $package->id,
            description: "Admin mengubah status paket {$package->name} menjadi {$statusLabel}"
        );

        return back()->with('status', "Paket {$package->name} berhasil {$statusLabel}.");
    }

    public function destroy(MembershipPackage $package): RedirectResponse
    {
        if ($package->memberships()->exists()) {
            // According to PRD 3.6: Paket yang telah digunakan tidak boleh dihapus permanen, nonaktifkan saja
            $package->update(['is_active' => false]);
            return back()->with('status', "Paket ini memiliki riwayat transaksi dan tidak dapat dihapus permanen, status diubah menjadi nonaktif.");
        }

        $packageName = $package->name;
        $package->delete();

        ActivityLog::record(
            action: 'delete_package',
            entityType: MembershipPackage::class,
            entityId: $package->id,
            description: "Admin menghapus paket membership: {$packageName}"
        );

        return back()->with('status', "Paket {$packageName} berhasil dihapus.");
    }
}
