@extends('admin.layout')

@section('content')
    @php
        $paymentMethods = ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'];
    @endphp

    <style>
        .dashboard-page {
            padding: 1rem 2rem;
        }

        .dashboard-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        /* Stats Card */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: rgba(255, 255, 255, .03);
            border: 1px solid rgba(255, 255, 255, .06);
            border-radius: 1.2rem;
            padding: 1.2rem;
        }

        .summary-label {
            font-size: .7rem;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 1px;
        }

        .summary-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            margin-top: 5px;
        }

        /* Action Cards */
        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .panel-card {
            background: rgba(255, 255, 255, .025);
            border: 1px solid rgba(255, 255, 255, .05);
            border-radius: 1.2rem;
            padding: 1.5rem;
        }

        .member-table thead th {
            font-size: .7rem;
            text-transform: uppercase;
            color: #9ca3af;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
        }

        .member-table tbody td {
            color: #ffffff !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
            padding: 1rem;
        }

        /* Nav Tab Custom */
        .nav-pills .nav-link {
            color: #dc3545;
            border-radius: 50px;
            padding: 8px 25px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .nav-pills .nav-link.active {
            background: #dc3545 !important;
            color: #fff;
        }

        .btn-checkin {
            height: 60px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
        }
    </style>

    <div class="dashboard-page">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <div class="text-uppercase small fw-bold opacity-50 mb-1" style="letter-spacing: 2px;">Front Desk</div>
                <h1 class="dashboard-title">Check-in Hub</h1>
            </div>
            <div class="text-end">
                <div class="text-white-50 small">{{ now()->format('l, d F Y') }}</div>
                <div class="fw-bold fs-4 text-white" id="liveClock">00:00:00</div>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success border-0 bg-success text-white rounded-3 mb-4 py-2 small shadow-sm">
                <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
            </div>
        @endif

        <div class="summary-grid">
            {{-- Card Member --}}
            <div class="summary-card" style="background: rgba(25, 135, 84, 0.1); border: 1px solid rgba(25, 135, 84, 0.2);">
                <div class="summary-label" style="color: #198754; opacity: 1; font-weight: 700;">Member Check-in Today</div>
                <div class="summary-value" style="color: #198754;">{{ $todayCheckinsCount }}</div>
            </div>

            {{-- Card Guest --}}
            <div class="summary-card"
                style="background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.2);">
                <div class="summary-label" style="color: #0d6efd; opacity: 1; font-weight: 700;">Daily Guest Today</div>
                <div class="summary-value" style="color: #0d6efd;">{{ $todayGuestsCount }}</div>
            </div>
        </div>

        <div class="action-grid">
            {{-- Member Check-in dengan Search --}}
            <div class="panel-card d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold mb-3"><i class="fas fa-qrcode me-2 text-danger"></i>Member Check-in</h5>
                    <p class="small text-white-50">Ketik nama atau kode member untuk mencari.</p>
                </div>
                <form action="{{ route('admin.checkins.store') }}" method="POST">
                    @csrf

                    <select name="gym_member_id" id="memberSelectHidden" style="display:none;" required>
                        <option value="">-- Pilih Member --</option>
                        @foreach ($memberOptions as $member)
                            <option value="{{ $member->id }}" data-name="{{ $member->full_name }}"
                                data-code="{{ $member->checkin_code }}">
                                {{ $member->full_name }} ({{ $member->checkin_code }})
                            </option>
                        @endforeach
                    </select>

                    {{-- Wrapper dengan position relative agar dropdown menempel di sini --}}
                    <div style="position: relative;">
                        <div class="input-group">
                            <input type="text" id="memberSearchInput"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 12px 0 0 12px;" placeholder="Cari nama atau kode member..."
                                autocomplete="off">

                            <button type="submit" class="btn btn-danger px-4" style="border-radius: 0 12px 12px 0;">Check
                                In</button>
                        </div>

                        {{-- Dropdown HARUS di dalam div position:relative ini --}}
                        <div id="memberDropdown"
                            style="display:none; position:fixed; z-index:999999;
            background:#1e1e2e; border:1px solid rgba(255,255,255,0.12);
            border-radius:10px; max-height:220px; overflow-y:auto;">
                        </div>
                    </div>
                </form>
            </div>

            <div class="panel-card d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold mb-3"><i class="fas fa-user-plus me-2 text-info"></i>Daily Guest (Tamu Harian)</h5>
                    <p class="small text-white-50">Input tamu yang membayar kunjungan harian.</p>
                </div>
                <button class="btn btn-info w-100 btn-checkin text-white shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#addGuestModal">
                    <i class="fas fa-ticket-alt me-2"></i> Input Tamu Harian
                </button>
            </div>
        </div>

        <div class="panel-card">
            <ul class="nav nav-pills mb-4 bg-white bg-opacity-5 p-2 rounded-pill d-inline-flex" id="pills-tab"
                role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="pills-member-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-member" type="button">Member Logs</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="pills-guest-tab" data-bs-toggle="pill" data-bs-target="#pills-guest"
                        type="button">Guest Logs</button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                {{-- TAB MEMBER --}}
                <div class="tab-pane fade show active" id="pills-member">
                    <div class="table-responsive">
                        <table class="table align-middle member-table">
                            <thead>
                                <tr>
                                    <th>Nama Member</th>
                                    <th>Masa Aktif</th>
                                    <th>Status</th>
                                    <th>Waktu Check-in</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($checkinRecords as $record)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $record->member->full_name }}</div>
                                            <div class="small opacity-50">{{ $record->member->checkin_code }}</div>
                                        </td>
                                        <td class="small">Hingga: {{ $record->member->expires_at?->format('d M Y') }}</td>
                                        <td><span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">Verified</span>
                                        </td>
                                        <td class="fw-bold">
                                            {{-- Format: 10 May 2026, 10:55 --}}
                                            {{ $record->checked_in_at->format('d M Y, H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 opacity-50">Belum ada member check-in
                                            hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB GUEST --}}
                <div class="tab-pane fade" id="pills-guest">
                    <div class="table-responsive">
                        <table class="table align-middle member-table">
                            <thead>
                                <tr>
                                    <th>Nama Tamu</th>
                                    <th>Metode Bayar</th>
                                    <th>Harga</th>
                                    <th>Waktu Kunjungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailyGuests as $guest)
                                    <tr>
                                        <td>{{ $guest->full_name }}</td>
                                        <td>
                                            <span class="badge bg-white bg-opacity-10 text-white fw-normal">
                                                {{ $guest->payment_method }}
                                            </span>
                                        </td>
                                        <td class="fw-bold text-info">
                                            Rp {{ number_format($guest->payment_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="fw-bold">
                                            {{-- Format: 10 May 2026, 10:55 --}}
                                            @if ($guest->visit_at)
                                                {{ \Carbon\Carbon::parse($guest->visit_at)->format('d M Y, H:i') }}
                                            @else
                                                {{ $guest->created_at->format('d M Y, H:i') }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 opacity-50">Belum ada tamu harian hari
                                            ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addGuestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold">Input Tamu Harian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.checkins.guest.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Nama Tamu</label>
                            <input type="text" name="name"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Harga (Rp)</label>
                            <input type="number" name="price"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" value="25000" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Metode Pembayaran</label>
                            <select name="payment_method"
                                class="form-select bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" required>
                                @foreach ($paymentMethods as $pm)
                                    <option value="{{ $pm }}" class="text-dark">{{ $pm }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-info w-100 rounded-pill fw-bold py-3 text-white">Konfirmasi
                            & Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('liveClock');
            clock.innerText = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    <script>
        (function() {
            const searchInput = document.getElementById('memberSearchInput');
            const selectHidden = document.getElementById('memberSelectHidden');
            const dropdown = document.getElementById('memberDropdown');

            // Angkat ke body agar mengambang di atas segalanya
            document.body.appendChild(dropdown);

            const allMembers = Array.from(selectHidden.options)
                .filter(o => o.value)
                .map(o => ({
                    value: o.value,
                    name: o.dataset.name,
                    code: o.dataset.code
                }));

            function positionDropdown() {
                const rect = searchInput.getBoundingClientRect();
                const dropHeight = dropdown.offsetHeight;
                dropdown.style.top = (rect.top - dropHeight - 4) + 'px';
                dropdown.style.left = rect.left + 'px';
                dropdown.style.width = rect.width + 'px';
            }

            function renderDropdown(query) {
                const q = query.toLowerCase().trim();
                const filtered = q ?
                    allMembers.filter(m =>
                        m.name.toLowerCase().includes(q) ||
                        m.code.toLowerCase().includes(q)) :
                    allMembers;

                dropdown.innerHTML = '';

                if (!filtered.length) {
                    dropdown.innerHTML =
                        '<div style="padding:12px 16px;color:rgba(255,255,255,.4);font-size:13px;">Tidak ada member ditemukan</div>';
                } else {
                    filtered.forEach(m => {
                        const div = document.createElement('div');
                        div.style.cssText =
                            'padding:10px 16px;color:#fff;cursor:pointer;border-bottom:1px solid rgba(255,255,255,.05);font-size:14px;';
                        div.innerHTML = `${m.name} <span style="font-size:11px;opacity:.5;">${m.code}</span>`;
                        div.addEventListener('mousedown', e => {
                            e.preventDefault();
                            searchInput.value = `${m.name} (${m.code})`;
                            selectHidden.value = m.value;
                            dropdown.style.display = 'none';
                        });
                        div.addEventListener('mouseover', () => div.style.background = 'rgba(220,53,69,.15)');
                        div.addEventListener('mouseout', () => div.style.background = '');
                        dropdown.appendChild(div);
                    });
                }

                dropdown.style.display = 'block';
                positionDropdown();
            }

            searchInput.addEventListener('input', () => {
                selectHidden.value = '';
                renderDropdown(searchInput.value);
            });
            searchInput.addEventListener('focus', () => renderDropdown(searchInput.value));
            searchInput.addEventListener('blur', () => setTimeout(() => dropdown.style.display = 'none', 150));
            window.addEventListener('scroll', () => {
                if (dropdown.style.display !== 'none') positionDropdown();
            }, true);
            window.addEventListener('resize', () => {
                if (dropdown.style.display !== 'none') positionDropdown();
            });
        })();
    </script>
@endsection
