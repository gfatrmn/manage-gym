@extends('admin.layout')

@section('content')
    <style>
        .cashier-hero-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
            margin-top: 1.5rem;
        }

        .cashier-hero-actions .theme-toggle {
            flex: 0 0 auto;
        }
    </style>

    <div class="topbar-card hero-shell p-3 p-lg-4 p-xxl-5 mb-3 mb-lg-4">
        <div class="row g-3 g-xl-4 align-items-center">
            <div class="col-12 col-lg-7">
                <div class="section-label">Cashier Summary</div>
                <h1 class="hero-title fw-bold mt-3 mb-3">Dashboard Kasir Arena Gym</h1>
                <p class="hero-copy muted-copy mb-0">
                    Akses cepat untuk transaksi member, daily pass, verifikasi pembayaran, dan cetak struk langsung dari front desk.
                </p>

                <div class="cashier-hero-actions">
                    <a href="{{ route('cashier.member-payments') }}" class="hero-pill">Pembayaran Member</a>
                    <a href="{{ route('cashier.daily-payments') }}" class="hero-pill">Daily Pass</a>
                    <a href="{{ route('cashier.transactions') }}" class="hero-pill">Transaksi Produk</a>
                    <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema">
                        <span class="theme-toggle-track" aria-hidden="true">
                            <span class="theme-toggle-thumb"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="hero-stat-card"
                    data-shift-start="{{ $cashierShift['start'] ?? '08:00' }}"
                    data-shift-end="{{ $cashierShift['end'] ?? '16:00' }}">
                    <div class="section-label">Shift Aktif</div>
                    <div class="hero-stat-value">{{ $cashierShift['label'] ?? '08:00 - 16:00' }}</div>
                    <div class="small text-white fw-semibold mt-2 js-shift-countdown">Menghitung waktu shift...</div>
                    <div class="small muted-copy mt-1 js-shift-note">Kasir utama bertugas di front counter Arena Gym.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-lg-4 mb-3 mb-lg-4">
        @foreach ($cashierStats as $index => $stat)
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="metric-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div class="metric-icon {{ $index === 0 ? 'icon-teal' : ($index === 1 ? 'icon-orange' : ($index === 2 ? 'icon-navy' : 'icon-green')) }}">{{ $index + 1 }}</div>
                        <span class="status-badge badge-soft-teal">{{ $stat['change'] }}</span>
                    </div>
                    <div class="section-label mt-4">{{ $stat['label'] }}</div>
                    <div class="fs-2 fw-bold mt-2">{{ $stat['value'] }}</div>
                </div>
            </div>
        @endforeach
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
            const noteEl = shiftCard.querySelector('.js-shift-note');

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
                    noteEl.textContent = 'Sistem sedang menunggu jam buka shift kasir.';
                    return;
                }

                if (now >= endAt) {
                    countdownEl.textContent = 'Shift hari ini sudah selesai';
                    noteEl.textContent = 'Waktu operasional kasir hari ini sudah melewati jam tutup.';
                    return;
                }

                countdownEl.textContent = `Sisa waktu shift ${formatRemaining(endAt - now)}`;
                noteEl.textContent = 'Hitungan mundur berjalan dari jam buka sampai jam tutup shift kasir.';
            };

            updateShiftCountdown();
            window.setInterval(updateShiftCountdown, 1000);
        })();
    </script>
@endsection
