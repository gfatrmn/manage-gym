@extends('admin.layout')

@section('content')
    @php
        $availableProducts = $products->filter(fn ($product) => $product->is_active && $product->stock > 0)->values();
        $preselectedProductIds = collect(old('product_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();
        $preselectedQuantities = collect(old('quantities', []))
            ->mapWithKeys(fn ($quantity, $id) => [(int) $id => max((int) $quantity, 1)]);
        $preselectedProducts = $products
            ->whereIn('id', $preselectedProductIds)
            ->map(function ($product) use ($preselectedQuantities) {
                $quantity = $preselectedQuantities->get($product->id, 1);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => ucfirst($product->category),
                    'brand' => $product->brand ?: 'Tanpa brand',
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'unit' => $product->unit,
                    'quantity' => $quantity,
                ];
            })
            ->values();
        $selectedTotal = $preselectedProducts->sum(fn ($product) => $product['price'] * $product['quantity']);
    @endphp

    <style>
        .product-catalog-table thead th {
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .product-catalog-table tbody tr {
            transition: background-color .2s ease, transform .2s ease;
        }

        .product-catalog-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .product-catalog-table tbody tr.is-selected {
            background: linear-gradient(90deg, rgba(255, 59, 59, 0.16), rgba(255, 255, 255, 0.03));
            box-shadow: inset 0 0 0 1px rgba(255, 59, 59, 0.2);
        }

        .product-name-cell {
            min-width: 220px;
        }

        .product-stock-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.7rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.9rem;
        }

        .product-check-button {
            width: 2.4rem;
            height: 2.4rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-weight: 800;
            transition: .2s ease;
        }

        .product-check-button:hover:not(:disabled),
        .product-check-button.is-selected {
            border-color: rgba(255, 59, 59, 0.32);
            background: linear-gradient(135deg, rgba(255, 59, 59, 0.9), rgba(166, 15, 31, 0.95));
            box-shadow: 0 12px 24px rgba(166, 15, 31, 0.28);
        }

        .product-check-button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .selected-product-list {
            display: grid;
            gap: .85rem;
        }

        .selected-product-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: .85rem;
            align-items: center;
            padding: .9rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
        }

        .selected-product-copy {
            min-width: 0;
        }

        .selected-product-copy .fw-semibold,
        .selected-product-copy .small {
            overflow-wrap: anywhere;
        }

        .selected-product-qty {
            width: 68px;
            text-align: center;
        }

        .selected-product-remove {
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.04);
            color: #fff;
        }

        .selected-product-step {
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.04);
            color: #fff;
            font-weight: 700;
        }

        .selected-product-empty {
            display: grid;
            gap: .35rem;
            border: 1px dashed rgba(255,59,59,0.28);
            border-radius: 1rem;
            padding: 1.1rem;
            color: var(--text-muted);
            text-align: center;
            background: rgba(255,59,59,.045);
        }

        .selected-product-empty strong {
            color: #fff;
            font-size: .95rem;
        }

        .product-search-box {
            max-width: 320px;
        }

        .checkout-helper-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .85rem;
        }

        .checkout-helper-card {
            min-height: 86px;
            padding: 1rem;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 1rem;
            background: rgba(255,255,255,.035);
        }

        .checkout-helper-card strong {
            display: block;
            color: #fff;
            margin-bottom: .25rem;
        }

        .checkout-product-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(360px, .65fr);
            gap: 1rem;
            align-items: start;
        }

        .checkout-method-group {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }

        .checkout-method-option {
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 1rem;
            color: var(--text-main);
            background: rgba(255,255,255,.045);
            cursor: pointer;
            font-weight: 800;
            transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
        }

        .btn-check:checked + .checkout-method-option {
            border-color: rgba(255,59,59,.48);
            background: linear-gradient(135deg, #ff3b3b, #b80f24);
            color: #fff;
            box-shadow: 0 16px 34px rgba(255,59,59,.2);
        }

        .product-checkout-button:disabled {
            opacity: .55;
            cursor: not-allowed;
            box-shadow: none;
        }

        .product-flow-note {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .9rem 1rem;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1rem;
            background: rgba(255,255,255,.035);
            color: var(--text-muted);
        }

        .product-flow-note i {
            width: 2.35rem;
            height: 2.35rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .85rem;
            color: #fff;
            background: linear-gradient(135deg, #ff3b3b, #b80f24);
            box-shadow: 0 14px 30px rgba(255,59,59,.18);
        }

        @media (max-width: 1199.98px) {
            .checkout-product-shell,
            .checkout-helper-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="topbar-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="section-label">Checkout</div>
                <h1 class="display-6 fw-bold mt-2 mb-0">Checkout Barang</h1>
            </div>
        </div>
    </div>

    <!-- <div class="panel-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
            <div>
                <div class="section-label">Pelanggan</div>
                <h2 class="h5 fw-bold mb-1">{{ $selectedMember ? $selectedMember->full_name : ($selectedCustomerName ? $selectedCustomerName : 'Belum ada pelanggan') }}</h2>
                <div class="small muted-copy">Checkout tetap bisa dibuat langsung dengan mengisi nama pembeli di form kanan.</div>
            </div>
            <div class="text-md-end">
                @if ($selectedMember)
                    <div class="small text-muted mb-1">ID Member: {{ $selectedMember->id }}</div>
                    <span class="badge text-bg-success">Member</span>
                @elseif($selectedCustomerName)
                    <span class="badge text-bg-secondary">Daily Pass</span>
                @else
                    <span class="badge text-bg-warning">Belum dipilih</span>
                @endif
            </div>
        </div>
        <div class="product-flow-note mt-3">
            <i class="fas fa-receipt"></i>
            <div>
                <div class="fw-semibold text-white">Alur checkout barang</div>
                <div class="small">Pilih produk, isi nama pembeli bila belum dari check-in, lalu pilih Cash untuk langsung cetak atau QRIS untuk masuk verifikasi.</div>
            </div>
        </div>
    </div>

    <div class="checkout-helper-grid mb-4">
        <div class="checkout-helper-card">
            <strong>1. Isi pelanggan</strong>
            <span class="small muted-copy">Pilih dari alur member/daily pass atau isi nama pembeli langsung.</span>
        </div>
        <div class="checkout-helper-card">
            <strong>2. Pilih barang</strong>
            <span class="small muted-copy">Klik tombol + pada produk, lalu atur jumlah barang.</span>
        </div>
        <div class="checkout-helper-card">
            <strong>3. Checkout</strong>
            <span class="small muted-copy">Cash langsung cetak struk, QRIS masuk verifikasi dulu.</span>
        </div>
    </div> -->

    <div class="checkout-product-shell mb-4">
        <div>
            <div class="panel-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
                    <div>
                        <h2 class="h4 fw-bold mb-1">Produk</h2>
                    </div>
                    <span class="badge text-bg-light border text-dark">{{ $products->count() }} produk</span>
                </div>

                <div class="d-flex justify-content-between align-items-center gap-3 mb-3 flex-wrap">
                    <div class="product-search-box w-100">
                        <label for="cashierProductSearch" class="form-label fw-semibold mb-2">Cari</label>
                        <input
                            type="search"
                            id="cashierProductSearch"
                            class="form-control"
                            placeholder="Nama, brand, SKU, kategori"
                            autocomplete="off"
                            data-product-search>
                    </div>
                    <div class="small muted-copy" data-product-search-summary>{{ $products->count() }} produk</div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 product-catalog-table">
                        <thead>
                            <tr>
                                <th>Pilih</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                @php
                                    $canCheckout = $product->is_active && $product->stock > 0;
                                    $isSelected = $preselectedProductIds->contains((int) $product->id);
                                @endphp
                                <tr
                                    data-product-row="{{ $product->id }}"
                                    data-product-search-text="{{ strtolower(implode(' ', array_filter([$product->name, $product->brand, $product->sku, $product->category, $product->unit]))) }}"
                                    @class(['is-selected' => $isSelected])>
                                    <td>
                                        <button
                                            type="button"
                                            class="product-check-button {{ $isSelected ? 'is-selected' : '' }}"
                                            title="{{ $canCheckout ? 'Pilih untuk checkout' : 'Produk tidak bisa dijual' }}"
                                            aria-label="{{ $canCheckout ? 'Pilih produk '.$product->name : 'Produk '.$product->name.' tidak bisa dijual' }}"
                                            data-product-select
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-product-category="{{ ucfirst($product->category) }}"
                                            data-product-price="{{ $product->price }}"
                                            data-product-stock="{{ $product->stock }}"
                                            data-product-unit="{{ $product->unit }}"
                                            data-product-brand="{{ $product->brand ?: 'Tanpa brand' }}"
                                            @disabled(! $canCheckout)>
                                            {{ $isSelected ? '✓' : '+' }}
                                        </button>
                                    </td>
                                    <td class="product-name-cell">
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <div class="small muted-copy">
                                            {{ $product->brand ?: 'Tanpa brand' }}
                                            @if ($product->sku)
                                                • SKU {{ $product->sku }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $product->category === 'vitamin' ? 'warning' : 'primary' }}">
                                            {{ ucfirst($product->category) }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="product-stock-chip">
                                            {{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($product->is_active)
                                            <span class="badge text-bg-{{ $product->stock > 0 ? 'success' : 'secondary' }}">
                                                {{ $product->stock > 0 ? 'Tersedia' : 'Stok Habis' }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">Belum ada produk dari admin yang bisa ditampilkan di kasir.</td>
                                </tr>
                            @endforelse
                            <tr class="d-none" data-product-empty-search>
                                <td colspan="6" class="text-center py-4 text-secondary">Produk tidak ditemukan. Coba kata kunci lain.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="panel-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h4 fw-bold mb-1">Checkout</h2>
                    </div>
                </div>

                <div class="list-card p-3 mb-3">
                    <div class="small muted-copy mb-2">Dipilih</div>
                    <div class="selected-product-list" data-selected-product-list>
                        @forelse ($preselectedProducts as $product)
                            <div class="selected-product-item">
                                <div class="selected-product-copy">
                                    <div class="fw-semibold">{{ $product['name'] }}</div>
                                    <div class="small muted-copy">{{ $product['category'] }} • {{ $product['brand'] }} • Rp{{ number_format($product['price'], 0, ',', '.') }} • Stok {{ $product['stock'] }} {{ $product['unit'] }}</div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" class="form-control selected-product-qty" value="{{ $product['quantity'] }}" min="1" max="{{ $product['stock'] }}" disabled>
                                    <button type="button" class="selected-product-remove" disabled>×</button>
                                </div>
                            </div>
                        @empty
                            <div class="selected-product-empty" data-selected-product-empty>
                                <strong>Belum ada barang dipilih</strong>
                                <span>Klik tombol + pada daftar produk untuk menambahkan barang ke checkout.</span>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        <div class="small muted-copy">Subtotal</div>
                        <div class="fs-4 fw-bold" data-selected-product-total>Rp{{ number_format($selectedTotal, 0, ',', '.') }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('cashier.transactions.store') }}" class="row g-3" id="productCheckoutForm">
                    @csrf
                    <input type="hidden" name="transaction_group" value="product_sale">
                    <div data-selected-product-inputs></div>
                    @error('product_ids')
                        <div class="col-12">
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        </div>
                    @enderror

                    @if ($selectedMember)
                        <div class="col-12">
                            <div class="alert alert-secondary p-3">
                                Member: <strong>{{ $selectedMember->full_name }}</strong>
                            </div>
                            <input type="hidden" name="gym_member_id" value="{{ $selectedMember->id }}">
                            <input type="hidden" name="customer_name" value="{{ $selectedCustomerName }}">
                        </div>
                    @elseif ($selectedCustomerName)
                        <div class="col-12">
                            <div class="alert alert-secondary p-3">
                                Daily pass: <strong>{{ $selectedCustomerName }}</strong>
                            </div>
                            <input type="hidden" name="customer_name" value="{{ $selectedCustomerName }}">
                        </div>
                    @else
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama</label>
                            <input name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="Nama pembeli" required>
                            @error('customer_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="col-12">
                        <label class="form-label fw-semibold">Metode Pembayaran</label>
                        <div class="checkout-method-group">
                            <input type="radio" name="payment_method" value="cash" class="btn-check" id="product_payment_cash" autocomplete="off" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}>
                            <label class="checkout-method-option" for="product_payment_cash">
                                <i class="fas fa-money-bill"></i> Tunai
                            </label>
                            <input type="radio" name="payment_method" value="qris" class="btn-check" id="product_payment_qris" autocomplete="off" {{ old('payment_method') === 'qris' ? 'checked' : '' }}>
                            <label class="checkout-method-option" for="product_payment_qris">
                                <i class="fas fa-qrcode"></i> QRIS
                            </label>
                        </div>
                        @error('payment_method')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Uang diterima</label>
                        <input name="paid_amount" type="number" min="0" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount') }}" placeholder="Uang dari pelanggan" required data-product-paid>
                        <div class="form-text text-white-50">Kembalian: <strong data-product-change>Rp0</strong></div>
                        @error('paid_amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Opsional">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary rounded-pill px-4" data-product-reset>Bersihkan</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4 product-checkout-button" data-product-submit @disabled($preselectedProducts->isEmpty())>Lanjutkan Checkout</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex flex-column justify-content-between gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">Riwayat Transaksi</h2>
            </div>
            <form method="GET" action="{{ route('cashier.transactions.products') }}" class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold small mb-2">Bulan</label>
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ $selectedMonth }}">
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold small mb-2">Status Pembayaran</label>
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="verified" {{ request('payment_status') === 'verified' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold small mb-2">Metode Pembayaran</label>
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">Semua Metode</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="later" {{ request('payment_method') === 'later' ? 'selected' : '' }}>Bayar Nanti</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold small mb-2">Urutkan</label>
                    <select name="sort_by" class="form-select form-select-sm">
                        <option value="terbaru" {{ request('sort_by', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlama" {{ request('sort_by') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                        <option value="nominal_tinggi" {{ request('sort_by') === 'nominal_tinggi' ? 'selected' : '' }}>Nominal Tinggi</option>
                        <option value="nominal_rendah" {{ request('sort_by') === 'nominal_rendah' ? 'selected' : '' }}>Nominal Rendah</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-auto d-flex gap-2">
                    @if($selectedMember)
                        <input type="hidden" name="gym_member_id" value="{{ $selectedMember->id }}">
                    @endif
                    @if($selectedCustomerName)
                        <input type="hidden" name="customer_name" value="{{ $selectedCustomerName }}">
                    @endif
                    <button type="submit" class="btn btn-dark rounded-pill btn-sm px-4 flex-grow-1 flex-lg-grow-0">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                    @if(request()->hasAny(['month', 'payment_status', 'payment_method', 'sort_by']))
                        <a href="{{ route('cashier.transactions.products', array_filter(['gym_member_id' => $selectedMember?->id, 'customer_name' => $selectedCustomerName])) }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Pembayaran</th>
                        <th>Nominal</th>
                        <th>Diterima</th>
                        <th>Kembali</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productTransactions as $item)
                        <tr>
                            <td>{{ $item->transaction_at->format('d M Y') }}</td>
                            <td class="fw-semibold">{{ $item->transaction_at->format('H:i') }}</td>
                            <td>{{ $item->invoice }}</td>
                            <td>{{ $item->customer_name }}</td>
                            <td>{{ $item->product?->name ?? $item->transaction_type }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                @if(in_array($item->payment_method, ['later', null], true))
                                    Bayar Nanti
                                @elseif($item->payment_method === 'cash')
                                    Tunai
                                @elseif($item->payment_method === 'qris')
                                    QRIS
                                @else
                                    {{ strtoupper($item->payment_method) }}
                                @endif
                            </td>
                            <td>Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->paid_amount ?? $item->amount, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->change_amount ?? 0, 0, ',', '.') }}</td>
                            <td>
                                @if($item->payment_status === 'verified')
                                    <span class="badge text-bg-success"><i class="fas fa-check-circle"></i> Lunas</span>
                                @else
                                    <span class="badge text-bg-warning"><i class="fas fa-hourglass-half"></i> Menunggu</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($item->payment_status === 'verified')
                                    <a href="{{ route('cashier.receipts.print', $item->invoice) }}" class="btn btn-sm btn-outline-light rounded-pill px-3" target="_blank">
                                        <i class="fas fa-print"></i> Cetak
                                    </a>
                                @else
                                    <span class="badge text-bg-warning">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-4 text-secondary">Belum ada transaksi penjualan produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectedList = document.querySelector('[data-selected-product-list]');
            const selectedTotal = document.querySelector('[data-selected-product-total]');
            const selectedInputs = document.querySelector('[data-selected-product-inputs]');
            const resetButton = document.querySelector('[data-product-reset]');
            const catalogButtons = document.querySelectorAll('[data-product-select]');
            const productRows = document.querySelectorAll('[data-product-row]');
            const productSearchInput = document.querySelector('[data-product-search]');
            const productSearchSummary = document.querySelector('[data-product-search-summary]');
            const emptySearchRow = document.querySelector('[data-product-empty-search]');
            const checkoutForm = document.getElementById('productCheckoutForm');
            const checkoutSubmit = document.querySelector('[data-product-submit]');
            const paidInput = document.querySelector('[data-product-paid]');
            const changeText = document.querySelector('[data-product-change]');
            const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
            let currentTotal = {{ (int) $selectedTotal }};

            if (!selectedList || !selectedTotal || !selectedInputs) {
                return;
            }

            const formatCurrency = (amount) => 'Rp' + new Intl.NumberFormat('id-ID').format(amount || 0);
            const initialSelections = @json($preselectedProducts);
            const selectedProducts = new Map(
                initialSelections.map((product) => [
                    String(product.id),
                    {
                        ...product,
                        quantity: Math.max(parseInt(product.quantity || 1, 10), 1),
                    },
                ])
            );

            const syncCatalogState = () => {
                productRows.forEach((row) => {
                    row.classList.toggle('is-selected', selectedProducts.has(row.dataset.productRow));
                });

                catalogButtons.forEach((button) => {
                    const isSelected = selectedProducts.has(button.dataset.productId);
                    button.classList.toggle('is-selected', isSelected);
                    button.textContent = isSelected ? '✓' : '+';
                });
            };

            const applyProductSearch = () => {
                const keyword = (productSearchInput?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                productRows.forEach((row) => {
                    const haystack = (row.dataset.productSearchText || '').toLowerCase();
                    const isVisible = keyword === '' || haystack.includes(keyword);
                    row.classList.toggle('d-none', !isVisible);

                    if (isVisible) {
                        visibleCount += 1;
                    }
                });

                if (productSearchSummary) {
                    productSearchSummary.textContent = `${visibleCount} / ${productRows.length}`;
                }

                emptySearchRow?.classList.toggle('d-none', visibleCount !== 0);
            };

            const renderSelectedProducts = () => {
                selectedInputs.innerHTML = '';
                if (checkoutSubmit) {
                    checkoutSubmit.disabled = selectedProducts.size === 0;
                    checkoutSubmit.textContent = selectedProducts.size === 0 ? 'Pilih barang dulu' : 'Lanjutkan Checkout';
                }

                if (selectedProducts.size === 0) {
                    currentTotal = 0;
                    selectedList.innerHTML = `
                        <div class="selected-product-empty">
                            <strong>Belum ada barang dipilih</strong>
                            <span>Klik tombol + pada daftar produk untuk menambahkan barang ke checkout.</span>
                        </div>
                    `;
                    selectedTotal.textContent = formatCurrency(0);
                    syncCatalogState();
                    syncPaymentAmount();
                    return;
                }

                let total = 0;

                selectedList.innerHTML = Array.from(selectedProducts.values()).map((product) => {
                    total += Number(product.price) * Number(product.quantity);
                    return `
                        <div class="selected-product-item">
                            <div class="selected-product-copy">
                                <div class="fw-semibold">${product.name}</div>
                                <div class="small muted-copy">${product.category} • ${product.brand} • ${formatCurrency(product.price)} • Stok ${product.stock} ${product.unit}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="selected-product-step" data-selected-step="decrease" data-selected-step-id="${product.id}">-</button>
                                <input type="number" class="form-control selected-product-qty" value="${product.quantity}" min="1" max="${product.stock}" data-selected-quantity="${product.id}">
                                <button type="button" class="selected-product-step" data-selected-step="increase" data-selected-step-id="${product.id}">+</button>
                                <button type="button" class="selected-product-remove" data-selected-remove="${product.id}">×</button>
                            </div>
                        </div>
                    `;
                }).join('');

                Array.from(selectedProducts.values()).forEach((product) => {
                    selectedInputs.insertAdjacentHTML('beforeend', `
                        <input type="hidden" name="product_ids[]" value="${product.id}">
                        <input type="hidden" name="quantities[${product.id}]" value="${product.quantity}">
                    `);
                });

                selectedTotal.textContent = formatCurrency(total);
                currentTotal = total;
                syncCatalogState();
                syncPaymentAmount();
            };

            const syncPaymentAmount = () => {
                if (!paidInput || !changeText) {
                    return;
                }

                const method = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
                if (method === 'qris') {
                    paidInput.value = currentTotal || '';
                    paidInput.readOnly = true;
                } else {
                    paidInput.readOnly = false;
                }

                changeText.textContent = formatCurrency((Number(paidInput.value) || 0) - currentTotal);
            };

            catalogButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const productId = String(this.dataset.productId || '');

                    if (!productId) {
                        return;
                    }

                    if (selectedProducts.has(productId)) {
                        selectedProducts.delete(productId);
                        renderSelectedProducts();
                        return;
                    }

                    selectedProducts.set(productId, {
                        id: productId,
                        name: this.dataset.productName || 'Produk',
                        category: this.dataset.productCategory || '-',
                        brand: this.dataset.productBrand || '-',
                        price: parseInt(this.dataset.productPrice || '0', 10),
                        stock: parseInt(this.dataset.productStock || '0', 10),
                        unit: this.dataset.productUnit || 'pcs',
                        quantity: 1,
                    });

                    renderSelectedProducts();
                    checkoutForm?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            selectedList.addEventListener('input', function (event) {
                const input = event.target.closest('[data-selected-quantity]');

                if (!input) {
                    return;
                }

                const productId = String(input.dataset.selectedQuantity || '');
                const selectedProduct = selectedProducts.get(productId);

                if (!selectedProduct) {
                    return;
                }

                const nextQuantity = Math.min(
                    Math.max(parseInt(input.value || '1', 10), 1),
                    Number(selectedProduct.stock) || 1
                );

                selectedProduct.quantity = nextQuantity;
                renderSelectedProducts();
            });

            selectedList.addEventListener('click', function (event) {
                const stepButton = event.target.closest('[data-selected-step]');
                if (stepButton) {
                    const productId = String(stepButton.dataset.selectedStepId || '');
                    const selectedProduct = selectedProducts.get(productId);

                    if (!selectedProduct) {
                        return;
                    }

                    const currentQuantity = Number(selectedProduct.quantity) || 1;
                    const maxStock = Number(selectedProduct.stock) || 1;
                    const nextQuantity = stepButton.dataset.selectedStep === 'increase'
                        ? Math.min(currentQuantity + 1, maxStock)
                        : Math.max(currentQuantity - 1, 1);

                    selectedProduct.quantity = nextQuantity;
                    renderSelectedProducts();
                    return;
                }

                const removeButton = event.target.closest('[data-selected-remove]');

                if (!removeButton) {
                    return;
                }

                const productId = String(removeButton.dataset.selectedRemove || '');
                selectedProducts.delete(productId);
                renderSelectedProducts();
            });

            resetButton?.addEventListener('click', function () {
                window.setTimeout(function () {
                    selectedProducts.clear();
                    renderSelectedProducts();
                }, 0);
            });

            checkoutForm?.addEventListener('submit', function (event) {
                if (selectedProducts.size > 0) {
                    return;
                }

                event.preventDefault();
                renderSelectedProducts();
                selectedList.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });

            productSearchInput?.addEventListener('input', applyProductSearch);
            paidInput?.addEventListener('input', syncPaymentAmount);
            paymentMethodInputs.forEach((input) => input.addEventListener('change', syncPaymentAmount));

            renderSelectedProducts();
            applyProductSearch();
        });
    </script>
@endsection
