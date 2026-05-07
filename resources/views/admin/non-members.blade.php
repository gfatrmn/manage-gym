@extends('admin.layout')

@section('content')
    <style>
        .guest-table-wrap {
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 0.6rem;
            -webkit-overflow-scrolling: touch;
            scrollbar-gutter: stable;
        }

        .guest-table {
            width: 100%;
            min-width: 1240px;
            table-layout: auto;
        }

        .guest-table th {
            white-space: nowrap;
        }

        .guest-table th,
        .guest-table td {
            vertical-align: middle;
        }

        .guest-table td {
            overflow-wrap: anywhere;
        }

        .guest-col-photo {
            width: 82px;
            min-width: 82px;
            max-width: 82px;
        }

        .guest-col-name {
            min-width: 190px;
        }

        .guest-col-email {
            min-width: 210px;
        }

        .guest-col-phone,
        .guest-col-status,
        .guest-col-payment,
        .guest-col-date,
        .guest-col-amount {
            white-space: nowrap;
        }

        .guest-col-notes {
            min-width: 220px;
            max-width: 320px;
        }

        .guest-col-actions {
            width: 160px;
            min-width: 160px;
        }

        .guest-actions {
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-end;
            gap: .5rem;
        }

        .guest-actions form {
            margin: 0;
        }

        .guest-actions .btn {
            white-space: nowrap;
        }

        @media (max-width: 1200px) {
            .guest-table {
                min-width: 1180px;
            }

            .guest-table > :not(caption) > * > * {
                padding: .85rem .9rem;
            }
        }
    </style>

    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Non Member</div>
        <h1 class="display-6 fw-bold mt-2 mb-2">Pengelolaan data non-member Arena Gym</h1>
        <p class="muted-copy mb-0">Kelola tamu harian Arena Gym dengan metode pembayaran yang tercatat, tanggal kunjungan otomatis, dan nominal daily pass standar.</p>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Data non-member</h2>
        </div>
        <div class="table-responsive guest-table-wrap">
            <table class="table align-middle mb-0 guest-table">
                <thead>
                    <tr>
                        <th class="guest-col-photo">Foto</th>
                        <th class="guest-col-name">Nama</th>
                        <th class="guest-col-email">Email</th>
                        <th class="guest-col-phone">Telepon</th>
                        <th class="guest-col-status">Status</th>
                        <th class="guest-col-payment">Metode Pembayaran</th>
                        <th class="guest-col-date">Tanggal</th>
                        <th class="guest-col-amount">Jumlah Bayar</th>
                        <th class="guest-col-notes">Catatan</th>
                        <th class="guest-col-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td class="guest-col-photo">
                                @if ($member->profile_photo_url)
                                    <img src="{{ $member->profile_photo_url }}" alt="Foto {{ $member->full_name }}" class="table-avatar">
                                @else
                                    <span class="table-avatar-placeholder">{{ $member->profile_initials }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold guest-col-name">{{ $member->full_name }}</td>
                            <td class="guest-col-email">{{ $member->email ?: '-' }}</td>
                            <td class="guest-col-phone">{{ $member->phone ?: '-' }}</td>
                            <td class="guest-col-status"><span class="badge text-bg-secondary">Non Member</span></td>
                            <td class="guest-col-payment">
                                <span class="badge text-bg-primary">
                                    {{ strtoupper($member->payment_method ?? 'cash') }}
                                </span>
                            </td>
                            <td class="guest-col-date">{{ $member->visit_date?->format('d M Y') ?: '-' }}</td>
                            <td class="fw-semibold guest-col-amount">Rp{{ number_format($member->payment_amount ?? 30000, 0, ',', '.') }}</td>
                            <td class="small muted-copy guest-col-notes">{{ $member->notes ?: '-' }}</td>
                            <td class="guest-col-actions">
                                <div class="guest-actions">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#editGuestModal{{ $member->id }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.non-members.destroy', $member) }}" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-secondary">Belum ada data non-member yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($members as $member)
        <div class="modal fade" id="editGuestModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form method="POST" action="{{ route('admin.non-members.update', $member) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Non Member {{ $member->full_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="full_name" class="form-control" value="{{ $member->full_name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $member->email }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $member->phone }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="cash" @selected($member->payment_method === 'cash')>Cash</option>
                                        <option value="qris" @selected($member->payment_method === 'qris')>QRIS</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal</label>
                                    <input type="text" class="form-control" value="{{ $member->visit_date?->format('d M Y') ?: now()->format('d M Y') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah Pembayaran</label>
                                    <input type="text" class="form-control" value="Rp{{ number_format($member->payment_amount ?? 30000, 0, ',', '.') }}" disabled>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ $member->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-dark">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
