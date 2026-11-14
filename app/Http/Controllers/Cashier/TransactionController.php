<?php

namespace App\Http\Controllers\Cashier;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use App\Models\MemberHistory;
use App\Models\Membership;
use App\Models\MembershipPackage;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $typeAliases = [
            'member' => 'member_payment',
            'daily_pass' => 'daily_pass',
            'daily-pass' => 'daily_pass',
            'product' => 'product_sale',
            'checkout' => 'all',
        ];

        $typeFilter = (string) $request->query('type', $request->query('section', 'all'));
        $typeFilter = $typeAliases[$typeFilter] ?? $typeFilter;
        $typeFilter = in_array($typeFilter, ['all', 'member_payment', 'daily_pass', 'product_sale', 'other'], true) ? $typeFilter : 'all';

        $statusFilter = in_array($request->query('status'), ['all', 'verified', 'pending'], true) ? $request->query('status') : 'all';
        $methodFilter = in_array($request->query('method'), ['all', 'cash', 'qris'], true) ? $request->query('method') : 'all';
        $periodFilter = in_array($request->query('period'), ['today', 'month', 'all'], true) ? $request->query('period') : 'today';
        $search = trim((string) $request->query('q', ''));

        $query = CashierTransaction::query()->with(['member', 'product'])->latest('transaction_at');

        if ($typeFilter !== 'all') {
            $query->where('transaction_group', $typeFilter);
        }
        if ($statusFilter !== 'all') {
            $query->where('payment_status', $statusFilter);
        }
        if ($methodFilter !== 'all') {
            $query->where('payment_method', $methodFilter);
        }
        if ($periodFilter === 'today') {
            $query->whereDate('transaction_at', today());
        } elseif ($periodFilter === 'month') {
            $query->whereYear('transaction_at', now()->year)->whereMonth('transaction_at', now()->month);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('invoice', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('transaction_type', 'like', '%' . $search . '%')
                    ->orWhere('transaction_group', 'like', '%' . $search . '%')
                    ->orWhere('payment_method', 'like', '%' . $search . '%');
            });
        }

        $filteredCollection = $query->get();
        $paymentHistory = $filteredCollection->map(fn (CashierTransaction $t) => [
            'id' => $t->id,
            'invoice' => $t->invoice,
            'customer_name' => $t->customer_name ?? $t->member?->full_name ?? '-',
            'transaction_group' => $t->transaction_group,
            'transaction_type' => $t->transaction_type,
            'quantity' => $t->quantity ?? 1,
            'amount' => $t->amount,
            'amount_formatted' => RouteHelpers::formatCurrency($t->amount),
            'paid_amount_formatted' => RouteHelpers::formatCurrency($t->paid_amount),
            'change_amount_formatted' => RouteHelpers::formatCurrency($t->change_amount),
            'payment_method' => strtoupper((string) $t->payment_method),
            'payment_status' => $t->payment_status,
            'receipt_status' => $t->receipt_status,
            'transaction_at' => $t->transaction_at?->translatedFormat('d M Y, H:i'),
            'notes' => $t->notes,
        ])->values();

        $todayQuery = CashierTransaction::query()->whereDate('transaction_at', today());

        return Inertia::render('Cashier/Transactions', RouteHelpers::buildCashierViewData([
            'pageTitle' => 'Transaksi - Kasir Arena Gym',
            'activePage' => 'cashier.transactions',
            'paymentHistory' => $paymentHistory,
            'paymentFilters' => [
                'type' => $typeFilter,
                'status' => $statusFilter,
                'method' => $methodFilter,
                'period' => $periodFilter,
                'q' => $search,
            ],
            'paymentSummary' => [
                'shown_count' => $filteredCollection->count(),
                'shown_total' => RouteHelpers::formatCurrency((int) $filteredCollection->where('payment_status', 'verified')->sum('amount')),
                'today_count' => (clone $todayQuery)->count(),
                'today_total' => RouteHelpers::formatCurrency((int) (clone $todayQuery)->where('payment_status', 'verified')->sum('amount')),
                'pending_count' => CashierTransaction::query()->where('payment_status', '!=', 'verified')->count(),
                'all_count' => CashierTransaction::query()->count(),
            ],
        ]));
    }

    public function products(Request $request): Response
    {
        $selectedMonth = (string) $request->query('month', Carbon::now()->format('Y-m'));

        try {
            $monthStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        } catch (\Exception $e) {
            $monthStart = Carbon::now()->startOfMonth();
            $selectedMonth = $monthStart->format('Y-m');
        }

        $monthEnd = (clone $monthStart)->endOfMonth();

        $selectedMember = null;
        $selectedCustomerName = trim((string) $request->query('customer_name', ''));

        if ($request->query('gym_member_id')) {
            $selectedMember = GymMember::query()->find($request->query('gym_member_id'));
            $selectedCustomerName = $selectedCustomerName ?: ($selectedMember?->full_name ?? '');
        }

        $products = Product::query()
            ->orderByDesc('is_active')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'category' => $p->category,
                'brand' => $p->brand,
                'sku' => $p->sku,
                'price' => $p->price,
                'price_formatted' => RouteHelpers::formatCurrency($p->price),
                'stock' => $p->stock,
                'unit' => $p->unit,
                'is_active' => $p->is_active,
            ]);

        $productTransactions = CashierTransaction::query()
            ->with(['member', 'product'])
            ->whereNotNull('product_id')
            ->whereBetween('transaction_at', [$monthStart, $monthEnd])
            ->latest('transaction_at')
            ->get()
            ->map(fn (CashierTransaction $t) => [
                'id' => $t->id,
                'invoice' => $t->invoice,
                'customer_name' => $t->customer_name ?? $t->member?->full_name ?? '-',
                'product_name' => $t->product?->name ?? $t->transaction_type,
                'quantity' => $t->quantity ?? 1,
                'amount_formatted' => RouteHelpers::formatCurrency($t->amount),
                'payment_method' => strtoupper((string) $t->payment_method),
                'payment_status' => $t->payment_status,
                'transaction_at' => $t->transaction_at?->translatedFormat('d M Y, H:i'),
            ]);

        return Inertia::render('Cashier/ProductTransactions', RouteHelpers::buildCashierViewData([
            'pageTitle' => 'Penjualan Produk - Kasir Arena Gym',
            'activePage' => 'cashier.product-transactions',
            'products' => $products,
            'productTransactions' => $productTransactions,
            'members' => GymMember::query()->where('status', 'member')->orderBy('full_name')->get(['id', 'full_name', 'checkin_code']),
            'selectedMember' => $selectedMember,
            'selectedCustomerName' => $selectedCustomerName,
            'selectedMonth' => $selectedMonth,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $transactionGroup = $request->input('transaction_group', 'other');

        if ($transactionGroup === 'product_sale') {
            $validated = $request->validate([
                'gym_member_id' => ['nullable', 'exists:gym_members,id'],
                'customer_name' => ['required_without:gym_member_id', 'nullable', 'string', 'max:255'],
                'payment_method' => ['required', 'in:cash,qris'],
                'paid_amount' => ['nullable', 'integer', 'min:0'],
                'product_ids' => ['required', 'array', 'min:1'],
                'product_ids.*' => ['required', 'exists:products,id'],
                'quantities' => ['required', 'array'],
                'notes' => ['nullable', 'string'],
            ]);

            $paymentMethod = $validated['payment_method'];
            $paymentStatus = $paymentMethod === 'cash' ? 'verified' : 'pending';
            $receiptStatus = $paymentStatus === 'verified' ? 'ready' : 'pending';

            $productIds = collect($validated['product_ids'])->map(fn ($id) => (int) $id)->unique()->values();

            $customerName = $validated['customer_name'] ?? null;
            if (empty($customerName) && ! empty($validated['gym_member_id'])) {
                $customerName = GymMember::query()->find($validated['gym_member_id'])?->full_name ?? '';
            }

            try {
                DB::transaction(function () use ($productIds, $validated, $paymentMethod, $paymentStatus, $receiptStatus, $customerName) {
                    $products = Product::query()
                        ->whereIn('id', $productIds)
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    foreach ($productIds as $productId) {
                        $product = $products->get($productId);
                        if (! $product || ! $product->is_active) {
                            throw new \DomainException('product_ids|Ada produk yang sudah nonaktif dan tidak bisa dijual.');
                        }
                        $quantity = max((int) data_get($validated, 'quantities.' . $product->id, 1), 1);
                        if ($product->stock < $quantity) {
                            throw new \DomainException("product_ids|Stok {$product->name} tidak mencukupi untuk jumlah yang dipilih.");
                        }
                    }

                    $totalAmount = $productIds->sum(function (int $productId) use ($products, $validated) {
                        $product = $products->get($productId);
                        $quantity = max((int) data_get($validated, 'quantities.' . $product->id, 1), 1);

                        return $product->price * $quantity;
                    });

                    $paidAmount = $paymentMethod === 'qris'
                        ? (int) $totalAmount
                        : (int) ($validated['paid_amount'] ?? 0);

                    if ($paymentMethod === 'cash' && $paidAmount < $totalAmount) {
                        throw new \DomainException('paid_amount|Uang diterima tidak boleh kurang dari total checkout.');
                    }

                    $remainingPaidAmount = $paidAmount;
                    $lastProductId = $productIds->last();

                    foreach ($productIds as $productId) {
                        $product = $products->get($productId);
                        $quantity = max((int) data_get($validated, 'quantities.' . $product->id, 1), 1);
                        $lineAmount = $product->price * $quantity;
                        $linePaidAmount = $paymentMethod === 'qris'
                            ? $lineAmount
                            : min($remainingPaidAmount, $lineAmount);
                        $remainingPaidAmount = max($remainingPaidAmount - $lineAmount, 0);
                        $lineChangeAmount = $paymentMethod === 'cash' && $remainingPaidAmount > 0 && $productId === $lastProductId
                            ? $remainingPaidAmount
                            : 0;

                        $transaction = CashierTransaction::query()->create([
                            'invoice' => RouteHelpers::generateInvoice('PRD'),
                            'gym_member_id' => $validated['gym_member_id'] ?? null,
                            'product_id' => $product->id,
                            'customer_name' => $customerName,
                            'transaction_group' => 'product_sale',
                            'transaction_type' => $product->name,
                            'amount' => $lineAmount,
                            'paid_amount' => $linePaidAmount + $lineChangeAmount,
                            'change_amount' => $lineChangeAmount,
                            'quantity' => $quantity,
                            'payment_method' => $paymentMethod,
                            'payment_status' => $paymentStatus,
                            'receipt_status' => $receiptStatus,
                            'transaction_at' => now(),
                            'notes' => $validated['notes'] ?? null,
                        ]);

                        if (! empty($validated['gym_member_id'])) {
                            MemberHistory::query()->create([
                                'gym_member_id' => $validated['gym_member_id'],
                                'product_id' => $product->id,
                                'history_type' => 'product_purchase',
                                'occurred_at' => $transaction->transaction_at,
                                'title' => $product->name,
                                'description' => 'Pembelian barang',
                                'quantity' => $quantity,
                                'amount' => $transaction->amount,
                                'source_type' => CashierTransaction::class,
                                'source_id' => $transaction->id,
                            ]);
                        }

                        $product->decrement('stock', $quantity);
                    }
                });
            } catch (\DomainException $e) {
                [$field, $message] = explode('|', $e->getMessage(), 2);

                return redirect()->route('cashier.transactions.products')
                    ->withErrors([$field => $message])
                    ->withInput();
            }

            if ($paymentMethod === 'cash') {
                return redirect()->route('cashier.transactions.products')
                    ->with('status', 'Pembayaran produk tunai berhasil dicatat. Struk bisa dicetak dari daftar transaksi produk.');
            }

            return redirect()->route('cashier.receipts')
                ->with('status', 'Checkout produk QRIS berhasil dicatat. Verifikasi pembayaran sebelum mencetak struk.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'transaction_type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'paid_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris'],
            'notes' => ['nullable', 'string'],
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

        $transaction = CashierTransaction::query()->create([
            'invoice' => RouteHelpers::generateInvoice('TRX'),
            'gym_member_id' => null,
            'product_id' => null,
            'customer_name' => $validated['customer_name'],
            'transaction_group' => 'other',
            'transaction_type' => $validated['transaction_type'],
            'amount' => $validated['amount'],
            'paid_amount' => $paidAmount,
            'change_amount' => max($paidAmount - (int) $validated['amount'], 0),
            'quantity' => 1,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $paymentStatus,
            'receipt_status' => $paymentStatus === 'verified' ? 'ready' : 'pending',
            'transaction_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($paymentStatus === 'verified') {
            return redirect()->route('cashier.transactions')
                ->with('status', "Transaksi tunai {$transaction->invoice} berhasil dicatat. Struk bisa dicetak dari bukti pembayaran.");
        }

        return redirect()->route('cashier.receipts')
            ->with('status', 'Transaksi QRIS berhasil dicatat. Verifikasi pembayaran sebelum mencetak struk.');
    }

    public function showRegisterMember(): Response
    {
        $registerMemberHistory = CashierTransaction::query()
            ->where('transaction_group', 'member_payment')
            ->where('transaction_type', 'like', '%Aktivasi Member%')
            ->latest('transaction_at')
            ->limit(10)
            ->get()
            ->map(fn (CashierTransaction $t) => [
                'id' => $t->id,
                'invoice' => $t->invoice,
                'customer_name' => $t->customer_name,
                'transaction_type' => $t->transaction_type,
                'amount_formatted' => RouteHelpers::formatCurrency($t->amount),
                'payment_method' => strtoupper((string) $t->payment_method),
                'payment_status' => $t->payment_status,
                'transaction_at' => $t->transaction_at?->translatedFormat('d M Y, H:i'),
            ]);

        $packages = MembershipPackage::query()
            ->where('is_active', true)
            ->orderBy('duration_days')
            ->get();

        return Inertia::render('Cashier/RegisterMember', RouteHelpers::buildCashierViewData([
            'pageTitle' => 'Daftarkan Member - Kasir Arena Gym',
            'activePage' => 'cashier.register-member',
            'registerMemberHistory' => $registerMemberHistory,
            'packages' => $packages,
        ]));
    }

    public function registerMember(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name'      => ['required', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email', 'unique:gym_members,email'],
            'username'       => ['required', 'string', 'max:255', 'unique:users,login'],
            'password'       => ['required', 'string', 'min:8'],
            'payment_method' => ['required', 'in:cash,qris'],
            'duration'       => ['nullable', 'integer', 'in:1,3,6,12'],
            'package_id'     => ['nullable', 'exists:membership_packages,id'],
            'gender'         => ['nullable', 'in:male,female'],
            'birth_date'     => ['nullable', 'date'],
            'address'        => ['nullable', 'string', 'max:500'],
            'discount'       => ['nullable', 'integer', 'min:0'],
            'payment_amount' => ['nullable', 'integer', 'min:1'],
            'paid_amount'    => ['nullable', 'integer', 'min:0'],
            'notes'          => ['nullable', 'string'],
        ]);

        $package = null;
        if (! empty($validated['package_id'])) {
            $package = MembershipPackage::find($validated['package_id']);
        }

        $durationMonths = (int) ($validated['duration'] ?? 1);
        $durationDays = $package ? $package->duration_days : ($durationMonths * 30);
        $planName = $package ? $package->name : "Membership {$durationMonths} Bulan";

        $defaultPrice = $package ? $package->price : ($durationMonths * 90000);
        $discount = (int) ($validated['discount'] ?? 0);
        $amount = (int) ($validated['payment_amount'] ?? max(0, $defaultPrice - $discount));

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
        $cashierId = session('auth.user_id') ?? session('auth.id') ?? auth()->id();

        DB::transaction(function () use ($validated, $durationDays, $planName, $package, $amount, $paidAmount, $changeAmount, $discount, $paymentStatus, $cashierId) {
            $memberUser = User::query()->create([
                'name'      => $validated['full_name'],
                'login'     => $validated['username'],
                'email'     => $validated['email'],
                'role'      => 'member',
                'password'  => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            $joinedAt = now()->startOfDay();
            $dates = RouteHelpers::calculateMembershipDates(null, $durationDays);

            $memberPayload = [
                'user_id'         => $memberUser->id,
                'full_name'       => $validated['full_name'],
                'email'           => $validated['email'],
                'phone'           => $validated['phone'] ?? null,
                'gender'          => $validated['gender'] ?? 'male',
                'birth_date'      => $validated['birth_date'] ?? null,
                'address'         => $validated['address'] ?? null,
                'member_status'   => 'member',
                'membership_plan' => $planName,
                'package_status'  => 'active',
                'payment_amount'  => $amount,
                'can_check_in'    => true,
                'joined_at'       => $joinedAt,
                'expires_at'      => $dates['end_date'],
                'payment_method'  => $validated['payment_method'],
                'status'          => 'member',
                'checkin_code'    => RouteHelpers::generateMemberCheckinCode(),
                'notes'           => $validated['notes'] ?? null,
            ];

            $memberColumns = Schema::getColumnListing('gym_members');
            $memberPayload = collect($memberPayload)
                ->filter(fn ($value, $key) => in_array($key, $memberColumns, true))
                ->all();

            $member = GymMember::query()->create($memberPayload);

            $trxPayload = [
                'invoice'           => RouteHelpers::generateInvoice('MBR'),
                'gym_member_id'     => $member->id,
                'cashier_id'        => $cashierId,
                'package_id'        => $package?->id,
                'product_id'        => null,
                'customer_name'     => $member->full_name,
                'transaction_group' => 'member_payment',
                'transaction_type'  => "Aktivasi {$planName}",
                'amount'            => $amount,
                'discount'          => $discount,
                'paid_amount'       => $paidAmount,
                'change_amount'     => $changeAmount,
                'quantity'          => 1,
                'payment_method'    => $validated['payment_method'],
                'payment_status'    => $paymentStatus,
                'receipt_status'    => $paymentStatus === 'verified' ? 'ready' : 'pending',
                'transaction_at'    => now(),
                'notes'             => $validated['notes'] ?? null,
            ];

            $trxColumns = Schema::getColumnListing('cashier_transactions');
            $trxPayload = collect($trxPayload)
                ->filter(fn ($value, $key) => in_array($key, $trxColumns, true))
                ->all();

            $transaction = CashierTransaction::query()->create($trxPayload);

            // Record Membership Lifecycle
            if (Schema::hasTable('memberships')) {
                Membership::create([
                    'member_id'      => $member->id,
                    'package_id'     => $package?->id,
                    'transaction_id' => $transaction->id,
                    'start_date'     => $dates['start_date'],
                    'end_date'       => $dates['end_date'],
                    'price'          => $amount,
                    'status'         => 'active',
                ]);
            }

            ActivityLog::record(
                action: 'register_member',
                userId: $cashierId,
                entityType: GymMember::class,
                entityId: $member->id,
                description: "Kasir mendaftarkan member baru: {$member->full_name} ({$member->checkin_code}) dengan paket {$planName}"
            );
        });

        $message = $paymentStatus === 'verified'
            ? 'Pendaftaran member berhasil dan transaksi sudah tercatat.'
            : 'Pendaftaran member berhasil. Menunggu verifikasi pembayaran QRIS.';

        return redirect()->route('cashier.transactions.register-member.form')
            ->with('status', $message . ' Username dan password awal sudah siap diberikan ke member.')
            ->with('member_credentials', [
                'name'     => $validated['full_name'],
                'username' => $validated['username'],
                'email'    => $validated['email'],
                'password' => $validated['password'],
            ]);
    }
}

