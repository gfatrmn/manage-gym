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
            padding: 1rem;
        }

        .cashier-qr-preview canvas,
        .cashier-qr-preview img {
            width: 100% !important;
            height: auto !important;
            border-radius: 1rem;
            background: #fff;
            padding: .8rem;
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
    </style>

    <div class="topbar-card p-4 p-lg-5 mb-4">
        <div class="section-label">Check-in Kasir</div>
        <h1 class="display-6 fw-bold mt-2 mb-3">Check-in hari ini untuk member dan non-member</h1>
        <p class="muted-copy mb-0">Pilih dahulu method check-in yang sesuai. Tetap satu menu Check-in, tetapi pisahkan antara Check-in Kasir dan Check-in QR dalam satu halaman.</p>
    </div>

    @php
        $checkinSection = request()->query('section', 'cashier') === 'qr' ? 'qr' : 'cashier';
    @endphp

    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <a href="{{ route('cashier.checkins', ['section' => 'cashier']) }}" class="btn {{ $checkinSection === 'cashier' ? 'btn-primary' : 'btn-outline-primary' }}">Check-in Kasir</a>
        <a href="{{ route('cashier.checkins', ['section' => 'qr']) }}" class="btn {{ $checkinSection === 'qr' ? 'btn-primary' : 'btn-outline-primary' }}">Check-in QR</a>
    </div>

    @if ($checkinSection === 'cashier')
        <div class="row g-4">
            <div class="col-12 col-xl-7">
                <div class="card cashier-validation-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <div>
                            <div class="section-label">Check-in Kasir</div>
                            <h2 class="h4 fw-bold mt-2 mb-0">Pilih pelanggan dan catat kehadiran.</h2>
                        </div>
                    </div>

                    <div class="small muted-copy mb-4">Cari nama pelanggan di daftar member / non-member. Pilih orang yang datang hari ini dan checklist untuk mencatat kedatangan.</div>

                    <div class="mb-4">
                        <input id="cashier-member-search" type="text" class="form-control" placeholder="Cari nama member atau non-member">
                    </div>

                    <form method="POST" action="{{ route('cashier.checkins.store') }}" id="cashier-assist-form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="cashier-assist-members">
                                <thead>
                                    <tr>
                                        <th>Pilih</th>
                                        <th>Nama</th>
                                        <th>Status</th>
                                        <th>HP</th>
                                        <th>Expire</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cashierCheckinMembers as $member)
                                        <tr data-name="{{ strtolower($member->full_name) }}" data-phone="{{ preg_replace('/\D+/', '', (string) $member->phone) }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input cashier-assist-radio" type="radio" name="gym_member_id" id="assist-member-{{ $member->id }}" value="{{ $member->id }}">
                                                </div>
                                            </td>
                                            <td class="fw-semibold">{{ $member->full_name }}</td>
                                            <td>
                                                <span class="badge text-bg-{{ $member->member_status === 'member' ? 'success' : 'secondary' }}">
                                                    {{ $member->member_status === 'member' ? 'Member' : 'Non-member' }}
                                                </span>
                                            </td>
                                            <td class="small muted-copy">{{ $member->phone ?: '-' }}</td>
                                            <td class="small">{{ $member->expires_at ? optional($member->expires_at)->format('d M Y') : '-' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary cashier-assist-select" data-member-id="{{ $member->id }}">Checklist</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-secondary">Tidak ada member atau non-member yang tersedia untuk check-in.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 d-flex gap-2 flex-wrap align-items-center">
                            <button type="submit" class="btn btn-dark rounded-pill">Catat Check-in</button>
                            <span class="small text-secondary">Pilih pelanggan lalu catat kehadiran hari ini.</span>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card cashier-qr-info-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <div>
                            <div class="section-label">Check-in Hari Ini</div>
                            <h2 class="h4 fw-bold mt-2 mb-0">Daftar member yang sudah check-in</h2>
                        </div>
                        <span class="status-badge badge-soft-teal">{{ $cashierTodayCheckins->count() }} member</span>
                    </div>

                    @if ($cashierLatestCheckin)
                        <div class="small muted-copy mb-3">
                            Check-in terbaru: {{ $cashierLatestCheckin->member?->full_name ?? '-' }}
                            pada {{ $cashierLatestCheckin->checked_in_at->format('d M Y, H:i') }}.
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nama Member</th>
                                    <th>Jam</th>
                                    <th>Sumber</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cashierTodayCheckins as $record)
                                    <tr>
                                        <td>
                                            @if ($record->member?->profile_photo_url)
                                                <img src="{{ $record->member->profile_photo_url }}" alt="Foto {{ $record->member->full_name }}" class="table-avatar">
                                            @elseif ($record->member)
                                                <span class="table-avatar-placeholder">{{ $record->member->profile_initials }}</span>
                                            @else
                                                <span class="table-avatar-placeholder">-</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $record->member?->full_name ?? '-' }}</td>
                                        <td>{{ $record->checked_in_at->format('H:i') }}</td>
                                        <td>
                                            @php
                                                $methodLabel = match ($record->checkin_method) {
                                                    'cashier' => 'Kasir',
                                                    'qr_member' => 'QR Member',
                                                    default => 'Admin',
                                                };
                                                $methodColor = match ($record->checkin_method) {
                                                    'cashier' => 'primary',
                                                    'qr_member' => 'success',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge text-bg-{{ $methodColor }}">{{ $methodLabel }}</span>
                                        </td>
                                        <td class="small muted-copy">{{ $record->notes ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">Belum ada member yang check-in hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-12 col-xl-5">
                <div class="card cashier-qr-card p-4 h-100">
                    <div class="section-label">QR Check-in</div>
                    <h2 class="h4 fw-bold mt-2 mb-2">Tampilkan QR untuk member</h2>
                    <div class="small muted-copy mb-4">Arahkan member untuk memindai QR dan kirimkan data check-in. Pengajuan akan muncul di antrian validasi kasir.</div>

                    <div class="cashier-qr-preview mb-4" data-checkin-qr="{{ route('member.checkin') }}">
                        <div class="small muted-copy text-center">Menyiapkan QR code...</div>
                    </div>

                    <div class="small muted-copy mb-2">Link check-in member</div>
                    <div class="form-control cashier-qr-link mb-3">{{ route('member.checkin') }}</div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('member.checkin') }}" target="_blank" rel="noopener" class="btn btn-dark rounded-pill">Buka Form Check-in Member</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-7">
                <div class="card cashier-validation-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <div>
                            <div class="section-label">Validasi Kasir</div>
                            <h2 class="h4 fw-bold mt-2 mb-0">Pengajuan check-in dari QR</h2>
                        </div>
                        <span class="status-badge badge-soft-teal">{{ $cashierPendingCheckins->count() }} menunggu</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Member Terpilih</th>
                                    <th>Data Diisi</th>
                                    <th>Status Member</th>
                                    <th>Waktu Submit</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cashierPendingCheckins as $record)
                                    @php
                                        $member = $record->member;
                                        $phoneMatches = $member?->phone && $record->submitted_phone
                                            ? preg_replace('/\D+/', '', $member->phone) === preg_replace('/\D+/', '', $record->submitted_phone)
                                            : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $member?->full_name ?? '-' }}</div>
                                            <div class="small muted-copy">{{ $member?->checkin_code ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $record->submitted_name ?: '-' }}</div>
                                            <div class="small muted-copy">{{ $record->submitted_phone ?: '-' }}</div>
                                        </td>
                                        <td>
                                            @if ($member)
                                                <div class="small">Aktif sampai {{ optional($member->expires_at)->format('d M Y') }}</div>
                                                <span class="badge text-bg-{{ $phoneMatches === true ? 'success' : ($phoneMatches === false ? 'warning' : 'secondary') }}">
                                                    {{ $phoneMatches === true ? 'No. HP cocok' : ($phoneMatches === false ? 'No. HP berbeda' : 'No. HP belum ada') }}
                                                </span>
                                            @else
                                                <span class="badge text-bg-danger">Member tidak ditemukan</span>
                                            @endif
                                        </td>
                                        <td>{{ $record->checked_in_at->format('d M Y, H:i') }}</td>
                                        <td>
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">Belum ada pengajuan check-in yang perlu divalidasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qrContainer = document.querySelector('[data-checkin-qr]');

            if (!qrContainer) {
                return;
            }

            const qrValue = qrContainer.dataset.checkinQr;

            const renderFallback = () => {
                qrContainer.innerHTML = '<div class="small muted-copy text-center px-3">QR code tidak berhasil dimuat. Gunakan link form check-in di bawah ini.</div>';
            };

            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
            script.async = true;
            script.onload = function () {
                qrContainer.innerHTML = '';

                if (!window.QRCode) {
                    renderFallback();
                    return;
                }

                new window.QRCode(qrContainer, {
                    text: qrValue,
                    width: 280,
                    height: 280,
                    colorDark: '#0f172a',
                    colorLight: '#ffffff',
                    correctLevel: window.QRCode.CorrectLevel.M,
                });
            };
            script.onerror = renderFallback;
            document.head.appendChild(script);
        });

        const assistSearch = document.getElementById('cashier-member-search');
        const assistRows = document.querySelectorAll('#cashier-assist-members tbody tr');
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
                    radio.scrollIntoView({ block: 'center', behavior: 'smooth' });
                }
            });
        });
    </script>
@endsection
