@extends('admin.layout')

@section('content')
    @php
        $isExpiredSection = $memberSection === 'expired';
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

        .member-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.7rem;
        }

        .member-summary-card {
            background: rgba(255, 255, 255, .03);
            border: 1px solid rgba(255, 255, 255, .06);
            border-radius: 1rem;
            padding: 0.85rem 1.1rem;
        }

        .summary-label {
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #fff;
            opacity: 1;
        }

        .summary-note {
            font-size: .7rem;
            font-weight: 400;
            color: #ffffffc9;
            opacity: 0.8;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
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
        }

        .member-photo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .member-photo-placeholder {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ef4444, #991b1b);
            color: white;
            font-size: .8rem;
            font-weight: 700;
        }

        .btn-action {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: none;
            transition: 0.2s;
        }

        .btn-action-success {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .btn-action-info {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .btn-action-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .btn-action-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .ds-section-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: rgba(255, 255, 255, 0.45);
            margin-bottom: .3rem;
        }

        /* =============================================
                                                           SEARCH INPUT
                                                           ============================================= */
        .member-search-wrap {
            position: relative;
            flex: 0 0 auto;
        }

        .member-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.35);
            font-size: 13px;
            pointer-events: none;
        }

        .member-search-input {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            color: #fff;
            font-size: 13px;
            padding: 0.42rem 1rem 0.42rem 2.1rem;
            width: 230px;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .member-search-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .member-search-input:focus {
            border-color: rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.09);
        }

        #memberNoResult {
            display: none;
        }

        .custom-pagination .pagination {
            margin-bottom: 0;
            gap: 5px;
        }

        .custom-pagination .page-link {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            border-radius: 8px !important;
            padding: 0.5rem 0.9rem;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .custom-pagination .page-item.active .page-link {
            background: #ef4444 !important;
            /* Warna Merah Arena */
            border-color: #ef4444 !important;
            color: white;
            font-weight: bold;
        }

        .custom-pagination .page-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .custom-pagination .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.02);
            color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.05);
        }

        /* Menyembunyikan teks bawaan pagination laravel */
        .custom-pagination nav .flex.items-center.justify-between .hidden.sm-flex-1 {
            display: none !important;
        }

        /* Jika menggunakan Bootstrap 5 default, gunakan ini */
        .custom-pagination .small.text-muted {
            display: none !important;
        }

        /* Menghilangkan wrapper teks showing yang duplikat */
        .custom-pagination nav p {
            display: none !important;
        }

        /* =============================================
                                                           RESPONSIVE — Tablet (max-width: 1024px)
                                                           ============================================= */
        @media (max-width: 1024px) {
            .dashboard-page {
                padding: 1rem 1.2rem;
            }

            .dashboard-title {
                font-size: 1.8rem;
            }

            .member-summary-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }

            .panel-card {
                padding: 1.2rem;
            }
        }

        /* =============================================
                                                           RESPONSIVE — Mobile (max-width: 640px)
                                                           ============================================= */
        @media (max-width: 640px) {
            .dashboard-page {
                padding: 0.75rem 0.85rem 1.5rem;
            }

            .dashboard-title {
                font-size: 1.5rem;
            }

            .ds-section-label {
                font-size: 10px;
            }

            .d-flex.justify-content-between.align-items-end.mb-4 {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.85rem;
            }

            .d-flex.justify-content-between.align-items-end.mb-4 .btn {
                width: 100%;
                text-align: center;
            }

            .member-summary-grid {
                grid-template-columns: 1fr;
                gap: 0.6rem;
                margin-bottom: 1.2rem;
            }

            .member-summary-card {
                padding: 0.75rem 1rem;
            }

            .summary-value {
                font-size: 1.3rem;
            }

            .panel-card {
                padding: 1rem 0.85rem;
                border-radius: 1rem;
            }

            /* Tab + search: stack vertically on mobile */
            .panel-tabs-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.65rem !important;
            }

            .panel-tabs-row .btn {
                font-size: 12px;
                padding: 0.4rem 1rem;
            }

            .member-search-wrap {
                width: 100%;
            }

            .member-search-input {
                width: 100%;
                box-sizing: border-box;
            }

            /* Table: hide less-important columns */
            .member-table thead th:nth-child(3),
            .member-table tbody td:nth-child(3),
            .member-table thead th:nth-child(4),
            .member-table tbody td:nth-child(4),
            .member-table thead th:nth-child(5),
            .member-table tbody td:nth-child(5) {
                display: none;
            }

            .member-table thead th {
                font-size: .65rem;
                padding: 0.75rem 0.6rem;
            }

            .member-table tbody td {
                font-size: 12px;
                padding: 0.75rem 0.6rem;
            }

            .member-photo,
            .member-photo-placeholder {
                width: 34px;
                height: 34px;
                font-size: .7rem;
            }

            .btn-action {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .member-table thead th:first-child,
            .member-table tbody td:first-child {
                width: 46px;
            }
        }

        /* =============================================
                                                           RESPONSIVE — Very small (max-width: 380px)
                                                           ============================================= */
        @media (max-width: 380px) {
            .dashboard-title {
                font-size: 1.3rem;
            }

            .member-summary-grid {
                gap: 0.5rem;
            }

            .summary-value {
                font-size: 1.15rem;
            }

            .btn-action {
                width: 28px;
                height: 28px;
                font-size: 11px;
            }

            .d-flex.justify-content-end.gap-2 {
                gap: 4px !important;
            }
        }
    </style>

    <div class="dashboard-page">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <div class="ds-section-label">Arena Gym · Management Member</div>
                <h1 class="dashboard-title">Manajemen Member</h1>
            </div>
            <button class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addMemberModal">
                <i class="fas fa-plus me-2"></i> Tambah Member
            </button>
        </div>

        @if (session('status'))
            <div class="alert alert-success border-0 bg-success text-white rounded-3 mb-4 py-2 small shadow-sm">
                <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
            </div>
        @endif

        <div class="member-summary-grid">
            <div class="member-summary-card">
                <div class="summary-label">Aktif</div>
                <div class="summary-value text-success">{{ $totalActiveCount }}</div>
                <div class="summary-note">Member Aktif</div>
            </div>
            <div class="member-summary-card">
                <div class="summary-label">Expired</div>
                <div class="summary-value text-danger">{{ $totalExpiredCount }}</div>
                <div class="summary-note">Member Expired</div>
            </div>
            <div class="member-summary-card">
                <div class="summary-label">Segera Habis</div>
                <div class="summary-value text-warning">{{ $expiringSoonCount }}</div>
                <div class="summary-note">Member Segera Habis</div>
            </div>
            <div class="member-summary-card">
                <div class="summary-label">Total Data</div>
                <div class="summary-value text-white">{{ $totalMembersCount }}</div>
                <div class="summary-note">Total Data Member</div>
            </div>
        </div>

        <div class="panel-card">
            {{-- Tab buttons + Search --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 panel-tabs-row">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.members', ['section' => 'active']) }}"
                        class="btn btn-sm rounded-pill {{ $isExpiredSection ? 'btn-outline-secondary' : 'btn-light text-dark fw-bold' }}">Member
                        Aktif</a>
                    <a href="{{ route('admin.members', ['section' => 'expired']) }}"
                        class="btn btn-sm rounded-pill {{ $isExpiredSection ? 'btn-light text-dark fw-bold' : 'btn-outline-secondary' }}">Member
                        Expired</a>
                </div>
                <div class="member-search-wrap">
                    <i class="fas fa-search member-search-icon"></i>
                    <input type="text" id="memberSearchInput" class="member-search-input"
                        placeholder="Cari nama atau no. HP...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle member-table">
                    <thead>
                        <tr>
                            <th style="width: 70px">Foto</th>
                            <th>Member</th>
                            <th>Telepon</th>
                            <th>Tgl Daftar</th>
                            <th>Metode Bayar</th>
                            <th>Masa Aktif</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="memberTableBody">
                        @forelse ($currentItems as $member)
                            @php
                                $diff = $member->expires_at ? now()->diffInDays($member->expires_at, false) : null;
                                $daysLeft = $diff !== null ? ceil($diff) : null;
                            @endphp
                            <tr class="member-row" data-name="{{ strtolower($member->full_name) }}"
                                data-phone="{{ $member->phone }}">
                                <td>
                                    @if ($member->profile_photo_path)
                                        <img src="{{ asset('storage/' . $member->profile_photo_path) }}"
                                            class="member-photo"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="member-photo-placeholder" style="display: none;">
                                            {{ strtoupper(substr($member->full_name, 0, 1)) }}</div>
                                    @else
                                        <div class="member-photo-placeholder">
                                            {{ strtoupper(substr($member->full_name, 0, 1)) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $member->full_name }}</div>
                                    <div class="small opacity-50">{{ $member->email ?: 'Tanpa Email' }}</div>
                                </td>
                                <td class="small">{{ $member->phone ?: '-' }}</td>
                                <td class="small">{{ $member->joined_at?->format('d M Y') ?: '-' }}</td>
                                <td><span
                                        class="badge bg-white bg-opacity-10 text-white fw-normal">{{ $member->payment_method ?: 'Cash' }}</span>
                                </td>
                                <td class="small">
                                    <div
                                        class="fw-bold {{ $isExpiredSection ? 'text-danger' : ($daysLeft <= 7 ? 'text-warning' : 'text-white') }}">
                                        Hingga: {{ $member->expires_at?->format('d M Y') }}
                                    </div>
                                    @if (!$isExpiredSection && $daysLeft <= 7)
                                        <div class="fw-bold text-warning" style="font-size: 11px; margin-top: 2px;">
                                            <i class="fas fa-clock me-1"></i>Sisa {{ $daysLeft }} Hari
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn-action btn-action-success" data-bs-toggle="modal"
                                            data-bs-target="#renewMemberModal{{ $member->id }}"><i
                                                class="fas fa-sync-alt"></i></button>
                                        <button class="btn-action btn-action-info" data-bs-toggle="modal"
                                            data-bs-target="#detailMemberModal{{ $member->id }}"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn-action btn-action-warning" data-bs-toggle="modal"
                                            data-bs-target="#editMemberModal{{ $member->id }}"><i
                                                class="fas fa-edit"></i></button>
                                        <form action="{{ route('admin.members.destroy', $member) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-danger"
                                                onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-secondary">Data Kosong</td>
                            </tr>
                        @endforelse
                        {{-- Row muncul saat hasil search kosong --}}
                        <tr id="memberNoResult">
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="fas fa-search me-2 opacity-50"></i>Member tidak ditemukan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold">Tambah Member</h5><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="form-label small text-uppercase fw-bold opacity-50">Nama
                                Lengkap</label><input type="text" name="full_name"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" required></div>
                        <div class="mb-3"><label
                                class="form-label small text-uppercase fw-bold opacity-50">Email</label><input
                                type="email" name="email"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;"></div>
                        <div class="row">
                            <div class="col-6 mb-3"><label class="form-label small text-uppercase fw-bold opacity-50">No.
                                    Telepon</label><input type="text" name="phone"
                                    class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                    style="border-radius: 0.8rem;"></div>
                            <div class="col-6 mb-3"><label
                                    class="form-label small text-uppercase fw-bold opacity-50">Metode Bayar</label>
                                <select name="payment_method"
                                    class="form-select bg-white bg-opacity-10 border-0 text-white p-3"
                                    style="border-radius: 0.8rem;" required>
                                    @foreach ($paymentMethods as $pm)
                                        <option value="{{ $pm }}" class="text-dark">{{ $pm }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3"><label class="form-label small text-uppercase fw-bold opacity-50">Tgl
                                Daftar</label><input type="date" name="joined_at"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" value="{{ date('Y-m-d') }}" required></div>
                        <div class="mb-0"><label class="form-label small text-uppercase fw-bold opacity-50">Foto
                                Profil</label><input type="file" name="profile_photo"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-2"
                                style="border-radius: 0.8rem;"></div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0"><button type="submit"
                            class="btn btn-danger w-100 rounded-pill fw-bold py-3">Simpan Member</button></div>
                </form>
            </div>
        </div>
    </div>

    @forelse ($currentItems as $member)
        <div class="modal fade" id="detailMemberModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                    <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                        <h5 class="modal-title fw-bold">Detail Profil</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        @if ($member->profile_photo_path)
                            <img src="{{ asset('storage/' . $member->profile_photo_path) }}"
                                class="rounded-circle mb-3 border border-3 border-danger"
                                style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="member-photo-placeholder mx-auto mb-3"
                                style="width: 100px; height: 100px; font-size: 2rem;">
                                {{ strtoupper(substr($member->full_name, 0, 1)) }}</div>
                        @endif
                        <h4 class="fw-bold mb-0">{{ $member->full_name }}</h4>
                        <p class="opacity-50 small mb-4">{{ $member->email ?: 'Email tidak tersedia' }}</p>

                        <div class="row g-3 text-start bg-white bg-opacity-10 p-4 rounded-4 mb-4">
                            <div class="col-6"><label
                                    class="small d-block text-uppercase fw-bold opacity-50">Telepon</label>
                                <span class="fw-bold">{{ $member->phone ?: '-' }}</span>
                            </div>
                            <div class="col-6"><label class="small d-block text-uppercase fw-bold opacity-50">Metode
                                    Bayar</label>
                                <span class="badge bg-danger px-3">{{ $member->payment_method ?: 'Cash' }}</span>
                            </div>
                            <div class="col-6"><label class="small d-block text-uppercase fw-bold opacity-50">Tgl
                                    Daftar</label>
                                <span class="fw-bold">{{ $member->joined_at?->format('d M Y') }}</span>
                            </div>
                            <div class="col-6"><label class="small d-block text-uppercase fw-bold opacity-50">Masa
                                    Aktif</label>
                                <span class="fw-bold text-danger">{{ $member->expires_at?->format('d M Y') }}</span>
                            </div>
                        </div>

                        {{-- QR Code --}}
                        <div class="mb-3">
                            <div class="small text-uppercase fw-bold opacity-50 mb-2">QR Code Member</div>
                            <div id="qr-{{ $member->id }}"
                                style="display:inline-block; background:#fff; padding:12px; border-radius:12px;">
                            </div>
                            <div class="small opacity-50 mt-2">{{ $member->checkin_code }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button class="btn btn-danger w-100 rounded-pill fw-bold py-3"
                            onclick="printMemberCard(
                        '{{ $member->full_name }}',
                        '{{ $member->checkin_code }}',
                        '{{ $member->expires_at?->format('d M Y') }}',
                        '{{ $member->phone }}',
                        'qr-{{ $member->id }}'
                    )">
                            <i class="fas fa-print me-2"></i> Cetak Kartu Member
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editMemberModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                    <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                        <h5 class="modal-title fw-bold text-warning">Edit Member</h5><button type="button"
                            class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.members.update', $member) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-body p-4">
                            <div class="mb-3"><label class="form-label small text-uppercase fw-bold opacity-50">Nama
                                    Lengkap</label><input type="text" name="full_name"
                                    class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                    style="border-radius: 0.8rem;" value="{{ $member->full_name }}" required></div>
                            <div class="mb-3"><label
                                    class="form-label small text-uppercase fw-bold opacity-50">Email</label><input
                                    type="email" name="email"
                                    class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                    style="border-radius: 0.8rem;" value="{{ $member->email }}"></div>
                            <div class="row">
                                <div class="col-6 mb-3"><label
                                        class="form-label small text-uppercase fw-bold opacity-50">Telepon</label><input
                                        type="text" name="phone"
                                        class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                        style="border-radius: 0.8rem;" value="{{ $member->phone }}"></div>
                                <div class="col-6 mb-3"><label
                                        class="form-label small text-uppercase fw-bold opacity-50">Metode Bayar</label>
                                    <select name="payment_method"
                                        class="form-select bg-white bg-opacity-10 border-0 text-white p-3"
                                        style="border-radius: 0.8rem;">
                                        @foreach ($paymentMethods as $pm)
                                            <option value="{{ $pm }}" class="text-dark"
                                                {{ $member->payment_method == $pm ? 'selected' : '' }}>{{ $pm }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-0"><label class="form-label small text-uppercase fw-bold opacity-50">Ganti
                                    Foto Profil</label><input type="file" name="profile_photo"
                                    class="form-control bg-white bg-opacity-10 border-0 text-white p-2"
                                    style="border-radius: 0.8rem;"></div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0"><button type="submit"
                                class="btn btn-warning w-100 rounded-pill fw-bold py-3 text-dark">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL RENEW (PERPANJANG) --}}
        @forelse ($currentItems as $member)
            <div class="modal fade" id="renewMemberModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                        <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                            <h5 class="modal-title fw-bold text-success">
                                <i class="fas fa-history me-2"></i>Perpanjang Member
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.members.update', $member) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body p-4">

                                <div class="p-3 mb-4"
                                    style="background: rgba(255,255,255,0.03); border-radius: 1rem; border: 1px solid rgba(255,255,255,0.08);">
                                    <div class="row g-3">
                                        <div class="col-7">
                                            <label class="d-block small text-uppercase fw-bold opacity-50 mb-1"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px;">Nama Member</label>
                                            <div class="fw-bold text-white fs-6">{{ $member->full_name }}</div>
                                        </div>
                                        <div class="col-5 text-end">
                                            <label class="d-block small text-uppercase fw-bold opacity-50 mb-1"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px;">Status Saat Ini</label>
                                            <div>
                                                @if ($member->expires_at && $member->expires_at->isPast())
                                                    <span
                                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill"
                                                        style="font-size: 0.7rem;">Expired</span>
                                                @else
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill"
                                                        style="font-size: 0.7rem;">Aktif</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3 pt-3 border-top border-white border-opacity-10">
                                            <label class="d-block small text-uppercase fw-bold opacity-50 mb-1"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px;">Masa Aktif
                                                Sebelumnya</label>
                                            <div class="text-white-50 fw-semibold">
                                                {{ $member->expires_at?->format('d M Y') ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase opacity-50 mb-2">Durasi
                                        Perpanjangan</label>
                                    {{-- Kita buat select ini read-only atau hanya satu pilihan --}}
                                    <select name="duration"
                                        class="form-select bg-white bg-opacity-10 border-0 text-white p-3 shadow-none"
                                        style="border-radius: 0.8rem;"
                                        onchange="updateRenewPreview(this, '{{ $member->id }}', '{{ $member->expires_at ? $member->expires_at->format('Y-m-d') : now()->format('Y-m-d') }}')">
                                        <option value="1" selected class="text-dark">1 Bulan</option>
                                    </select>
                                </div>

                                <div
                                    class="p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 mt-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small text-success fw-bold opacity-75">Masa Aktif Baru (+<span
                                                    id="prevMonths{{ $member->id }}">1</span> Bln)</div>
                                            <div id="prevNew{{ $member->id }}" class="fw-bold text-success fs-5 mt-1">
                                                {{-- Inisialisasi tampilan awal: Masa Aktif Sekarang + 1 Bulan --}}
                                                @php
                                                    $baseDate =
                                                        $member->expires_at && $member->expires_at->isFuture()
                                                            ? $member->expires_at
                                                            : now();
                                                    echo $baseDate->addMonth()->format('d M Y');
                                                @endphp
                                            </div>
                                        </div>
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 45px; height: 45px; background: rgba(25, 135, 84, 0.2) !important;">
                                            <i class="fas fa-calendar-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 p-4 pt-0">
                                <input type="hidden" name="full_name" value="{{ $member->full_name }}">
                                <input type="hidden" name="payment_method" value="{{ $member->payment_method }}">

                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-3 shadow-sm">
                                    Konfirmasi & Perbarui Member
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach

    <div class="mt-4">
        <div class="d-flex flex-column align-items-center gap-3">
            {{-- Tombol Navigasi --}}
            <div class="custom-pagination">
                @if (isset($currentItems) && $currentItems instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{-- Kita sembunyikan info bawaan lewat CSS di atas --}}
                    {{ $currentItems->links('pagination::bootstrap-5') }}
                @endif
            </div>

            {{-- Keterangan Data Manual (Yang ini tetap dipertahankan) --}}
            <div class="small text-white-50">
                @if (isset($currentItems) && $currentItems instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    Menampilkan {{ $currentItems->firstItem() ?: 0 }} - {{ $currentItems->lastItem() ?: 0 }}
                    dari {{ $currentItems->total() }} member
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        function updateRenewPreview(select, memberId, currentExpiry) {
            const monthsToAdd = parseInt(select.value);

            // Gunakan tanggal expiry dari database, jika sudah lewat gunakan hari ini
            let baseDate = new Date(currentExpiry);
            let today = new Date();

            if (baseDate < today) {
                baseDate = today;
            }

            // Tambah bulan
            baseDate.setMonth(baseDate.getMonth() + monthsToAdd);

            // Format tampilan (Contoh: 10 Aug 2026)
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            const day = String(baseDate.getDate()).padStart(2, '0');
            const month = monthNames[baseDate.getMonth()];
            const year = baseDate.getFullYear();

            document.getElementById('prevNew' + memberId).innerText = `${day} ${month} ${year}`;
            document.getElementById('prevMonths' + memberId).innerText = monthsToAdd;
        }

        // ── Member Search ──────────────────────────────────────────
        document.getElementById('memberSearchInput').addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#memberTableBody .member-row');
            const noResult = document.getElementById('memberNoResult');
            let visible = 0;

            rows.forEach(function(row) {
                const name = row.dataset.name || '';
                const phone = row.dataset.phone || '';
                const match = name.includes(query) || phone.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            noResult.style.display = visible === 0 ? '' : 'none';
        });
    </script>

    <script>
        // Generate QR saat modal dibuka
        document.querySelectorAll('[id^="detailMemberModal"]').forEach(function(modal) {
            modal.addEventListener('shown.bs.modal', function() {
                const qrId = this.querySelector('[id^="qr-"]').id;
                const memberId = qrId.replace('qr-', '');
                const codeEl = this.querySelector('.small.opacity-50.mt-2');
                const code = codeEl ? codeEl.innerText.trim() : memberId;

                const container = document.getElementById(qrId);

                // Jangan generate ulang kalau sudah ada
                if (container.querySelector('canvas') || container.querySelector('img')) return;

                new QRCode(container, {
                    text: code,
                    width: 140,
                    height: 140,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
            });
        });

        // Cetak kartu member
        function printMemberCard(name, code, expires, phone, qrContainerId) {
            const qrContainer = document.getElementById(qrContainerId);
            const qrImg = qrContainer.querySelector('img') || qrContainer.querySelector('canvas');

            let qrSrc = '';
            if (qrImg) {
                qrSrc = qrImg.tagName === 'CANVAS' ? qrImg.toDataURL() : qrImg.src;
            }

            // 4 digit terakhir HP
            const last4 = phone ? phone.slice(-4) : '****';

            const win = window.open('', '_blank');
            win.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Kartu Member - ${name}</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
                .card {
                    width: 85.6mm; height: 54mm;
                    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
                    border-radius: 8mm;
                    padding: 5mm;
                    display: flex;
                    align-items: center;
                    gap: 4mm;
                    color: #fff;
                    position: relative;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
                }
                .card::before {
                    content: '';
                    position: absolute;
                    top: -10mm; right: -10mm;
                    width: 35mm; height: 35mm;
                    background: rgba(220,53,69,0.2);
                    border-radius: 50%;
                }
                .card::after {
                    content: '';
                    position: absolute;
                    bottom: -8mm; left: 20mm;
                    width: 25mm; height: 25mm;
                    background: rgba(220,53,69,0.1);
                    border-radius: 50%;
                }
                .qr-box {
                    flex-shrink: 0;
                    background: #fff;
                    padding: 2mm;
                    border-radius: 3mm;
                    width: 28mm; height: 28mm;
                    display: flex; align-items: center; justify-content: center;
                }
                .qr-box img { width: 100%; height: 100%; }
                .info { flex: 1; z-index: 1; }
                .gym-name { font-size: 7pt; color: #dc3545; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 1mm; }
                .member-name { font-size: 10pt; font-weight: bold; line-height: 1.2; margin-bottom: 2mm; }
                .member-code { font-size: 7pt; color: rgba(255,255,255,0.5); margin-bottom: 3mm; font-family: monospace; }
                .divider { height: 0.3mm; background: rgba(255,255,255,0.15); margin-bottom: 3mm; }
                .expires-label { font-size: 6pt; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; }
                .expires-val { font-size: 8pt; font-weight: bold; color: #dc3545; }
                .last4 { position: absolute; bottom: 3mm; right: 4mm; font-size: 6pt; color: rgba(255,255,255,0.3); letter-spacing: 2px; }
                @media print {
                    body { background: none; }
                    .card { box-shadow: none; }
                }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="qr-box">
                    <img src="${qrSrc}" alt="QR">
                </div>
                <div class="info">
                    <div class="gym-name">Arena Gym</div>
                    <div class="member-name">${name}</div>
                    <div class="member-code">${code}</div>
                    <div class="divider"></div>
                    <div class="expires-label">Aktif hingga</div>
                    <div class="expires-val">${expires}</div>
                </div>
                <div class="last4">••${last4}</div>
            </div>
            <script>window.onload = function() { window.print(); }<\/script>
        </body>
        </html>
    `);
            win.document.close();
        }
    </script>
@endsection
