@extends('admin.layout')

@section('content')
    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Pembayaran</div>
        <h1 class="display-6 fw-bold mt-2 mb-2">Halaman Pembayaran Kasir</h1>
        <p class="muted-copy mb-0">Satu halaman pembayaran lengkap untuk member, daily pass, dan checkout produk.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="panel-card p-4 h-100">
                <div class="section-label">Pembayaran Member</div>
                <h2 class="h4 fw-bold mt-2 mb-2">Halaman khusus pembayaran member</h2>
                <p class="muted-copy mb-4">Gunakan halaman ini untuk mencatat pembayaran member dan mengikuti transaksi langganan.</p>
                <a href="{{ route('cashier.member-payments') }}" class="btn btn-dark rounded-pill">Buka Pembayaran Member</a>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="panel-card p-4 h-100">
                <div class="section-label">Daily Non Member</div>
                <h2 class="h4 fw-bold mt-2 mb-2">Halaman khusus daily pass</h2>
                <p class="muted-copy mb-4">Gunakan halaman ini untuk mencatat pembayaran harian non-member secara terpisah.</p>
                <a href="{{ route('cashier.daily-payments') }}" class="btn btn-dark rounded-pill">Buka Daily Non Member</a>
            </div>
        </div>
    </div>

    <div class="panel-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Ringkasan cepat</h2>
            <span class="badge text-bg-light border text-dark">Pembayaran</span>
        </div>
        <div class="row g-4">
            <div class="col-12 col-md-6">
                <div class="hero-stat-card p-4 h-100">
                    <div class="small text-uppercase text-muted mb-2">Pembayaran Member</div>
                    <div class="fs-3 fw-bold">{{ number_format($memberPayments->count(), 0, ',', '.') }}</div>
                    <div class="small text-secondary">Transaksi member 24 jam terakhir</div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="hero-stat-card p-4 h-100">
                    <div class="small text-uppercase text-muted mb-2">Daily Non Member</div>
                    <div class="fs-3 fw-bold">{{ number_format($dailyPayments->count(), 0, ',', '.') }}</div>
                    <div class="small text-secondary">Transaksi daily pass 24 jam terakhir</div>
                </div>
            </div>
        </div>
    </div>

    @php
        $memberCheckins = collect($checkoutCheckins)
            ->filter(fn ($checkin) => $checkin->member?->member_status === 'member')
            ->values();
        $nonMemberCheckins = collect($checkoutCheckins)
            ->filter(fn ($checkin) => $checkin->member?->member_status !== 'member')
            ->values();
        $dailyPaymentCheckouts = collect($checkoutDailyPayments)->values();
    @endphp

    <div class="panel-card p-4 mb-4">
        <form method="GET" action="{{ route('cashier.transactions') }}" class="row gy-3 gx-3 align-items-center mb-4">
            <input type="hidden" name="section" value="checkout">
            <div class="col-auto flex-grow-1">
                <input type="text" name="q" class="form-control" value="{{ $transactionSearch }}" placeholder="Cari nama atau telepon check-in">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-dark rounded-pill">Cari</button>
            </div>
        </form>

        <div class="row g-4">
            <div class="col-12">
                <div class="hero-stat-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="h5 mb-1">Member</h3>
                            <p class="muted-copy mb-0">{{ $memberCheckins->count() }} nama member check-in hari ini.</p>
                        </div>
                        <span class="badge text-bg-light border text-dark">{{ $memberCheckins->count() }}</span>
                    </div>

                    @if ($memberCheckins->isEmpty())
                        <div class="text-muted">Tidak ada member yang sudah check-in hari ini.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>No. Telepon</th>
                                        <th>Waktu Check-in</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($memberCheckins as $checkin)
                                        <tr>
                                            <td>{{ $checkin->member->full_name }}</td>
                                            <td>{{ $checkin->member->phone }}</td>
                                            <td>{{ $checkin->checked_in_at->format('H:i, d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('cashier.transactions.products', ['gym_member_id' => $checkin->member->id]) }}" class="btn btn-outline-dark rounded-pill btn-sm">
                                                    Transaksi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12">
                <div class="hero-stat-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="h5 mb-1">Non Member</h3>
                            <p class="muted-copy mb-0">{{ $nonMemberCheckins->count() }} nama non-member check-in hari ini.</p>
                        </div>
                        <span class="badge text-bg-light border text-dark">{{ $nonMemberCheckins->count() }}</span>
                    </div>

                    <form method="GET" action="{{ route('cashier.transactions') }}" class="row gx-2 gy-2 align-items-center mb-3">
                        <input type="hidden" name="section" value="checkout">
                        <div class="col-auto flex-grow-1">
                            <input type="text" name="q" class="form-control form-control-sm" value="{{ $transactionSearch }}" placeholder="Cari non-member">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill">Cari</button>
                        </div>
                    </form>

                    @if ($nonMemberCheckins->isEmpty())
                        <div class="text-muted">Tidak ada non-member yang sudah check-in hari ini.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>No. Telepon</th>
                                        <th>Waktu Check-in</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nonMemberCheckins as $checkin)
                                        <tr>
                                            <td>{{ $checkin->member?->full_name ?? $checkin->submitted_name ?? 'Non Member' }}</td>
                                            <td>{{ $checkin->member?->phone ?? $checkin->submitted_phone ?? '-' }}</td>
                                            <td>{{ $checkin->checked_in_at->format('H:i, d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('cashier.transactions.products', ['customer_name' => $checkin->member?->full_name ?? $checkin->submitted_name ?? 'Non Member']) }}" class="btn btn-outline-dark rounded-pill btn-sm">
                                                    Transaksi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="panel-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="section-label">Pembayaran Daily Pass</div>
                <h3 class="h5 mb-1">Daily pass yang sudah dibayar hari ini</h3>
                <p class="muted-copy mb-0">Klik Transaksi untuk melanjutkan penjualan produk bagi pelanggan daily pass.</p>
            </div>
            <span class="badge text-bg-light border text-dark">{{ $dailyPaymentCheckouts->count() }}</span>
        </div>

        @if ($dailyPaymentCheckouts->isEmpty())
            <div class="text-muted">Tidak ada pembayaran daily pass hari ini.</div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jumlah</th>
                            <th>Metode</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dailyPaymentCheckouts as $payment)
                            <tr>
                                <td>{{ $payment->customer_name }}</td>
                                <td>Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td>{{ strtoupper($payment->payment_method) }}</td>
                                <td>{{ $payment->transaction_at->format('H:i, d M Y') }}</td>
                                <td>
                                    <a href="{{ route('cashier.transactions.products', ['customer_name' => $payment->customer_name]) }}" class="btn btn-outline-dark rounded-pill btn-sm">
                                        Transaksi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection