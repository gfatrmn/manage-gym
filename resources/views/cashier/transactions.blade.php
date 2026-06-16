@extends('admin.layout')

@section('content')
    <style>
        .payment-dashboard {
            display: grid;
            gap: 1rem;
        }

        .payment-action-bar {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .9rem;
        }

        .payment-action-btn {
            min-height: 86px;
            padding: 1rem 1.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: .85rem;
            font-weight: 700;
            text-align: left;
            border-radius: 1.1rem !important;
        }

        .payment-action-icon {
            width: 2.75rem;
            height: 2.75rem;
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            font-size: 1.1rem;
        }

        .payment-action-copy {
            min-width: 0;
            display: grid;
            gap: .1rem;
        }

        .payment-action-title {
            line-height: 1.15;
        }

        .payment-action-note {
            font-size: .82rem;
            font-weight: 600;
            opacity: .78;
        }

        .payment-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .85rem;
        }


        .payment-summary-card {
            min-height: 108px;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 1rem;
            background: rgba(255, 255, 255, .035);
        }

        .payment-summary-card span {
            display: block;
            color: rgba(255, 255, 255, .55);
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .payment-summary-card strong {
            display: block;
            margin-top: .65rem;
            color: #fff;
            font-size: clamp(1.35rem, 2.4vw, 1.8rem);
            line-height: 1.1;
            overflow-wrap: anywhere;
        }

        .payment-filter-card {
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 1.1rem;
            background: rgba(255, 255, 255, .03);
        }

        .payment-filter-grid {
            display: grid;
            grid-template-columns: minmax(220px, 1.4fr) repeat(4, minmax(140px, .8fr)) auto;
            gap: .75rem;
            align-items: end;
        }

        .payment-table-wrap {
            overflow-x: auto;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 1rem;
        }

        .payment-history-table {
            width: 100%;
            min-width: 1080px;
            margin: 0;
        }

        .payment-history-table th {
            color: rgba(255, 255, 255, .68);
            background: rgba(255, 59, 59, .12);
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .payment-history-table td {
            vertical-align: middle;
        }

        .payment-type-pill {
            display: inline-flex;
            align-items: center;
            padding: .38rem .7rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .08);
            color: rgba(255, 255, 255, .86);
            font-size: .78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .payment-invoice {
            display: inline-flex;
            max-width: 12rem;
            overflow-wrap: anywhere;
            white-space: normal;
            line-height: 1.25;
        }

        @media (max-width: 1199.98px) {
            .payment-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .payment-action-bar,
            .payment-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .payment-filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $typeLabels = [
            'member_payment' => 'Member',
            'daily_pass' => 'Daily Pass',
            'product_sale' => 'Produk',
            'other' => 'Lainnya',
        ];

        $statusLabels = [
            'verified' => 'Lunas',
            'pending' => 'Pending',
        ];
    @endphp

    <div class="payment-dashboard">
        <div class="topbar-card p-4">
            <div class="section-label">Kasir</div>
            <h1 class="display-6 fw-bold mt-2 mb-1">Dashboard Transaksi</h1>
            <p class="muted-copy mb-0">Semua transaksi masuk ke riwayat ini, termasuk member, daily pass, produk, dan transaksi lainnya.</p>
        </div>

        <div class="panel-card p-4">
            <div class="payment-action-bar">
                <a href="{{ route('cashier.member-payments') }}" class="btn btn-primary rounded-pill payment-action-btn">
                    <span class="payment-action-icon"><i class="fas fa-id-card"></i></span>
                    <span class="payment-action-copy">
                        <span class="payment-action-title">Member / Aktivasi</span>
                        <span class="payment-action-note">Transaksi membership</span>
                    </span>
                </a>
                <a href="{{ route('cashier.daily-payments') }}" class="btn btn-outline-primary rounded-pill payment-action-btn">
                    <span class="payment-action-icon"><i class="fas fa-user-plus"></i></span>
                    <span class="payment-action-copy">
                        <span class="payment-action-title">Daily Pass</span>
                        <span class="payment-action-note">Tamu daily pass</span>
                    </span>
                </a>
                <a href="{{ route('cashier.transactions.products') }}" class="btn btn-outline-light rounded-pill payment-action-btn">
                    <span class="payment-action-icon"><i class="fas fa-basket-shopping"></i></span>
                    <span class="payment-action-copy">
                        <span class="payment-action-title">Pembelian Produk</span>
                        <span class="payment-action-note">Input pembelian baru</span>
                    </span>
                </a>
                <a href="{{ route('cashier.transactions.register-member.form') }}" class="btn btn-outline-warning rounded-pill payment-action-btn">
                    <span class="payment-action-icon"><i class="fas fa-user-check"></i></span>
                    <span class="payment-action-copy">
                        <span class="payment-action-title">Daftarkan Member</span>
                        <span class="payment-action-note">Aktivasi dari daily pass</span>
                    </span>
                </a>
            </div>
        </div>

        <div class="payment-summary-grid">
            <div class="payment-summary-card">
                <span>Total Ditampilkan</span>
                <strong>{{ number_format($paymentSummary['shown_count'], 0, ',', '.') }}</strong>
            </div>
            <div class="payment-summary-card">
                <span>Nominal Ditampilkan</span>
                <strong>Rp{{ number_format($paymentSummary['shown_total'], 0, ',', '.') }}</strong>
            </div>
            <div class="payment-summary-card">
                <span>Pendapatan Hari Ini</span>
                <strong>Rp{{ number_format($paymentSummary['today_total'], 0, ',', '.') }}</strong>
            </div>
            <div class="payment-summary-card">
                <span>Pending</span>
                <strong>{{ number_format($paymentSummary['pending_count'], 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="panel-card p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="section-label">Riwayat Transaksi</div>
                    <h2 class="h4 fw-bold mt-2 mb-0">Semua Transaksi</h2>
                </div>
                <span class="badge text-bg-light border text-dark">{{ number_format($paymentSummary['all_count'], 0, ',', '.') }} total</span>
            </div>

            <form method="GET" action="{{ route('cashier.transactions') }}" class="payment-filter-card p-3 mb-4">
                <div class="payment-filter-grid">
                    <div>
                        <label class="form-label fw-semibold" for="payment_search">Cari</label>
                        <input id="payment_search" name="q" type="search" class="form-control" value="{{ $paymentFilters['q'] }}" placeholder="Invoice, pelanggan, tipe, metode">
                    </div>
                    <div>
                        <label class="form-label fw-semibold" for="payment_type">Tipe</label>
                        <select id="payment_type" name="type" class="form-select">
                            <option value="all" @selected($paymentFilters['type'] === 'all')>Semua</option>
                            <option value="member_payment" @selected($paymentFilters['type'] === 'member_payment')>Member</option>
                            <option value="daily_pass" @selected($paymentFilters['type'] === 'daily_pass')>Daily Pass</option>
                            <option value="product_sale" @selected($paymentFilters['type'] === 'product_sale')>Produk</option>
                            <option value="other" @selected($paymentFilters['type'] === 'other')>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold" for="payment_status">Status</label>
                        <select id="payment_status" name="status" class="form-select">
                            <option value="all" @selected($paymentFilters['status'] === 'all')>Semua</option>
                            <option value="verified" @selected($paymentFilters['status'] === 'verified')>Lunas</option>
                            <option value="pending" @selected($paymentFilters['status'] === 'pending')>Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold" for="payment_method">Metode</label>
                        <select id="payment_method" name="method" class="form-select">
                            <option value="all" @selected($paymentFilters['method'] === 'all')>Semua</option>
                            <option value="cash" @selected($paymentFilters['method'] === 'cash')>Cash</option>
                            <option value="qris" @selected($paymentFilters['method'] === 'qris')>QRIS</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold" for="payment_period">Periode</label>
                        <select id="payment_period" name="period" class="form-select">
                            <option value="today" @selected($paymentFilters['period'] === 'today')>Hari Ini</option>
                            <option value="month" @selected($paymentFilters['period'] === 'month')>Bulan Ini</option>
                            <option value="all" @selected($paymentFilters['period'] === 'all')>Semua</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark rounded-pill px-4">Filter</button>
                        <a href="{{ route('cashier.transactions') }}" class="btn btn-outline-secondary rounded-pill px-4">Reset</a>
                    </div>
                </div>
            </form>

            <div class="payment-table-wrap">
                <table class="table align-middle payment-history-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Tipe</th>
                            <th>Detail</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-end">Diterima</th>
                            <th class="text-end">Kembali</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentHistory as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->transaction_at?->format('H:i') ?? '-' }}</div>
                                    <div class="small text-secondary">{{ $item->transaction_at?->format('d M Y') ?? '-' }}</div>
                                </td>
                                <td><span class="payment-invoice fw-semibold">{{ $item->invoice }}</span></td>
                                <td class="fw-semibold">{{ $item->customer_name ?: '-' }}</td>
                                <td><span class="payment-type-pill">{{ $typeLabels[$item->transaction_group] ?? ucfirst(str_replace('_', ' ', $item->transaction_group ?? 'Transaksi')) }}</span></td>
                                <td>{{ $item->transaction_type }}</td>
                                <td class="text-end fw-semibold">Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                                <td class="text-end">Rp{{ number_format($item->paid_amount ?? $item->amount, 0, ',', '.') }}</td>
                                <td class="text-end">Rp{{ number_format($item->change_amount ?? 0, 0, ',', '.') }}</td>
                                <td>{{ strtoupper($item->payment_method ?? '-') }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $item->payment_status === 'verified' ? 'success' : 'warning' }}">
                                        {{ $statusLabels[$item->payment_status] ?? ucfirst($item->payment_status ?? '-') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if ($item->payment_status === 'verified')
                                        <a href="{{ route('cashier.receipts.print', $item->invoice) }}" class="btn btn-sm btn-outline-light rounded-pill px-3" target="_blank">
                                            Cetak
                                        </a>
                                    @else
                                        <span class="badge text-bg-warning">Verifikasi</span>
                                    @endif
                                </td>
                            </tr>
                            @if (str_contains(strtolower((string) $item->transaction_type), 'aktivasi member'))
                                <tr>
                                    <td colspan="11" class="pt-0 border-0">
                                        <div class="small text-secondary ps-1">
                                            Registrasi member baru dari kasir
                                            @if ($item->payment_status === 'verified')
                                                • Bukti pembayaran bisa dicetak.
                                            @else
                                                • Cetak tersedia setelah verifikasi pembayaran.
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-secondary">Tidak ada transaksi sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
