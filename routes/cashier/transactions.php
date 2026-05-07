<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cashier – Semua Transaksi & Penjualan Produk
|--------------------------------------------------------------------------
*/

// ── Daftar transaksi (member, non-member, checkout) ───────────────────────────
Route::get('/transactions', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $viewData = RouteHelpers::buildCashierViewData([
        'pageTitle'  => 'Pembayaran - Kasir Arena Gym',
        'activePage' => 'cashier.transactions',
    ]);

    $section = in_array($request->query('section'), ['member', 'non_member', 'checkout'], true)
        ? $request->query('section')
        : 'member';

    $search = trim((string) $request->query('q', ''));

    $filterByName = fn ($transactions) => collect($transactions)
        ->when(
            $search !== '',
            fn ($col) => $col->filter(
                fn (CashierTransaction $t) => str_contains(
                    str()->lower($t->customer_name),
                    str()->lower($search)
                )
            )
        )
        ->values();

    $checkoutCheckins = collect($viewData['cashierTodayCheckins'])
        ->when(
            $search !== '',
            fn ($col) => $col->filter(
                fn (GymCheckin $c) => str_contains(
                    str()->lower($c->member?->full_name ?? $c->submitted_name ?? ''),
                    str()->lower($search)
                ) || str_contains(
                    str()->lower($c->member?->phone ?? $c->submitted_phone ?? ''),
                    str()->lower($search)
                )
            )
        )
        ->values();

    $checkoutDailyPayments = collect($viewData['dailyPayments'])
        ->filter(fn (CashierTransaction $t) => $t->transaction_at->isToday())
        ->when(
            $search !== '',
            fn ($col) => $col->filter(
                fn (CashierTransaction $t) => str_contains(
                    str()->lower($t->customer_name),
                    str()->lower($search)
                )
            )
        )
        ->values();

    return view('cashier.transactions', array_merge($viewData, [
        'transactionSection'    => $section,
        'transactionSearch'     => $search,
        'memberPayments'        => $filterByName($viewData['memberPayments']),
        'dailyPayments'         => $filterByName($viewData['dailyPayments']),
        'checkoutCheckins'      => $checkoutCheckins,
        'checkoutDailyPayments' => $checkoutDailyPayments,
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
        'members'             => GymMember::query()->where('member_status', 'member')->orderBy('full_name')->get(),
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
        foreach ($validated['product_ids'] as $productId) {
            $product  = $products->get((int) $productId);
            $quantity = max((int) data_get($validated, 'quantities.' . $product->id, 1), 1);

            if (! $product || ! $product->is_active) {
                return redirect()->route('cashier.transactions.products')
                    ->withErrors(['product_ids' => 'Ada produk yang sudah nonaktif dan tidak bisa dijual.'])
                    ->withInput();
            }

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

        foreach ($validated['product_ids'] as $productId) {
            $product  = $products->get((int) $productId);
            $quantity = max((int) data_get($validated, 'quantities.' . $product->id, 1), 1);

            CashierTransaction::create([
                'invoice'          => RouteHelpers::generateInvoice('PRD'),
                'gym_member_id'    => $validated['gym_member_id'] ?? null,
                'product_id'       => $product->id,
                'customer_name'    => $customerName,
                'transaction_group'=> 'product_sale',
                'transaction_type' => $product->name,
                'amount'           => $product->price * $quantity,
                'quantity'         => $quantity,
                'payment_method'   => $paymentMethod,
                'payment_status'   => $paymentStatus,
                'receipt_status'   => $receiptStatus,
                'transaction_at'   => now(),
                'notes'            => $validated['notes'] ?? null,
            ]);

            $product->decrement('stock', $quantity);
        }

        return redirect()->route('cashier.transactions.products')
            ->with('status', 'Produk berhasil dicatat. Periksa status pembayaran sesuai metode yang dipilih.');
    }

    // ── Transaksi lain (other) ────────────────────────────────────────────────
    $validated = $request->validate([
        'customer_name'    => ['required', 'string', 'max:255'],
        'transaction_type' => ['required', 'string', 'max:255'],
        'amount'           => ['required', 'integer', 'min:1'],
        'payment_method'   => ['required', 'in:cash,qris'],
        'notes'            => ['nullable', 'string'],
    ]);

    $paymentStatus = $validated['payment_method'] === 'cash' ? 'verified' : 'pending';

    CashierTransaction::create([
        'invoice'          => RouteHelpers::generateInvoice('TRX'),
        'gym_member_id'    => null,
        'product_id'       => null,
        'customer_name'    => $validated['customer_name'],
        'transaction_group'=> 'other',
        'transaction_type' => $validated['transaction_type'],
        'amount'           => $validated['amount'],
        'quantity'         => 1,
        'payment_method'   => $validated['payment_method'],
        'payment_status'   => $paymentStatus,
        'receipt_status'   => $paymentStatus === 'verified' ? 'ready' : 'pending',
        'transaction_at'   => now(),
        'notes'            => $validated['notes'] ?? null,
    ]);

    return redirect()->route('cashier.transactions.products')
        ->with('status', 'Transaksi baru berhasil ditambahkan.');
})->name('transactions.store');
