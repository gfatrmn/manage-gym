<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use App\Models\MemberHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Cashier – Semua Transaksi & Penjualan Produk
|--------------------------------------------------------------------------
*/

// Daftar transaksi (member, daily pass, checkout)
Route::get('/transactions', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $viewData = RouteHelpers::buildCashierViewData([
        'pageTitle'  => 'Transaksi - Kasir Arena Gym',
        'activePage' => 'cashier.transactions',
    ]);

    $typeAliases = [
        'member' => 'member_payment',
        'daily_pass' => 'daily_pass',
        'daily-pass' => 'daily_pass',
        'product' => 'product_sale',
        'checkout' => 'all',
    ];

    $typeFilter = (string) $request->query('type', $request->query('section', 'all'));
    $typeFilter = $typeAliases[$typeFilter] ?? $typeFilter;
    $typeFilter = in_array($typeFilter, ['all', 'member_payment', 'daily_pass', 'product_sale', 'other'], true)
        ? $typeFilter
        : 'all';

    $statusFilter = in_array($request->query('status'), ['all', 'verified', 'pending'], true)
        ? $request->query('status')
        : 'all';

    $methodFilter = in_array($request->query('method'), ['all', 'cash', 'qris'], true)
        ? $request->query('method')
        : 'all';

    $periodFilter = in_array($request->query('period'), ['today', 'month', 'all'], true)
        ? $request->query('period')
        : 'today';

    $search = trim((string) $request->query('q', ''));
    $allPaymentTransactions = collect($viewData['transactions'])->values();

    $paymentHistory = $allPaymentTransactions
        ->when($typeFilter !== 'all', fn ($items) => $items->where('transaction_group', $typeFilter))
        ->when($statusFilter !== 'all', fn ($items) => $items->where('payment_status', $statusFilter))
        ->when($methodFilter !== 'all', fn ($items) => $items->where('payment_method', $methodFilter))
        ->when($periodFilter === 'today', fn ($items) => $items->filter(fn (CashierTransaction $t) => $t->transaction_at?->isToday()))
        ->when($periodFilter === 'month', fn ($items) => $items->filter(fn (CashierTransaction $t) => $t->transaction_at?->isSameMonth(now())))
        ->when($search !== '', function ($items) use ($search) {
            $needle = str()->lower($search);

            return $items->filter(function (CashierTransaction $transaction) use ($needle) {
                return str_contains(str()->lower($transaction->invoice ?? ''), $needle)
                    || str_contains(str()->lower($transaction->customer_name ?? ''), $needle)
                    || str_contains(str()->lower($transaction->transaction_type ?? ''), $needle)
                    || str_contains(str()->lower($transaction->transaction_group ?? ''), $needle)
                    || str_contains(str()->lower($transaction->payment_method ?? ''), $needle)
                    || str_contains((string) ($transaction->amount ?? ''), $needle);
            });
        })
        ->values();

    $todayTransactions = $allPaymentTransactions
        ->filter(fn (CashierTransaction $transaction) => $transaction->transaction_at?->isToday())
        ->values();
    $todayVerifiedTransactions = $todayTransactions
        ->where('payment_status', 'verified')
        ->values();

    return view('cashier.transactions', array_merge($viewData, [
        'paymentHistory' => $paymentHistory,
        'paymentFilters' => [
            'type' => $typeFilter,
            'status' => $statusFilter,
            'method' => $methodFilter,
            'period' => $periodFilter,
            'q' => $search,
        ],
        'paymentSummary' => [
            'shown_count' => $paymentHistory->count(),
            'shown_total' => $paymentHistory->where('payment_status', 'verified')->sum('amount'),
            'today_count' => $todayTransactions->count(),
            'today_total' => $todayVerifiedTransactions->sum('amount'),
            'pending_count' => $allPaymentTransactions->where('payment_status', '!=', 'verified')->count(),
            'all_count' => $allPaymentTransactions->count(),
        ],
    ]));
})->name('transactions');

// ── Penjualan produk (index) ──────────────────────────────────────────────────
Route::get('/transactions/products', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $viewData = RouteHelpers::buildCashierViewData([
        'pageTitle'  => 'Penjualan Produk - Kasir Arena Gym',
        'activePage' => 'cashier.product-transactions',
    ]);

    $selectedMonth = (string) $request->query('month', Carbon::now()->format('Y-m'));

    try {
        $monthStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
    } catch (\Exception $e) {
        $monthStart    = Carbon::now()->startOfMonth();
        $selectedMonth = $monthStart->format('Y-m');
    }

    $monthEnd = (clone $monthStart)->endOfMonth();

    $selectedMember      = null;
    $selectedCustomerName = trim((string) $request->query('customer_name', ''));

    if ($request->query('gym_member_id')) {
        $selectedMember       = GymMember::query()->find($request->query('gym_member_id'));
        $selectedCustomerName = $selectedCustomerName ?: ($selectedMember?->full_name ?? '');
    }

    return view('cashier.product-transactions', array_merge($viewData, [
        'products' => Product::query()
            ->orderByDesc('is_active')
            ->orderBy('category')
            ->orderBy('name')
            ->get(),
        'productTransactions' => collect($viewData['transactions'])
            ->filter(fn (CashierTransaction $t) => $t->product_id !== null && $t->transaction_at->between($monthStart, $monthEnd))
            ->values(),
        'members'             => GymMember::query()->where('status', 'member')->orderBy('full_name')->get(),
        'selectedMember'      => $selectedMember,
        'selectedCustomerName'=> $selectedCustomerName,
        'selectedMonth'       => $selectedMonth,
    ]));
})->name('transactions.products');

// ── Store transaksi (produk & lain-lain) ──────────────────────────────────────
Route::post('/transactions', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $transactionGroup = $request->input('transaction_group', 'other');

    // ── Penjualan produk ──────────────────────────────────────────────────────
    if ($transactionGroup === 'product_sale') {
        $validated = $request->validate([
            'gym_member_id'  => ['nullable', 'exists:gym_members,id'],
            'customer_name'  => ['required_without:gym_member_id', 'nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:cash,qris'],
            'paid_amount'    => ['nullable', 'integer', 'min:0'],
            'product_ids'    => ['required', 'array', 'min:1'],
            'product_ids.*'  => ['required', 'exists:products,id'],
            'quantities'     => ['required', 'array'],
            'notes'          => ['nullable', 'string'],
        ]);

        $paymentMethod = $validated['payment_method'];
        $paymentStatus = $paymentMethod === 'cash' ? 'verified' : 'pending';
        $receiptStatus = $paymentStatus === 'verified' ? 'ready' : 'pending';

        $products = Product::query()
            ->whereIn('id', $validated['product_ids'])
            ->get()
            ->keyBy('id');

        // Validasi stok & status aktif sebelum menyimpan apapun
        $createdTransactions = collect();

        foreach ($validated['product_ids'] as $productId) {
            $product  = $products->get((int) $productId);

            if (! $product || ! $product->is_active) {
                return redirect()->route('cashier.transactions.products')
                    ->withErrors(['product_ids' => 'Ada produk yang sudah nonaktif dan tidak bisa dijual.'])
                    ->withInput();
            }

            $quantity = max((int) data_get($validated, 'quantities.' . $product->id, 1), 1);

            if ($product->stock < $quantity) {
                return redirect()->route('cashier.transactions.products')
                    ->withErrors(['product_ids' => "Stok {$product->name} tidak mencukupi untuk jumlah yang dipilih."])
                    ->withInput();
            }
        }

        $customerName = $validated['customer_name'] ?? null;
        if (empty($customerName) && ! empty($validated['gym_member_id'])) {
            $customerName = GymMember::query()->find($validated['gym_member_id'])?->full_name ?? '';
        }

        $totalAmount = collect($validated['product_ids'])->sum(function ($productId) use ($products, $validated) {
            $product = $products->get((int) $productId);
            $quantity = $product ? max((int) data_get($validated, 'quantities.' . $product->id, 1), 1) : 1;

            return $product ? ($product->price * $quantity) : 0;
        });
        $paidAmount = $paymentMethod === 'qris'
            ? (int) $totalAmount
            : (int) ($validated['paid_amount'] ?? 0);

        if ($paymentMethod === 'cash' && $paidAmount < $totalAmount) {
            return redirect()->route('cashier.transactions.products')
                ->withErrors(['paid_amount' => 'Uang diterima tidak boleh kurang dari total checkout.'])
                ->withInput();
        }

        $remainingPaidAmount = $paidAmount;

        foreach ($validated['product_ids'] as $productId) {
            $product  = $products->get((int) $productId);
            $quantity = max((int) data_get($validated, 'quantities.' . $product->id, 1), 1);
            $lineAmount = $product->price * $quantity;
            $linePaidAmount = $paymentMethod === 'qris'
                ? $lineAmount
                : min($remainingPaidAmount, $lineAmount);
            $remainingPaidAmount = max($remainingPaidAmount - $lineAmount, 0);
            $lineChangeAmount = $paymentMethod === 'cash' && $remainingPaidAmount > 0 && $productId === collect($validated['product_ids'])->last()
                ? $remainingPaidAmount
                : 0;

            $transaction = CashierTransaction::create([
                'invoice'          => RouteHelpers::generateInvoice('PRD'),
                'gym_member_id'    => $validated['gym_member_id'] ?? null,
                'product_id'       => $product->id,
                'customer_name'    => $customerName,
                'transaction_group'=> 'product_sale',
                'transaction_type' => $product->name,
                'amount'           => $lineAmount,
                'paid_amount'      => $linePaidAmount + $lineChangeAmount,
                'change_amount'    => $lineChangeAmount,
                'quantity'         => $quantity,
                'payment_method'   => $paymentMethod,
                'payment_status'   => $paymentStatus,
                'receipt_status'   => $receiptStatus,
                'transaction_at'   => now(),
                'notes'            => $validated['notes'] ?? null,
            ]);

            $createdTransactions->push($transaction);

            if (! empty($validated['gym_member_id'])) {
                MemberHistory::create([
                    'gym_member_id' => $validated['gym_member_id'],
                    'product_id'    => $product->id,
                    'history_type'  => 'product_purchase',
                    'occurred_at'   => $transaction->transaction_at,
                    'title'         => $product->name,
                    'description'   => 'Pembelian barang',
                    'quantity'      => $quantity,
                    'amount'        => $transaction->amount,
                    'source_type'   => CashierTransaction::class,
                    'source_id'     => $transaction->id,
                ]);
            }

            $product->decrement('stock', $quantity);
        }

        if ($paymentMethod === 'cash' && $createdTransactions->isNotEmpty()) {
            return redirect()->route('cashier.transactions.products')
                ->with('status', 'Pembayaran produk tunai berhasil dicatat. Struk bisa dicetak dari daftar transaksi produk.');
        }

        return redirect()->route('cashier.receipts')
            ->with('status', 'Checkout produk QRIS berhasil dicatat. Verifikasi pembayaran sebelum mencetak struk.');
    }

    // ── Transaksi lain (other) ────────────────────────────────────────────────
    $validated = $request->validate([
        'customer_name'    => ['required', 'string', 'max:255'],
        'transaction_type' => ['required', 'string', 'max:255'],
        'amount'           => ['required', 'integer', 'min:1'],
        'paid_amount'      => ['nullable', 'integer', 'min:0'],
        'payment_method'   => ['required', 'in:cash,qris'],
        'notes'            => ['nullable', 'string'],
    ]);

    $paymentStatus = $validated['payment_method'] === 'cash' ? 'verified' : 'pending';
    $paidAmount = $validated['payment_method'] === 'qris'
        ? (int) $validated['amount']
        : (int) ($validated['paid_amount'] ?? 0);

    if ($validated['payment_method'] === 'cash' && $paidAmount < (int) $validated['amount']) {
        return back()
            ->withErrors(['paid_amount' => 'Uang diterima tidak boleh kurang dari nominal transaksi.'])
            ->withInput();
    }

    $transaction = CashierTransaction::create([
        'invoice'          => RouteHelpers::generateInvoice('TRX'),
        'gym_member_id'    => null,
        'product_id'       => null,
        'customer_name'    => $validated['customer_name'],
        'transaction_group'=> 'other',
        'transaction_type' => $validated['transaction_type'],
        'amount'           => $validated['amount'],
        'paid_amount'      => $paidAmount,
        'change_amount'    => max($paidAmount - (int) $validated['amount'], 0),
        'quantity'         => 1,
        'payment_method'   => $validated['payment_method'],
        'payment_status'   => $paymentStatus,
        'receipt_status'   => $paymentStatus === 'verified' ? 'ready' : 'pending',
        'transaction_at'   => now(),
        'notes'            => $validated['notes'] ?? null,
    ]);

    if ($paymentStatus === 'verified') {
        return redirect()->route('cashier.transactions')
            ->with('status', "Transaksi tunai {$transaction->invoice} berhasil dicatat. Struk bisa dicetak dari bukti pembayaran.");
    }

    return redirect()->route('cashier.receipts')
        ->with('status', 'Transaksi QRIS berhasil dicatat. Verifikasi pembayaran sebelum mencetak struk.');
})->name('transactions.store');

Route::post('/transactions/register-member', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $validated = $request->validate([
        'full_name' => ['required', 'string', 'max:255'],
        'phone' => ['nullable', 'string', 'max:30'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'username' => ['required', 'string', 'max:255', 'unique:users,login'],
        'password' => ['required', 'string', 'min:8'],
        'payment_method' => ['required', 'in:cash,qris'],
        'duration' => ['required', 'integer', 'in:1,3,6,12'],
        'payment_amount' => ['nullable', 'integer', 'min:1'],
        'paid_amount' => ['nullable', 'integer', 'min:0'],
        'notes' => ['nullable', 'string'],
    ]);

    $joinedAt = now()->startOfDay();
    $durationMonths = (int) $validated['duration'];
    $defaultPricePerMonth = 90000;
    $amount = (int) ($validated['payment_amount'] ?? ($durationMonths * $defaultPricePerMonth));
    $paymentStatus = $validated['payment_method'] === 'cash' ? 'verified' : 'pending';
    $paidAmount = $validated['payment_method'] === 'qris'
        ? $amount
        : (int) ($validated['paid_amount'] ?? 0);

    if ($validated['payment_method'] === 'cash' && $paidAmount < $amount) {
        return back()
            ->withErrors(['paid_amount' => 'Uang diterima tidak boleh kurang dari nominal pembayaran.'])
            ->withInput();
    }

    $changeAmount = max($paidAmount - $amount, 0);

    $memberUser = User::query()->create([
        'name' => $validated['full_name'],
        'login' => $validated['username'],
        'email' => $validated['email'],
        'role' => 'member',
        'password' => Hash::make($validated['password']),
    ]);

    $memberPayload = [
        'user_id' => $memberUser->id,
        'full_name' => $validated['full_name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'member_status' => 'member',
        'membership_plan' => "Membership {$durationMonths} Bulan",
        'package_status' => 'active',
        'payment_amount' => $amount,
        'can_check_in' => false,
        'joined_at' => $joinedAt,
        'expires_at' => $joinedAt->copy()->addMonthsNoOverflow($durationMonths),
        'payment_method' => $validated['payment_method'],
        'status' => 'member',
        'checkin_code' => 'AGM-' . strtoupper(Str::random(8)),
        'notes' => $validated['notes'] ?? null,
    ];

    $memberColumns = Schema::getColumnListing('gym_members');
    $memberPayload = collect($memberPayload)
        ->filter(fn ($value, $key) => in_array($key, $memberColumns, true))
        ->all();

    $member = GymMember::query()->create($memberPayload);

    CashierTransaction::query()->create([
        'invoice' => RouteHelpers::generateInvoice('MBR'),
        'gym_member_id' => $member->id,
        'product_id' => null,
        'customer_name' => $member->full_name,
        'transaction_group' => 'member_payment',
        'transaction_type' => "Aktivasi Member {$durationMonths} Bulan",
        'amount' => $amount,
        'paid_amount' => $paidAmount,
        'change_amount' => $changeAmount,
        'quantity' => $durationMonths,
        'payment_method' => $validated['payment_method'],
        'payment_status' => $paymentStatus,
        'receipt_status' => $paymentStatus === 'verified' ? 'ready' : 'pending',
        'transaction_at' => now(),
        'notes' => $validated['notes'] ?? null,
    ]);

    $message = $paymentStatus === 'verified'
        ? 'Pendaftaran member berhasil dan transaksi sudah tercatat.'
        : 'Pendaftaran member berhasil. Menunggu verifikasi pembayaran QRIS.';

    return redirect()->route('cashier.transactions.register-member.form')
        ->with('status', $message . ' Username dan password awal sudah siap diberikan ke member.')
        ->with('member_credentials', [
            'name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
})->name('transactions.register-member');

Route::get('/transactions/register-member', function () {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $viewData = RouteHelpers::buildCashierViewData([
        'pageTitle' => 'Daftarkan Member - Kasir Arena Gym',
        'activePage' => 'cashier.transactions',
    ]);

    $registerMemberHistory = collect($viewData['memberPayments'] ?? [])
        ->filter(fn (CashierTransaction $item) => str_contains(strtolower((string) $item->transaction_type), 'aktivasi member'))
        ->take(10)
        ->values();

    return view('cashier.register-member', array_merge($viewData, [
        'registerMemberHistory' => $registerMemberHistory,
    ]));
})->name('transactions.register-member.form');

