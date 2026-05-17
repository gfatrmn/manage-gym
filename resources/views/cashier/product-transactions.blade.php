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
            transform: translateY(-1px);
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
            border: 1px dashed rgba(255,255,255,0.12);
            border-radius: 1rem;
            padding: 1rem;
            color: var(--text-muted);
            text-align: center;
        }

        .product-search-box {
            max-width: 320px;
        }
    </style>

    <div class="topbar-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="section-label">Produk</div>
                <h1 class="display-6 fw-bold mt-2 mb-0">Transaksi Produk</h1>
            </div>
            @if (request()->query('has_payment') == 1)
                <div class="d-flex gap-2">
                    <a href="{{ request()->query('gym_member_id') ? route('cashier.member-payments') : route('cashier.daily-payments') }}" class="btn btn-outline-light rounded-pill px-4">
                        Tidak Ada
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if (request()->query('has_payment') == 1)
        <div class="alert alert-info mb-4 rounded-3 border-0" role="alert">
            <div class="d-flex gap-2 align-items-start">
                <svg class="flex-shrink-0 mt-1" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <div>
                    <h5 class="mb-2">Apakah ada transaksi produk?</h5>
                    <p class="mb-0 small">Pembayaran telah tercatat. Apakah pelanggan membeli produk? Jika ya, pilih produk di bawah dan lanjutkan checkout.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="panel-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
            <div>
                <div class="section-label">Pelanggan</div>
                <h2 class="h5 fw-bold mb-1">{{ $selectedMember ? $selectedMember->full_name : ($selectedCustomerName ? $selectedCustomerName : 'Belum ada pelanggan') }}</h2>
            </div>
            <div class="text-md-end">
                @if ($selectedMember)
                    <div class="small text-muted mb-1">ID Member: {{ $selectedMember->id }}</div>
                    <span class="badge text-bg-success">Member</span>
                @elseif($selectedCustomerName)
                    <span class="badge text-bg-secondary">Non-member</span>
                @else
                    <span class="badge text-bg-warning">Belum dipilih</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-7">
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

        <div class="col-12 col-xl-5">
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
                                Non member: <strong>{{ $selectedCustomerName }}</strong>
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
                        <label class="form-label fw-semibold">Metode</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <label class="btn btn-outline-secondary rounded-pill mb-0">
                                <input type="radio" name="payment_method" value="cash" class="btn-check" autocomplete="off" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}>
                                Cash
                            </label>
                            <label class="btn btn-outline-secondary rounded-pill mb-0">
                                <input type="radio" name="payment_method" value="qris" class="btn-check" autocomplete="off" {{ old('payment_method') === 'qris' ? 'checked' : '' }}>
                                QRIS
                            </label>
                        </div>
                        @error('payment_method')
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
                        <button type="reset" class="btn btn-outline-secondary rounded-pill px-4" data-product-reset>Reset</button>
                        @if (request()->query('has_payment') == 1)
                            <a href="{{ request()->query('gym_member_id') ? route('cashier.member-payments') : route('cashier.daily-payments') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Kembali
                            </a>
                        @endif
                        <button type="submit" class="btn btn-dark rounded-pill px-4" @disabled($preselectedProducts->isEmpty())>Checkout</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
            <div>
                <h2 class="h4 fw-bold mb-1">Riwayat</h2>
            </div>
            <form method="GET" action="{{ route('cashier.transactions.products') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="month" name="month" class="form-control form-control-sm" value="{{ $selectedMonth }}">
                @if($selectedMember)
                    <input type="hidden" name="gym_member_id" value="{{ $selectedMember->id }}">
                @endif
                @if($selectedCustomerName)
                    <input type="hidden" name="customer_name" value="{{ $selectedCustomerName }}">
                @endif
                <button type="submit" class="btn btn-outline-dark rounded-pill btn-sm">Filter Bulan</button>
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
                        <th>Status</th>
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
                            <td>{{ in_array($item->payment_method, ['later', null], true) ? 'Bayar Nanti' : strtoupper($item->payment_method) }}</td>
                            <td>Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td><span class="badge text-bg-{{ $item->payment_status === 'verified' ? 'success' : 'warning' }}">{{ $item->payment_status === 'verified' ? 'Lunas' : 'Pending' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-secondary">Belum ada transaksi penjualan produk.</td>
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

                if (selectedProducts.size === 0) {
                    selectedList.innerHTML = '<div class="selected-product-empty"></div>';
                    selectedTotal.textContent = formatCurrency(0);
                    syncCatalogState();
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
                syncCatalogState();
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
                    document.getElementById('productCheckoutForm')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

            productSearchInput?.addEventListener('input', applyProductSearch);

            renderSelectedProducts();
            applyProductSearch();
        });
    </script>
@endsection
