@extends('admin.layout')

@section('content')
    <style>
        .cashier-qr-card,
        .cashier-qr-info-card {
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
        }

        .cashier-qr-preview {
            width: min(100%, 340px);
            aspect-ratio: 1 / 1;
            margin: 0 auto;
            border-radius: 1.5rem;
            border: 1px dashed var(--border);
            background: rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .cashier-qr-preview video {
            position: absolute;
            inset: 1rem;
            width: auto;
            height: auto;
            object-fit: cover;
            border-radius: 1rem;
            background: #111827;
        }

        .cashier-scanner-frame {
            position: relative;
            overflow: hidden;
        }

        .cashier-scanner-placeholder {
            position: absolute;
            inset: 1rem;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            background: #111827;
        }

        .cashier-html5-reader {
            position: absolute;
            inset: 0;
            z-index: 1;
            width: 100%;
            height: 100%;
        }

        .cashier-html5-reader video {
            inset: 0;
            border-radius: 1.5rem;
        }

        .cashier-html5-reader__hidden {
            display: none;
        }

        .cashier-scanner-frame::after {
            content: '';
            position: absolute;
            inset: 18%;
            border: 2px solid rgba(255, 255, 255, .85);
            border-radius: 1rem;
            box-shadow: 0 0 0 999px rgba(15, 23, 42, .28);
            pointer-events: none;
        }

        .cashier-scan-result {
            min-height: 2.75rem;
        }

        .cashier-confirm-photo {
            width: 88px;
            height: 88px;
            border-radius: 999px;
            object-fit: cover;
            border: 3px solid rgba(255, 59, 59, .38);
        }

        .cashier-confirm-placeholder {
            width: 88px;
            height: 88px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff3b3b, #8f1020);
            color: #fff;
            font-weight: 900;
            font-size: 1.45rem;
            border: 3px solid rgba(255, 59, 59, .38);
        }

        .cashier-confirm-info {
            background: #f8fafc;
            color: #111827;
        }

        .cashier-confirm-info .cashier-confirm-label {
            color: #6b7280;
        }

        .cashier-confirm-info .cashier-confirm-value {
            color: #111827;
        }

        .cashier-qr-link {
            word-break: break-all;
        }

        .cashier-validation-card {
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
        }

        .checkin-action-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
            margin-bottom: 1rem;
        }

        .checkin-action-card {
            min-height: 82px;
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: 1rem;
            border-radius: 1.1rem;
            border: 1px solid rgba(255, 255, 255, .1);
            background: rgba(255, 255, 255, .035);
            color: var(--text-main);
            text-decoration: none;
            font-weight: 800;
        }

        .checkin-action-card.active {
            border-color: rgba(255, 59, 59, .45);
            background: linear-gradient(135deg, #ff3b3b, #b80f24);
            box-shadow: 0 16px 34px rgba(255, 59, 59, .22);
            color: #fff;
        }

        .checkin-action-card:hover {
            color: #fff;
            border-color: rgba(255, 59, 59, .42);
        }

        .checkin-action-icon {
            width: 2.7rem;
            height: 2.7rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            flex: 0 0 auto;
        }

        .member-list-wrap {
            max-height: 540px;
            overflow: auto;
        }

        .member-checkin-list {
            display: grid;
            gap: .7rem;
        }

        .member-checkin-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: .85rem;
            align-items: center;
            padding: .85rem;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 1rem;
            background: rgba(255, 255, 255, .055);
        }

        .member-checkin-name {
            color: #fff;
            font-weight: 800;
            line-height: 1.2;
        }

        .member-checkin-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem .75rem;
            color: rgba(255, 255, 255, .78);
            font-size: .88rem;
            font-weight: 600;
        }

        .member-checkin-radio {
            width: 1.15rem;
            height: 1.15rem;
        }

        .today-checkin-list {
            display: grid;
            gap: .65rem;
        }

        .today-checkin-title {
            color: #fff;
        }

        .today-checkin-count {
            min-width: 2.35rem;
            height: 2.35rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #ff3b3b;
            color: #fff;
            font-weight: 800;
        }

        .today-checkin-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: .8rem;
            align-items: center;
            padding: .8rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, .065);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .today-checkin-name,
        .today-checkin-time {
            color: #fff;
            font-weight: 800;
        }

        .today-checkin-source {
            color: rgba(255, 255, 255, .76);
            font-weight: 600;
        }

        @media (max-width: 767.98px) {
            .checkin-action-grid {
                grid-template-columns: 1fr;
            }

            .member-checkin-item {
                grid-template-columns: auto minmax(0, 1fr);
            }

            .member-checkin-item .cashier-assist-select {
                grid-column: 1 / -1;
                width: 100%;
            }
        }
    </style>

    @php
        $checkinSection = request()->query('section', 'cashier') === 'qr' ? 'qr' : 'cashier';
        $cashierOnlyCheckins = $cashierTodayCheckins->filter(fn ($record) => $record->checkin_method === 'cashier');
        $qrPendingCheckins = $cashierPendingCheckins->filter(fn ($record) => $record->checkin_method === 'qr_member');
        $qrTodayCheckins = $cashierTodayCheckins->filter(fn ($record) => $record->checkin_method === 'qr_member');
        $qrEntries = $qrPendingCheckins->concat($qrTodayCheckins)->unique('id')->values();
    @endphp

    <div class="topbar-card p-4 mb-3">
        <div class="section-label">Check-in</div>
        <h1 class="display-6 fw-bold mt-2 mb-0">Check-in</h1>
    </div>

    @if (session('welcome_name'))
        <div class="alert alert-info rounded-4 mb-4" role="alert">
            Selamat datang, <strong>{{ session('welcome_name') }}</strong>! Check-in Anda berhasil.
        </div>
    @endif

    <div class="checkin-action-grid">
        <a href="{{ route('cashier.checkins', ['section' => 'cashier']) }}" class="checkin-action-card {{ $checkinSection === 'cashier' ? 'active' : '' }}">
            <span class="checkin-action-icon"><i class="fas fa-user-check"></i></span>
            <span>Manual</span>
        </a>
        <a href="{{ route('cashier.checkins', ['section' => 'qr', 'scan' => 1]) }}" class="checkin-action-card {{ $checkinSection === 'qr' ? 'active' : '' }}">
            <span class="checkin-action-icon"><i class="fas fa-barcode"></i></span>
            <span>Scan QR</span>
        </a>
    </div>

    @if ($checkinSection === 'cashier')
        <div class="row g-4">
            <div class="col-12 col-xl-7">
                <div class="card cashier-validation-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <h2 class="h4 fw-bold mb-0">Pilih Member</h2>
                    </div>

                    <div class="mb-4">
                        <input id="cashier-member-search" type="text" class="form-control" placeholder="Cari nama atau HP">
                    </div>

                    <form method="POST" action="{{ route('cashier.checkins.store') }}" id="cashier-assist-form">
                        @csrf
                        <div class="member-list-wrap" id="cashier-assist-members">
                            <div class="member-checkin-list">
                                @forelse ($cashierCheckinMembers as $member)
                                    <div class="member-checkin-item" data-name="{{ strtolower($member->full_name) }}" data-phone="{{ preg_replace('/\D+/', '', (string) $member->phone) }}">
                                        <input class="form-check-input cashier-assist-radio member-checkin-radio" type="radio" name="gym_member_id" id="assist-member-{{ $member->id }}" value="{{ $member->id }}">
                                        <label for="assist-member-{{ $member->id }}" class="mb-0">
                                            <span class="member-checkin-name d-block">{{ $member->full_name }}</span>
                                            <span class="member-checkin-meta">
                                                <span>{{ $member->phone ?: '-' }}</span>
                                                <span>Exp {{ $member->expires_at ? optional($member->expires_at)->format('d M Y') : '-' }}</span>
                                            </span>
                                        </label>
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill cashier-assist-select" data-member-id="{{ $member->id }}">Check-in</button>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-secondary">Tidak ada member tersedia.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-3 d-flex gap-2 flex-wrap align-items-center">
                            <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan Check-in</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card cashier-qr-info-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <h2 class="h4 fw-bold mb-0 today-checkin-title">Hari Ini</h2>
                        <span class="today-checkin-count">{{ $cashierOnlyCheckins->count() }}</span>
                    </div>

                    <div class="today-checkin-list">
                        @forelse ($cashierOnlyCheckins as $record)
                            <div class="today-checkin-item">
                                @if ($record->member?->profile_photo_url)
                                    <img src="{{ $record->member->profile_photo_url }}" alt="Foto {{ $record->member->full_name }}" class="table-avatar">
                                @elseif ($record->member)
                                    <span class="table-avatar-placeholder">{{ $record->member->profile_initials }}</span>
                                @else
                                    <span class="table-avatar-placeholder">-</span>
                                @endif
                                <div class="min-w-0">
                                    <div class="today-checkin-name">{{ $record->member?->full_name ?? '-' }}</div>
                                    <div class="small today-checkin-source">Kasir</div>
                                </div>
                                <div class="today-checkin-time">{{ $record->checked_in_at->format('H:i') }}</div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-secondary">Belum ada check-in lewat kasir.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-12 col-xl-5">
                <div class="card cashier-qr-card p-4 h-100">
                    <div class="section-label">Scan QR</div>
                    <h2 class="h4 fw-bold mt-2 mb-3">Kamera</h2>

                    <div class="cashier-qr-preview cashier-scanner-frame mb-4" data-barcode-scanner>
                        <video id="cashier-barcode-video" muted playsinline></video>
                        <div id="cashier-html5-reader" class="cashier-html5-reader cashier-html5-reader__hidden"></div>
                        <div class="cashier-scanner-placeholder small muted-copy text-center px-3" id="cashier-scanner-placeholder">Kamera belum aktif.</div>
                    </div>

                    <div class="cashier-scan-result alert alert-light border small mb-3" id="cashier-scan-result">
                        Belum ada QR yang discan.
                    </div>

                    <form method="POST" action="{{ route('cashier.checkins.store') }}" id="cashier-barcode-form" class="d-grid gap-2">
                        @csrf
                        <input type="hidden" name="actor" value="qr_member">
                        <input type="hidden" name="checkin_code" id="cashier-scanned-code">
                        <button type="button" class="btn btn-dark rounded-pill" id="cashier-start-scanner">Mulai Kamera</button>
                        <button type="button" class="btn btn-outline-secondary rounded-pill" id="cashier-stop-scanner" disabled>Matikan Kamera</button>
                    </form>

                </div>
            </div>
            <div class="col-12 col-xl-7">
                <div class="card cashier-validation-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <div>
                            <div class="section-label">QR</div>
                            <h2 class="h4 fw-bold mt-2 mb-0">Tabel Check-in QR</h2>
                        </div>
                        <span class="status-badge badge-soft-teal">{{ $qrEntries->count() }} masuk</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Kode</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($qrEntries as $record)
                                    @php
                                        $member = $record->member;
                                        $statusLabel = $record->verification_status === 'pending' ? 'Menunggu' : 'Diverifikasi';
                                        $statusClass = $record->verification_status === 'pending' ? 'warning' : 'success';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($member?->profile_photo_url)
                                                    <img src="{{ $member->profile_photo_url }}" alt="Foto {{ $member->full_name }}" class="table-avatar">
                                                @elseif ($member)
                                                    <span class="table-avatar-placeholder">{{ $member->profile_initials }}</span>
                                                @else
                                                    <span class="table-avatar-placeholder">-</span>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $member?->full_name ?? ($record->submitted_name ?: '-') }}</div>
                                                    <div class="small muted-copy">{{ $record->submitted_phone ?: ($member?->phone ?? '-') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $member?->checkin_code ?? '-' }}</td>
                                        <td>
                                            <span class="badge text-bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>{{ $record->checked_in_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            @if ($record->verification_status === 'pending')
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <form method="POST" action="{{ route('cashier.checkins.verify', $record) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill">Validasi</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('cashier.checkins.reject', $record) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Tolak</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">Belum ada check-in lewat QR hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="cashierConfirmQrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.35rem;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Konfirmasi Check-in QR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div id="cashier-confirm-photo-wrap" class="mb-3"></div>
                    <h3 class="h4 fw-bold mb-1" id="cashier-confirm-name">-</h3>
                    <div class="text-secondary mb-3" id="cashier-confirm-phone">-</div>
                    <div class="row g-2 text-start">
                        <div class="col-6">
                            <div class="p-3 rounded-4 cashier-confirm-info">
                                <div class="small fw-semibold cashier-confirm-label">Kode</div>
                                <div class="fw-bold cashier-confirm-value" id="cashier-confirm-code">-</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-4 cashier-confirm-info">
                                <div class="small fw-semibold cashier-confirm-label">Masa Aktif</div>
                                <div class="fw-bold cashier-confirm-value" id="cashier-confirm-expiry">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="cashier-confirm-submit">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scanner = document.querySelector('[data-barcode-scanner]');
            const video = document.getElementById('cashier-barcode-video');
            const placeholder = document.getElementById('cashier-scanner-placeholder');
            const startButton = document.getElementById('cashier-start-scanner');
            const stopButton = document.getElementById('cashier-stop-scanner');
            const resultBox = document.getElementById('cashier-scan-result');
            const scannedCode = document.getElementById('cashier-scanned-code');
            const barcodeForm = document.getElementById('cashier-barcode-form');
            const confirmModalEl = document.getElementById('cashierConfirmQrModal');
            const confirmSubmit = document.getElementById('cashier-confirm-submit');
            const confirmPhotoWrap = document.getElementById('cashier-confirm-photo-wrap');
            const confirmName = document.getElementById('cashier-confirm-name');
            const confirmPhone = document.getElementById('cashier-confirm-phone');
            const confirmCode = document.getElementById('cashier-confirm-code');
            const confirmExpiry = document.getElementById('cashier-confirm-expiry');
            const lookupUrl = @json(route('cashier.checkins.lookup-member'));
            const shouldAutoStartScanner = @json($checkinSection === 'qr' && request()->boolean('scan'));
            let scannerStream = null;
            let scanTimer = null;
            let detector = null;
            let html5QrCode = null;
            let lastScannedValue = '';
            let confirmModal = null;

            const setScannerMessage = (message, type = 'light') => {
                if (!resultBox) return;

                resultBox.className = `cashier-scan-result alert alert-${type} border small mb-3`;
                resultBox.textContent = message;
            };

            const loadScript = (src) => new Promise((resolve, reject) => {
                const existingScript = document.querySelector(`script[src="${src}"]`);

                if (existingScript) {
                    existingScript.addEventListener('load', resolve, { once: true });
                    existingScript.addEventListener('error', reject, { once: true });
                    resolve();
                    return;
                }

                const script = document.createElement('script');
                script.src = src;
                script.async = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });

            const stopScanner = async () => {
                if (scanTimer) {
                    window.clearInterval(scanTimer);
                    scanTimer = null;
                }

                if (html5QrCode) {
                    try {
                        await html5QrCode.stop();
                        await html5QrCode.clear();
                    } catch (error) {
                        // Scanner may already be stopped by the browser.
                    }

                    html5QrCode = null;
                }

                if (scannerStream) {
                    scannerStream.getTracks().forEach((track) => track.stop());
                    scannerStream = null;
                }

                if (video) {
                    video.srcObject = null;
                }

                const html5Reader = document.getElementById('cashier-html5-reader');
                if (html5Reader) {
                    html5Reader.classList.add('cashier-html5-reader__hidden');
                }

                if (placeholder) {
                    placeholder.style.display = '';
                    placeholder.textContent = 'Menyiapkan kamera...';
                }

                if (startButton) {
                    startButton.disabled = false;
                }

            };

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const showMemberConfirmation = (member) => {
                if (!confirmModalEl) {
                    barcodeForm.submit();
                    return;
                }

                if (confirmPhotoWrap) {
                    confirmPhotoWrap.innerHTML = member.profile_photo_url
                        ? `<img src="${escapeHtml(member.profile_photo_url)}" alt="Foto ${escapeHtml(member.full_name)}" class="cashier-confirm-photo">`
                        : `<span class="cashier-confirm-placeholder">${escapeHtml(member.profile_initials || '-')}</span>`;
                }

                if (confirmName) confirmName.textContent = member.full_name || '-';
                if (confirmPhone) confirmPhone.textContent = member.phone || '-';
                if (confirmCode) confirmCode.textContent = member.checkin_code || '-';
                if (confirmExpiry) confirmExpiry.textContent = member.expires_at || '-';

                confirmModal = confirmModal || new bootstrap.Modal(confirmModalEl);
                confirmModal.show();
            };

            const lookupScannedMember = async (code) => {
                const response = await fetch(`${lookupUrl}?checkin_code=${encodeURIComponent(code)}`, {
                    headers: {
                        Accept: 'application/json',
                    },
                });
                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(payload.message || 'Data member tidak ditemukan.');
                }

                return payload.member;
            };

            const submitScannedCode = async (value) => {
                const normalized = value.trim().toUpperCase();

                if (!normalized || normalized === lastScannedValue) {
                    return;
                }

                lastScannedValue = normalized;
                scannedCode.value = normalized;
                setScannerMessage(`QR terbaca: ${normalized}. Mengambil data member...`, 'success');
                await stopScanner();

                try {
                    const member = await lookupScannedMember(normalized);
                    setScannerMessage(`QR terbaca: ${normalized}. Menunggu konfirmasi kasir.`, 'success');
                    showMemberConfirmation(member);
                } catch (error) {
                    scannedCode.value = '';
                    setScannerMessage(error.message || 'QR tidak valid. Silakan scan ulang.', 'danger');
                    window.setTimeout(() => {
                        lastScannedValue = '';
                    }, 1000);
                }
            };

            const startHtml5Scanner = async () => {
                const html5Reader = document.getElementById('cashier-html5-reader');

                if (!html5Reader) {
                    setScannerMessage('Scanner fallback tidak tersedia. Pakai input kode manual di bawah.', 'warning');
                    return;
                }

                try {
                    await loadScript('https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js');

                    if (!window.Html5Qrcode) {
                        throw new Error('Html5Qrcode not loaded');
                    }

                    if (video) {
                        video.srcObject = null;
                    }

                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }

                    html5Reader.classList.remove('cashier-html5-reader__hidden');
                    html5QrCode = new window.Html5Qrcode('cashier-html5-reader');

                    const formats = window.Html5QrcodeSupportedFormats
                        ? [
                            window.Html5QrcodeSupportedFormats.QR_CODE,
                            window.Html5QrcodeSupportedFormats.CODE_39,
                            window.Html5QrcodeSupportedFormats.CODE_128,
                            window.Html5QrcodeSupportedFormats.EAN_13,
                            window.Html5QrcodeSupportedFormats.EAN_8,
                        ]
                        : undefined;

                    await html5QrCode.start(
                        { facingMode: 'environment' },
                        {
                            fps: 10,
                            qrbox: { width: 230, height: 230 },
                            formatsToSupport: formats,
                        },
                        (decodedText) => submitScannedCode(decodedText),
                        () => {}
                    );

                    setScannerMessage('Kamera aktif. Arahkan QR ke area scan.', 'info');
                } catch (error) {
                    stopScanner();
                    setScannerMessage('Kamera belum bisa dibuka oleh scanner otomatis. Coba izinkan kamera atau pakai input manual.', 'danger');
                }
            };

            const startScanner = async () => {
                if (!scanner || !video || !startButton || !stopButton) {
                    return;
                }

                try {
                    startButton.disabled = true;
                    stopButton.disabled = false;
                    lastScannedValue = '';
                    setScannerMessage('Menyiapkan kamera...', 'info');

                    if (!navigator.mediaDevices?.getUserMedia) {
                        setScannerMessage('Browser ini tidak menyediakan akses kamera. Pakai input kode manual di bawah.', 'warning');
                        return;
                    }

                    if (!('BarcodeDetector' in window)) {
                        setScannerMessage('Menyiapkan kamera scanner fallback...', 'info');
                        await startHtml5Scanner();
                        return;
                    }

                    detector = detector || new window.BarcodeDetector({
                        formats: ['qr_code', 'code_39', 'code_128', 'ean_13', 'ean_8'],
                    });

                    scannerStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'environment' } },
                        audio: false,
                    });

                    video.srcObject = scannerStream;
                    await video.play();

                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }

                    setScannerMessage('Kamera aktif. Arahkan QR ke area scan.', 'info');

                    scanTimer = window.setInterval(async () => {
                        if (!detector || !video || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) {
                            return;
                        }

                        try {
                            const barcodes = await detector.detect(video);
                            const firstCode = barcodes[0]?.rawValue || '';
                            submitScannedCode(firstCode);
                        } catch (error) {
                            setScannerMessage('Scanner kamera belum bisa membaca QR. Coba dekatkan QR atau pakai input manual.', 'warning');
                        }
                    }, 450);
                } catch (error) {
                    stopScanner();
                    setScannerMessage('Kamera tidak bisa dibuka. Izinkan kamera lalu coba lagi.', 'danger');
                }
            };

            if (startButton) {
                startButton.addEventListener('click', startScanner);
            }

            if (stopButton) {
                stopButton.addEventListener('click', stopScanner);
            }

            if (confirmSubmit) {
                confirmSubmit.addEventListener('click', () => {
                    if (!scannedCode.value) {
                        setScannerMessage('Kode QR belum tersedia. Silakan scan ulang.', 'danger');
                        return;
                    }

                    confirmSubmit.disabled = true;
                    confirmSubmit.textContent = 'Menyimpan...';
                    barcodeForm.submit();
                });
            }

            if (shouldAutoStartScanner && startButton) {
                window.setTimeout(startScanner, 350);
            }

            window.addEventListener('beforeunload', stopScanner);
        });

        const assistSearch = document.getElementById('cashier-member-search');
        const assistRows = document.querySelectorAll('#cashier-assist-members [data-name]');
        const selectButtons = document.querySelectorAll('.cashier-assist-select');

        const filterMembers = (query) => {
            const normalized = query.trim().toLowerCase();

            assistRows.forEach((row) => {
                const name = row.dataset.name || '';
                const phone = row.dataset.phone || '';
                const visible = !normalized || name.includes(normalized) || phone.includes(normalized);

                row.style.display = visible ? '' : 'none';
            });
        };

        if (assistSearch) {
            assistSearch.addEventListener('input', (event) => {
                filterMembers(event.target.value);
            });
        }

        selectButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const memberId = button.dataset.memberId;
                const radio = document.getElementById(`assist-member-${memberId}`);

                if (radio) {
                    radio.checked = true;
                    document.getElementById('cashier-assist-form')?.requestSubmit();
                }
            });
        });
    </script>

    @if (session('welcome_name'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const name = @json(session('welcome_name'));
                const message = `Selamat datang, ${name}`;

                if ('speechSynthesis' in window) {
                    const utterance = new SpeechSynthesisUtterance(message);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.95;
                    window.speechSynthesis.speak(utterance);
                }
            });
        </script>
    @endif
@endsection
