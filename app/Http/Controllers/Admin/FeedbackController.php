<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function index(): Response
    {
        $feedbacks = MemberFeedback::query()
            ->latest()
            ->paginate(15)
            ->through(fn (MemberFeedback $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'subject' => $item->subject,
                'message' => $item->message,
                'is_read' => (bool) $item->read_at,
                'read_at' => $item->read_at?->translatedFormat('d M Y, H:i'),
                'created_at' => $item->created_at?->translatedFormat('d M Y, H:i'),
            ]);

        return Inertia::render('Admin/Feedbacks', [
            'pageTitle' => 'Kritik & Saran - Arena Gym',
            'activePage' => 'feedbacks',
            'feedbacks' => $feedbacks,
            'unreadCount' => MemberFeedback::query()->whereNull('read_at')->count(),
        ]);
    }

    public function markRead(Request $request, MemberFeedback $feedback): JsonResponse|RedirectResponse
    {
        $feedback->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Pesan ditandai sudah dibaca.');
    }

    public function destroy(MemberFeedback $feedback): RedirectResponse
    {
        $feedback->delete();

        return back()->with('status', 'Kritik dan saran berhasil dihapus.');
    }
}

