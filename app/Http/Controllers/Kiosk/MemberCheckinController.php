<?php

namespace App\Http\Controllers\Kiosk;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberCheckinController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Checkin/MemberSelfService', [
            'pageTitle' => 'Check-in Member Arena Gym',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return RouteHelpers::storeMemberCheckin(
            request: $request,
            actor: 'qr_member',
            redirectRoute: 'member.checkin'
        );
    }
}

