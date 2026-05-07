@extends('admin.layout')

@section('content')
    <div class="topbar-card p-3 p-lg-4 mb-3 mb-lg-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-xl-8">
                <div class="section-label">Cashier Summary</div>
                <h1 class="display-6 fw-bold mt-2 mb-2">Dashboard kasir Arena Gym</h1>
                <p class="muted-copy mb-0">Kelola pembayaran member, pembayaran daily non member, data transaksi, verifikasi status pembayaran, dan pencetakan bukti pembayaran dalam area kasir yang terpisah dari admin.</p>
            </div>
            <div class="col-12 col-xl-4">
                <div class="dark-panel rounded-4 p-4">
                    <div class="section-label text-white-50">Shift Aktif</div>
                    <div class="h3 fw-bold mt-2 mb-1">{{ $cashierShift['label'] ?? '08:00 - 16:00' }}</div>
                    <div class="small text-white fw-semibold mb-1 js-shift-countdown">Menghitung waktu shift...</div>
                    <div class="small muted-copy js-shift-note">Kasir utama bertugas di front counter Arena Gym.</div>
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

    <div class="row g-3 g-lg-4">
        <div class="col-12 col-xl-8">
            <div class="panel-card p-4 mb-3 mb-lg-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 fw-bold mb-0">Pembayaran member</h2>
                    <button class="btn btn-dark rounded-pill px-4" type="button">Catat pembayaran member</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Paket</th>
                                <th>Nominal</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($memberPayments as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item['name'] }}</td>
                                    <td>{{ $item['package'] }}</td>
                                    <td>{{ $item['amount'] }}</td>
                                    <td>{{ $item['method'] }}</td>
                                    <td>
                                        <span class="badge text-bg-{{ $item['status'] === 'Terverifikasi' ? 'success' : 'warning' }}">{{ $item['status'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel-card p-4 mb-3 mb-lg-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 fw-bold mb-0">Pembayaran daily non member</h2>
                    <button class="btn btn-outline-secondary rounded-pill px-4" type="button">Catat daily pass</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pengunjung</th>
                                <th>Tipe</th>
                                <th>Nominal</th>
                                <th>Metode</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dailyPayments as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item['visitor'] }}</td>
                                    <td>{{ $item['type'] }}</td>
                                    <td>{{ $item['amount'] }}</td>
                                    <td>{{ $item['method'] }}</td>
                                    <td><span class="badge text-bg-light border text-dark">{{ $item['receipt'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 fw-bold mb-0">Kelola data transaksi</h2>
                    <button class="btn btn-dark rounded-pill px-4" type="button">Input transaksi</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Invoice</th>
                                <th>Pelanggan</th>
                                <th>Tipe</th>
                                <th>Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item['time'] }}</td>
                                    <td>{{ $item['invoice'] }}</td>
                                    <td>{{ $item['customer'] }}</td>
                                    <td>{{ $item['type'] }}</td>
                                    <td>{{ $item['amount'] }}</td>
                                    <td><span class="badge text-bg-{{ $item['status'] === 'Lunas' ? 'success' : 'warning' }}">{{ $item['status'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="panel-card dark-panel p-4 mb-3 mb-lg-4">
                <h2 class="h4 fw-bold mb-4">Verifikasi status pembayaran member</h2>
                <div class="d-grid gap-3">
                    @foreach ($memberPayments as $item)
                        <div class="rounded-4 p-3" style="background: rgba(255,255,255,.06);">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $item['name'] }}</div>
                                    <div class="small muted-copy">{{ $item['package'] }} • {{ $item['amount'] }}</div>
                                </div>
                                <span class="badge text-bg-{{ $item['status'] === 'Terverifikasi' ? 'success' : 'warning' }}">{{ $item['status'] }}</span>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-light rounded-pill">Verifikasi pembayaran</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel-card p-4 mb-3 mb-lg-4">
                <h2 class="h4 fw-bold mb-4">Cetak bukti pembayaran</h2>
                <div class="d-grid gap-3">
                    @foreach ($receiptQueue as $item)
                        <div class="list-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold">{{ $item['invoice'] }}</div>
                                    <div class="small muted-copy">{{ $item['customer'] }} • {{ $item['type'] }}</div>
                                </div>
                                <span class="badge text-bg-light border text-dark">{{ $item['status'] }}</span>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-outline-secondary btn-sm rounded-pill">Cetak bukti</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel-card dark-panel p-4 h-100">
                <h2 class="h4 fw-bold mb-4">Ringkasan metode pembayaran</h2>
                <div class="d-grid gap-3">
                    @foreach ($paymentMethods as $item)
                        <div class="rounded-4 p-3" style="background: rgba(255,255,255,.06);">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="muted-copy">{{ $item['label'] }}</span>
                                <span class="fw-semibold text-white">{{ $item['value'] }}</span>
                            </div>
                            <div class="mini-progress">
                                <div class="mini-progress-bar bg-{{ $item['color'] }}" style="width: {{ $item['progress'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const shiftCard = document.querySelector('.js-shift-countdown')?.closest('.dark-panel');

            if (!shiftCard) {
                return;
            }

            const startValue = @json($cashierShift['start'] ?? '08:00');
            const endValue = @json($cashierShift['end'] ?? '16:00');
            const countdownEl = shiftCard.querySelector('.js-shift-countdown');
            const noteEl = shiftCard.querySelector('.js-shift-note');

            const toDate = (timeValue) => {
                const [hours, minutes] = timeValue.split(':').map(Number);
                const now = new Date();
                return new Date(now.getFullYear(), now.getMonth(), now.getDate(), hours, minutes, 0, 0);
            };

            const formatRemaining = (milliseconds) => {
                const totalSeconds = Math.max(Math.floor(milliseconds / 1000), 0);
                const totalMinutes = Math.floor(totalSeconds / 60);
                const hours = Math.floor(totalMinutes / 60);
                const minutes = totalMinutes % 60;
                const seconds = totalSeconds % 60;
                const secondLabel = `${String(seconds).padStart(2, '0')} detik`;
                return hours > 0 ? `${hours} jam ${minutes} menit ${secondLabel}` : `${minutes} menit ${secondLabel}`;
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
