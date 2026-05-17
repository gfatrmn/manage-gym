@extends('admin.layout')

@section('content')
    <style>
        .cashier-dashboard-head {
            min-height: 0;
        }

        .cashier-quick-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .8rem;
        }

        .cashier-quick-action {
            min-height: 70px;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .9rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.04);
            color: #fff;
            text-decoration: none;
            font-weight: 800;
        }

        .cashier-quick-action:hover {
            color: #fff;
            border-color: rgba(255,59,59,.38);
            background: rgba(255,59,59,.12);
        }

        .cashier-quick-action.primary {
            background: linear-gradient(135deg, #ff3b3b, #b80f24);
            border-color: rgba(255,59,59,.48);
            box-shadow: 0 16px 34px rgba(255,59,59,.2);
        }

        .cashier-quick-icon {
            width: 2.35rem;
            height: 2.35rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            flex: 0 0 auto;
        }

        .cashier-shift-card {
            height: 100%;
            padding: 1.25rem;
        }

        .cashier-shift-time {
            font-size: clamp(1.7rem, 3vw, 2.35rem);
            line-height: 1.05;
        }

        .cashier-stat-card {
            padding: 1.25rem;
        }

        .cashier-stat-label {
            color: rgba(255,255,255,.72);
        }

        .cashier-stat-value {
            color: #fff;
        }

        .cashier-dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, .65fr);
            gap: 1rem;
            align-items: start;
        }

        .cashier-panel {
            border: 1px solid var(--border);
            border-radius: 1rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .cashier-panel-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,.025);
        }

        .cashier-panel-body {
            padding: 1rem 1.25rem 1.25rem;
        }

        .cashier-panel .table thead th {
            color: var(--text-muted);
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
            border-bottom-color: var(--border);
            background: rgba(255,255,255,.025);
        }

        .cashier-panel .table td {
            padding-top: .85rem;
            padding-bottom: .85rem;
            border-top-color: var(--border);
            vertical-align: middle;
        }

        .cashier-side-list {
            display: grid;
            gap: .75rem;
        }

        .cashier-side-item {
            padding: .9rem;
            border: 1px solid var(--border);
            border-radius: .9rem;
            background: rgba(255,255,255,.035);
        }

        .cashier-side-title {
            color: var(--text-main);
            font-weight: 800;
        }

        .cashier-empty {
            padding: 1.25rem;
            border: 1px dashed var(--border);
            border-radius: .9rem;
            color: var(--text-muted);
            text-align: center;
        }

        @media (max-width: 991.98px) {
            .cashier-quick-actions {
                grid-template-columns: 1fr;
            }

            .cashier-dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="topbar-card cashier-dashboard-head p-4 mb-3 mb-lg-4">
        <div class="row g-3 g-xl-4 align-items-stretch">
            <div class="col-12 col-xl-8">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <div class="section-label">Kasir</div>
                        <h1 class="display-6 fw-bold mt-2 mb-0">Dashboard</h1>
                    </div>
                    <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema">
                        <span class="theme-toggle-track" aria-hidden="true">
                            <span class="theme-toggle-thumb"></span>
                        </span>
                    </button>
                </div>

                <div class="cashier-quick-actions">
                    <a href="{{ route('cashier.member-payments') }}" class="cashier-quick-action primary">
                        <span class="cashier-quick-icon"><i class="fas fa-id-card"></i></span>
                        <span>Member</span>
                    </a>
                    <a href="{{ route('cashier.daily-payments') }}" class="cashier-quick-action">
                        <span class="cashier-quick-icon"><i class="fas fa-user-plus"></i></span>
                        <span>Daily Pass</span>
                    </a>
                    <a href="{{ route('cashier.transactions.products') }}" class="cashier-quick-action">
                        <span class="cashier-quick-icon"><i class="fas fa-basket-shopping"></i></span>
                        <span>Produk</span>
                    </a>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="hero-stat-card cashier-shift-card"
                    data-shift-start="{{ $cashierShift['start'] ?? '08:00' }}"
                    data-shift-end="{{ $cashierShift['end'] ?? '16:00' }}">
                    <div class="section-label">Shift Aktif</div>
                    <div class="cashier-shift-time fw-bold mt-3">{{ $cashierShift['label'] ?? '08:00 - 16:00' }}</div>
                    <div class="small text-white fw-semibold mt-2 js-shift-countdown">Menghitung waktu shift...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-lg-4 mb-3 mb-lg-4">
        @foreach ($cashierStats as $index => $stat)
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="metric-card cashier-stat-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div class="section-label cashier-stat-label">{{ $stat['label'] }}</div>
                        <span class="status-badge badge-soft-teal">{{ $stat['change'] }}</span>
                    </div>
                    <div class="fs-2 fw-bold cashier-stat-value">{{ $stat['value'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="cashier-dashboard-grid">
        <div class="cashier-panel">
            <div class="cashier-panel-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <div class="section-label">Riwayat Hari Ini</div>
                    <h2 class="h5 fw-bold mt-2 mb-0">Transaksi terbaru</h2>
                </div>
                <a href="{{ route('cashier.transactions') }}" class="btn btn-dark rounded-pill px-4 fw-semibold">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Waktu</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Tipe</th>
                            <th>Nominal</th>
                            <th class="pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions->take(6) as $item)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $item->transaction_at?->format('H:i') ?? '-' }}</td>
                                <td>{{ $item->invoice }}</td>
                                <td class="fw-semibold">{{ $item->customer_name ?? $item->member?->full_name ?? 'Tidak dikenal' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $item->transaction_group ?? $item->transaction_type ?? '-')) }}</td>
                                <td class="fw-semibold">Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                                <td class="pe-4">
                                    <span class="badge text-bg-{{ $item->payment_status === 'verified' ? 'success' : 'warning' }}">{{ $item->payment_status === 'verified' ? 'Lunas' : 'Pending' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4">
                                    <div class="cashier-empty">Belum ada transaksi hari ini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-grid gap-3">
            <div class="cashier-panel">
                <div class="cashier-panel-header d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <div class="section-label">Perlu Dicek</div>
                        <h2 class="h5 fw-bold mt-2 mb-0">Pembayaran</h2>
                    </div>
                    <a href="{{ route('cashier.transactions') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">Buka</a>
                </div>
                <div class="cashier-panel-body">
                    <div class="cashier-side-list">
                        @forelse ($receiptQueue->take(3) as $item)
                            <div class="cashier-side-item">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="cashier-side-title">{{ $item->invoice }}</div>
                                        <div class="small muted-copy mt-1">{{ $item->customer_name ?? $item->member?->full_name ?? 'Tidak dikenal' }} - {{ ucfirst(str_replace('_', ' ', $item->transaction_group ?? $item->transaction_type ?? '-')) }}</div>
                                    </div>
                                    <span class="badge text-bg-light border text-dark">{{ $item->receipt_status === 'verified' ? 'Bukti OK' : 'Cek Bukti' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="cashier-empty">Tidak ada pembayaran yang perlu dicek.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="cashier-panel">
                <div class="cashier-panel-header">
                    <div class="section-label">Metode Bayar</div>
                    <h2 class="h5 fw-bold mt-2 mb-0">Ringkasan</h2>
                </div>
                <div class="cashier-panel-body">
                    <div class="cashier-side-list">
                        @forelse ($paymentMethods as $item)
                            <div>
                                <div class="d-flex justify-content-between small mb-2">
                                    <span class="muted-copy">{{ $item['label'] }}</span>
                                    <span class="fw-semibold">{{ $item['value'] }}</span>
                                </div>
                                <div class="mini-progress">
                                    <div class="mini-progress-bar bg-{{ $item['color'] }}" style="width: {{ $item['progress'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="cashier-empty">Belum ada pembayaran lunas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const shiftCard = document.querySelector('[data-shift-start][data-shift-end]');

            if (!shiftCard) {
                return;
            }

            const startValue = shiftCard.dataset.shiftStart;
            const endValue = shiftCard.dataset.shiftEnd;
            const countdownEl = shiftCard.querySelector('.js-shift-countdown');

            const toDate = (timeValue) => {
                const [hours, minutes] = timeValue.split(':').map(Number);
                const now = new Date();
                return new Date(
                    now.getFullYear(),
                    now.getMonth(),
                    now.getDate(),
                    hours,
                    minutes,
                    0,
                    0
                );
            };

            const formatRemaining = (milliseconds) => {
                const totalSeconds = Math.max(Math.floor(milliseconds / 1000), 0);
                const totalMinutes = Math.floor(totalSeconds / 60);
                const hours = Math.floor(totalMinutes / 60);
                const minutes = totalMinutes % 60;
                const seconds = totalSeconds % 60;
                const secondLabel = `${String(seconds).padStart(2, '0')} detik`;

                if (hours <= 0) {
                    return `${minutes} menit ${secondLabel}`;
                }

                return `${hours} jam ${minutes} menit ${secondLabel}`;
            };

            const updateShiftCountdown = () => {
                const now = new Date();
                const startAt = toDate(startValue);
                const endAt = toDate(endValue);

                if (now < startAt) {
                    countdownEl.textContent = `Shift dimulai dalam ${formatRemaining(startAt - now)}`;
                    return;
                }

                if (now >= endAt) {
                    countdownEl.textContent = 'Shift hari ini sudah selesai';
                    return;
                }

                countdownEl.textContent = `Sisa waktu shift ${formatRemaining(endAt - now)}`;
            };

            updateShiftCountdown();
            window.setInterval(updateShiftCountdown, 1000);
        })();
    </script>
@endsection
