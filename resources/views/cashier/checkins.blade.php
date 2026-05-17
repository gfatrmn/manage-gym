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

        /* Non Member / Daily Guest List */
        .nonmember-list {
            display: grid;
            gap: .65rem;
            max-height: 480px;
            overflow: auto;
        }

        .nonmember-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: .8rem;
            align-items: center;
            padding: .8rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, .065);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .nonmember-avatar {
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #ff3b3b, #b80f24);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: .95rem;
            flex: 0 0 auto;
        }

        .nonmember-name {
            color: #fff;
            font-weight: 800;
            line-height: 1.2;
        }

        .nonmember-meta {
            color: rgba(255, 255, 255, .76);
            font-size: .85rem;
            font-weight: 600;
        }

        .nonmember-time {
            color: #fff;
            font-weight: 800;
            font-size: .9rem;
            white-space: nowrap;
        }

        /* Payment method toggle */
        .payment-method-group {
            display: flex;
            gap: .5rem;
        }

        .payment-method-btn {
            flex: 1;
            padding: .65rem 1rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .2);
            background: transparent;
            color: rgba(255, 255, 255, .7);
            font-weight: 700;
            font-size: .95rem;
            cursor: pointer;
            transition: all .2s;
            text-align: center;
        }

        .payment-method-btn.active {
            background: #fff;
            color: #111;
            border-color: #fff;
        }

        /* Modal dark theme */
        .modal-nonmember .modal-content {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 1.5rem;
            color: #fff;
        }

        .modal-nonmember .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            padding: 1.4rem 1.5rem 1rem;
        }

        .modal-nonmember .modal-title {
            font-weight: 800;
            font-size: 1.2rem;
        }

        .modal-nonmember .modal-body {
            padding: 1.4rem 1.5rem;
        }

        .modal-nonmember .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, .08);
            padding: 1rem 1.5rem 1.4rem;
        }

        .modal-nonmember .form-label {
            font-weight: 700;
            color: rgba(255, 255, 255, .9);
            margin-bottom: .4rem;
        }

        .modal-nonmember .form-control {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: .9rem;
            color: #fff;
            padding: .75rem 1rem;
        }

        .modal-nonmember .form-control::placeholder {
            color: rgba(255, 255, 255, .35);
        }

        .modal-nonmember .form-control:focus {
            background: rgba(255, 255, 255, .09);
            border-color: rgba(255, 255, 255, .3);
            box-shadow: none;
            color: #fff;
        }

        .modal-nonmember .btn-close {
            filter: invert(1);
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
        $checkinSection      = request()->query('section', 'cashier') === 'nonmember' ? 'nonmember' : 'cashier';
        $cashierOnlyCheckins = $cashierTodayCheckins->filter(fn ($r) => $r->checkin_method === 'cashier');

        // Summary counts for non member section
        $nmTotal = $nonMemberCheckins?->count() ?? 0;
        $nmCash  = $nonMemberCheckins?->where('payment_method', 'Cash')->count() ?? 0;
        $nmQris  = $nonMemberCheckins?->where('payment_method', 'qris')->count() ?? 0;
    @endphp

    <div class="topbar-card p-4 mb-3 mt-4">
        <div class="section-label">Check-in</div>
        <h1 class="display-6 fw-bold mt-2 mb-0">Check-in</h1>
    </div>

    @if (session('status'))
        <div class="alert alert-info rounded-4 mb-4" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if (session('welcome_name'))
        <div class="alert alert-info rounded-4 mb-4" role="alert">
            Selamat datang, <strong>{{ session('welcome_name') }}</strong>! Check-in Anda berhasil.
        </div>
    @endif

    <div class="checkin-action-grid">
        <a href="{{ route('cashier.checkins', ['section' => 'cashier']) }}" class="checkin-action-card {{ $checkinSection === 'cashier' ? 'active' : '' }}">
            <span class="checkin-action-icon"><i class="fas fa-user-check"></i></span>
            <span>Member</span>
        </a>
        <a href="{{ route('cashier.checkins', ['section' => 'nonmember']) }}" class="checkin-action-card {{ $checkinSection === 'nonmember' ? 'active' : '' }}">
            <span class="checkin-action-icon"><i class="fas fa-user-plus"></i></span>
            <span>Non Member</span>
        </a>
    </div>

    @if ($checkinSection === 'cashier')
        {{-- ── Member Check-in Section ── --}}
        <div class="row g-4">
            <div class="col-12 col-xl-7">
                <div class="card cashier-validation-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <h2 class="h4 fw-bold mb-0 text-white">Pilih Member</h2>
                    </div>

                    <div class="mb-4">
                        <input id="cashier-member-search" type="text" class="form-control" placeholder="Cari nama atau HP">
                    </div>

                    <form method="POST" action="{{ route('cashier.checkins.store') }}" id="cashier-assist-form">
                        @csrf
                        <div class="member-list-wrap" id="cashier-assist-members">
                            <div class="member-checkin-list">
                                @forelse ($cashierCheckinMembers as $member)
                                    <div class="member-checkin-item"
                                         data-name="{{ strtolower($member->full_name) }}"
                                         data-phone="{{ preg_replace('/\D+/', '', (string) $member->phone) }}">
                                        <input class="form-check-input cashier-assist-radio member-checkin-radio"
                                               type="radio" name="gym_member_id"
                                               id="assist-member-{{ $member->id }}"
                                               value="{{ $member->id }}">
                                        <label for="assist-member-{{ $member->id }}" class="mb-0">
                                            <span class="member-checkin-name d-block">{{ $member->full_name }}</span>
                                            <span class="member-checkin-meta">
                                                <span>{{ $member->phone ?: '-' }}</span>
                                                <span>Exp {{ $member->expires_at ? optional($member->expires_at)->format('d M Y') : '-' }}</span>
                                            </span>
                                        </label>
                                        <button type="button"
                                                class="btn btn-sm btn-primary rounded-pill cashier-assist-select"
                                                data-member-id="{{ $member->id }}">Check-in</button>
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
                                    <img src="{{ $record->member->profile_photo_url }}"
                                         alt="Foto {{ $record->member->full_name }}"
                                         class="table-avatar">
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
        {{-- ── Non Member / Daily Guest Section ── --}}
        <div class="row g-4">
            <div class="col-12 col-xl-7">
                <div class="card cashier-validation-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <div>
                            <div class="section-label">Daily Pass</div>
                            <h2 class="h4 fw-bold mt-1 mb-0 text-white">Riwayat Non Member</h2>
                        </div>
                        <button type="button"
                                class="btn btn-danger rounded-pill px-4 fw-bold"
                                data-bs-toggle="modal"
                                data-bs-target="#modalNonMember">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                    </div>

                    <div class="nonmember-list">
                        @forelse ($nonMemberCheckins ?? [] as $guest)
                            <div class="nonmember-item">
                                <div class="nonmember-avatar">
                                    {{ strtoupper(substr($guest->full_name ?? 'N', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="nonmember-name">{{ $guest->full_name }}</div>
                                    <div class="nonmember-meta">
                                        Daily Pass &middot;
                                        {{ strtoupper($guest->payment_method ?? '-') }}
                                        @if ($guest->payment_amount)
                                            &middot; Rp {{ number_format($guest->payment_amount, 0, ',', '.') }}
                                        @endif
                                        @if ($guest->phone)
                                            &middot; {{ $guest->phone }}
                                        @endif
                                    </div>
                                </div>
                                <div class="nonmember-time">
                                    {{ \Carbon\Carbon::parse($guest->visit_at)->format('H:i') }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-secondary">Belum ada data non member hari ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card cashier-qr-info-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                        <h2 class="h4 fw-bold mb-0 today-checkin-title">Ringkasan Hari Ini</h2>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="today-checkin-item">
                            <span class="checkin-action-icon" style="background: rgba(255,59,59,.18);">
                                <i class="fas fa-users" style="color:#ff3b3b;"></i>
                            </span>
                            <div>
                                <div class="today-checkin-name">Total Non Member</div>
                                <div class="small today-checkin-source">Hari ini</div>
                            </div>
                            <div class="today-checkin-count">{{ $nmTotal }}</div>
                        </div>

                        <div class="today-checkin-item">
                            <span class="checkin-action-icon" style="background: rgba(34,197,94,.15);">
                                <i class="fas fa-money-bill-wave" style="color:#22c55e;"></i>
                            </span>
                            <div>
                                <div class="today-checkin-name">Cash</div>
                                <div class="small today-checkin-source">Hari ini</div>
                            </div>
                            <div class="today-checkin-time" style="font-size:.9rem;">{{ $nmCash }}x</div>
                        </div>

                        <div class="today-checkin-item">
                            <span class="checkin-action-icon" style="background: rgba(99,102,241,.15);">
                                <i class="fas fa-qrcode" style="color:#6366f1;"></i>
                            </span>
                            <div>
                                <div class="today-checkin-name">QRIS</div>
                                <div class="small today-checkin-source">Hari ini</div>
                            </div>
                            <div class="today-checkin-time" style="font-size:.9rem;">{{ $nmQris }}x</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal Daily Pass (Non Member) ── --}}
    <div class="modal fade modal-nonmember" id="modalNonMember" tabindex="-1"
         aria-labelledby="modalNonMemberLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNonMemberLabel">Daily Pass Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('cashier.checkins.nonmember.store') }}" id="nonmember-form">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Nama --}}
                            <div class="col-12 col-md-6">
                                <label for="nm-name" class="form-label">Nama</label>
                                <input type="text" name="submitted_name" id="nm-name"
                                       class="form-control" placeholder="Nama" required>
                            </div>

                            {{-- No. HP --}}
                            <div class="col-12 col-md-6">
                                <label for="nm-phone" class="form-label">No. HP <span class="text-secondary fw-normal">(opsional)</span></label>
                                <input type="text" name="submitted_phone" id="nm-phone"
                                       class="form-control" placeholder="08xxxxxxxxxx">
                            </div>

                            {{-- Nominal --}}
                            <div class="col-12 col-md-6">
                                <label for="nm-nominal" class="form-label">Nominal</label>
                                <input type="number" name="nominal" id="nm-nominal"
                                       class="form-control" placeholder="Nominal" min="0">
                            </div>

                            {{-- Metode --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Metode</label>
                                <div class="payment-method-group">
                                    <button type="button" class="payment-method-btn active" data-method="cash">Cash</button>
                                    <button type="button" class="payment-method-btn" data-method="qris">QRIS</button>
                                </div>
                                <input type="hidden" name="payment_method" id="nm-payment-method" value="cash">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" id="nm-reset-btn">Reset</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Member search filter ──────────────────────────────────────────
            const assistSearch = document.getElementById('cashier-member-search');
            const assistRows   = document.querySelectorAll('#cashier-assist-members [data-name]');
            const selectBtns   = document.querySelectorAll('.cashier-assist-select');

            if (assistSearch) {
                assistSearch.addEventListener('input', (e) => {
                    const q = e.target.value.trim().toLowerCase();
                    assistRows.forEach((row) => {
                        const match = !q
                            || (row.dataset.name  || '').includes(q)
                            || (row.dataset.phone || '').includes(q);
                        row.style.display = match ? '' : 'none';
                    });
                });
            }

            selectBtns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const radio = document.getElementById(`assist-member-${btn.dataset.memberId}`);
                    if (radio) {
                        radio.checked = true;
                        document.getElementById('cashier-assist-form')?.requestSubmit();
                    }
                });
            });

            // ── Non Member modal – payment method toggle ──────────────────────
            const methodBtns   = document.querySelectorAll('.payment-method-btn');
            const hiddenMethod = document.getElementById('nm-payment-method');

            methodBtns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    methodBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    if (hiddenMethod) hiddenMethod.value = btn.dataset.method;
                });
            });

            // ── Reset helper ──────────────────────────────────────────────────
            const resetForm = () => {
                const form = document.getElementById('nonmember-form');
                if (form) form.reset();
                methodBtns.forEach(b => b.classList.remove('active'));
                document.querySelector('.payment-method-btn[data-method="cash"]')?.classList.add('active');
                if (hiddenMethod) hiddenMethod.value = 'cash';
            };

            document.getElementById('nm-reset-btn')?.addEventListener('click', resetForm);

            document.getElementById('modalNonMember')
                    ?.addEventListener('hidden.bs.modal', resetForm);
        });
    </script>

    @if (session('welcome_name'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const name    = @json(session('welcome_name'));
                const message = `Selamat datang, ${name}`;
                if ('speechSynthesis' in window) {
                    const utterance  = new SpeechSynthesisUtterance(message);
                    utterance.lang   = 'id-ID';
                    utterance.rate   = 0.95;
                    window.speechSynthesis.speak(utterance);
                }
            });
        </script>
    @endif
@endsection
