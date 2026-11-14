<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\MembershipPackage;
use Inertia\Inertia;
use Inertia\Response;

class PackageController extends Controller
{
    public function index(): Response
    {
        $packages = MembershipPackage::query()
            ->where('is_active', true)
            ->orderBy('duration_days')
            ->get();

        return Inertia::render('Cashier/Packages', [
            'packages' => $packages,
        ]);
    }
}
