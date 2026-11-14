<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashierTransaction;
use App\Models\GymSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));
        $group = $request->query('group', 'all');
        $status = $request->query('status', 'all');
        $paymentMethod = $request->query('payment_method', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = CashierTransaction::query()
            ->with(['member:id,full_name,email,phone,checkin_code', 'product:id,name', 'cashier:id,name'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('invoice', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($mq) => $mq->where('full_name', 'like', "%{$search}%"));
                });
            })
            ->when($group !== 'all', function ($q) use ($group) {
                $q->where('transaction_group', $group);
            })
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('payment_status', $status);
            })
            ->when($paymentMethod !== 'all', function ($q) use ($paymentMethod) {
                $q->where('payment_method', $paymentMethod);
            })
            ->when(! empty($startDate), function ($q) use ($startDate) {
                $q->whereDate('transaction_at', '>=', $startDate);
            })
            ->when(! empty($endDate), function ($q) use ($endDate) {
                $q->whereDate('transaction_at', '<=', $endDate);
            });

        $totalRevenue = (int) (clone $query)->where('payment_status', 'verified')->sum('amount');
        $totalTransactions = (clone $query)->count();

        $transactions = $query->latest('transaction_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Transactions', [
            'transactions'      => $transactions,
            'totalRevenue'      => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'filters'           => [
                'q'              => $search,
                'group'          => $group,
                'status'         => $status,
                'payment_method' => $paymentMethod,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
            ],
        ]);
    }

    public function show(CashierTransaction $transaction): Response
    {
        $transaction->load(['member', 'product', 'cashier', 'package']);
        $settings = GymSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Admin/TransactionDetail', [
            'transaction' => $transaction,
            'gymProfile'  => [
                'name'    => $settings['gym_name'] ?? 'Arena Gym & Fitness',
                'phone'   => $settings['gym_phone'] ?? '0812-3456-7890',
                'address' => $settings['gym_address'] ?? 'Jl. Arena Olahraga No. 10',
                'footer'  => $settings['invoice_footer'] ?? 'Terima kasih atas kunjungan Anda.',
            ],
        ]);
    }
}
