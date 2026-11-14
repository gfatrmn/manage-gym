<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));
        $action = $request->query('action', 'all');

        $logs = ActivityLog::query()
            ->with('user:id,name,role')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            })
            ->when($action !== 'all', function ($q) use ($action) {
                $q->where('action', $action);
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $actionTypes = ActivityLog::query()
            ->distinct()
            ->pluck('action')
            ->values();

        return Inertia::render('Admin/ActivityLogs', [
            'logs'        => $logs,
            'actionTypes' => $actionTypes,
            'filters'     => [
                'q'      => $search,
                'action' => $action,
            ],
        ]);
    }
}
