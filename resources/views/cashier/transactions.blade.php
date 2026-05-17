@extends('admin.layout')

@section('content')
    <style>
        .payment-action-bar {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
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

        @media (max-width: 991.98px) {
            .payment-action-bar {
                grid-template-columns: 1fr;
            }
        }

        .checkout-action-btn {
            min-width: 108px;
            border-color: rgba(255, 59, 59, .8);
            background: #ff3b3b;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(255, 59, 59, .22);
        }

        .checkout-action-btn:hover,
        .checkout-action-btn:focus {
            border-color: #ff5b5b;
            background: #ff5b5b;
            color: #fff;
        }

        .badge-cash {
            background: rgba(34, 197, 94, .15);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, .25);
        }

        .badge-qris {
            background: rgba(99, 102, 241, .15);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, .25);
        }
    </style>

    <div class="topbar-card p-4 mb-4 mt-4">
        <div class="section-label">Transaksi</div>
        <h1 class="display-6 fw-bold mt-2 mb-0">Transaksi</h1>
    </div>

    @php
        $memberCheckins = collect($checkoutCheckins)
            ->filter(fn ($checkin) => $checkin->member !== null)
            ->values();
    @endphp

    {{-- ── Member Check-in Hari Ini ── --}}
    <div class="panel-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="section-label">Member</div>
                <h3 class="h5 mb-1">Member Masuk Hari Ini</h3>
            </div>
            <span class="badge text-bg-light border text-dark">{{ $memberCheckins->count() }}</span>
        </div>

        <div class="mb-3">
            <label for="member-today-search" class="form-label fw-semibold">Cari member hari ini</label>
            <input id="member-today-search" type="text" class="form-control" placeholder="Cari nama, HP, paket, atau status">
        </div>

        @if ($memberCheckins->isEmpty())
            <div class="text-muted">Tidak ada member yang check-in hari ini.</div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0" id="member-checkin-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor HP</th>
                            <th>Paket</th>
                            <th>Expire</th>
                            <th>Check-in</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($memberCheckins as $checkin)
                            @php
                                $today     = \Illuminate\Support\Carbon::today();
                                $expiresAt = $checkin->member->expires_at;
                                if (! $expiresAt) {
                                    $statusBadge = 'secondary';
                                    $statusLabel = 'Unknown';
                                } elseif ($expiresAt->lt($today)) {
                                    $statusBadge = 'danger';
                                    $statusLabel = 'Expired';
                                } elseif ($expiresAt->betweenIncluded($today, $today->copy()->addDays(7))) {
                                    $statusBadge = 'warning';
                                    $statusLabel = 'Akan Expired';
                                } else {
                                    $statusBadge = 'success';
                                    $statusLabel = 'Aktif';
                                }
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $checkin->member->full_name }}</td>
                                <td>{{ $checkin->member->phone ?: '-' }}</td>
                                <td>{{ $checkin->member->membership_plan ?: 'Membership Bulanan' }}</td>
                                <td>
                                    <div class="d-flex gap-2 align-items-center">
                                        {{ $checkin->member->expires_at?->format('d M Y') ?: '-' }}
                                        <span class="badge text-bg-{{ $statusBadge }} text-nowrap">{{ $statusLabel }}</span>
                                    </div>
                                </td>
                                <td>{{ $checkin->checked_in_at->format('H:i') }}</td>
                                <td>
                                    <a href="{{ route('cashier.transactions.products', ['gym_member_id' => $checkin->member->id, 'has_payment' => 1]) }}"
                                       class="btn rounded-pill btn-sm checkout-action-btn">
                                        Pembayaran
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Non Member (Daily Guest) Hari Ini ── --}}
    <div class="panel-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="section-label">Daily Pass</div>
                <h3 class="h5 mb-1">Non Member Hari Ini</h3>
            </div>
            <span class="badge text-bg-light border text-dark">{{ $dailyGuestsToday->count() }}</span>
        </div>

        <div class="mb-3">
            <label for="daily-today-search" class="form-label fw-semibold">Cari non member hari ini</label>
            <input id="daily-today-search" type="text" class="form-control" placeholder="Cari nama, HP, atau metode pembayaran">
        </div>

        @if ($dailyGuestsToday->isEmpty())
            <div class="text-muted">Tidak ada non member hari ini.</div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0" id="daily-guest-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>No. HP</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dailyGuestsToday as $guest)
                            <tr>
                                <td class="fw-semibold">{{ $guest->full_name }}</td>
                                <td>{{ $guest->phone ?: '-' }}</td>
                                <td>
                                    @if ($guest->payment_amount)
                                        Rp{{ number_format($guest->payment_amount, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php $method = strtolower($guest->payment_method ?? ''); @endphp
                                    <span class="badge rounded-pill fw-semibold {{ $method === 'qris' ? 'badge-qris' : 'badge-cash' }}">
                                        {{ strtoupper($guest->payment_method ?? '-') }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($guest->visit_at)->format('H:i') }}</td>
                                <td>
                                    <a href="{{ route('cashier.transactions.products', ['customer_name' => $guest->full_name]) }}"
                                       class="btn rounded-pill btn-sm checkout-action-btn">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const normalize = (value) => value.toLowerCase();

            // Member search
            const memberSearch = document.getElementById('member-today-search');
            const memberRows   = document.querySelectorAll('#member-checkin-table tbody tr');
            if (memberSearch) {
                memberSearch.addEventListener('input', function () {
                    const query = normalize(this.value.trim());
                    memberRows.forEach((row) => {
                        const text = normalize(row.textContent || '');
                        row.style.display = query === '' || text.includes(query) ? '' : 'none';
                    });
                });
            }

            // Daily guest search
            const dailySearch = document.getElementById('daily-today-search');
            const dailyRows   = document.querySelectorAll('#daily-guest-table tbody tr');
            if (dailySearch) {
                dailySearch.addEventListener('input', function () {
                    const query = normalize(this.value.trim());
                    dailyRows.forEach((row) => {
                        const text = normalize(row.textContent || '');
                        row.style.display = query === '' || text.includes(query) ? '' : 'none';
                    });
                });
            }
        });
    </script>
@endsection
