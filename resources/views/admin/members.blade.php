@extends('admin.layout')

@section('content')

@php
    // Logika filter untuk member yang akan segera habis masa aktifnya (7 hari ke depan)
    $expiringSoonMembers = $members->filter(function ($member) {
        return $member->expires_at &&
            now()->diffInDays($member->expires_at, false) >= 0 &&
            now()->diffInDays($member->expires_at, false) <= 7;
    });

    $isExpiredSection = $memberSection === 'expired';

    $currentSection = [
        'items' => $isExpiredSection ? $expiredMembers : $activeMembers,
        'badge' => $isExpiredSection ? 'text-bg-danger' : 'text-bg-success',
        'statusLabel' => $isExpiredSection ? 'Expired' : 'Aktif',
    ];
@endphp

<style>
    .dashboard-page { padding: 1rem 2rem; }
    .dashboard-heading { position: relative; padding-bottom: 1.2rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,.06); }
    .dashboard-subtitle { font-size: .75rem; letter-spacing: .18em; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: .7rem; font-weight: 500; }
    .dashboard-title { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 700; margin: 0; color: #fff; }

    /* Stats Card Ramping */
    .member-summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.7rem; }
    .member-summary-card { background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.06); border-radius: 1rem; padding: 0.85rem 1.1rem; transition: .2s ease; }
    .summary-label { font-size: .65rem; text-transform: uppercase; letter-spacing: .12em; color: #ffffff; margin-bottom: .3rem; }
    .summary-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; color: #fff; }
    .summary-danger { background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.15); }

    /* Tabel Custom & Warna Teks Putih */
    .panel-card { background: rgba(255,255,255,.025); border: 1px solid rgba(255,255,255,.05); border-radius: 1.2rem; padding: 1.5rem; }
    .member-table thead th { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; padding: 1rem; border-bottom: 1px solid rgba(255,255,255,.06); }

    /* Memaksa semua teks di dalam tabel menjadi putih/terang */
    .member-table tbody td { color: #ffffff !important; }
    .text-white-custom { color: #ffffff !important; }
    .text-muted-custom { color: rgba(255,255,255,0.6) !important; }

    .member-photo { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.1); }
    .member-photo-placeholder { width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #ef4444, #991b1b); color: white; font-size: .8rem; font-weight: 700; }

    /* Tombol Aksi Bulat */
    .btn-action { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; border: none; transition: 0.2s; }
    .btn-action i { font-size: 0.75rem; }
    .btn-action-info { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
    .btn-action-warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
    .btn-action-danger { background: rgba(239, 68, 68, 0.2); color: #f87171; }

    @media (max-width: 992px) { .member-summary-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="dashboard-page">
    <div class="dashboard-heading d-flex justify-content-between align-items-end">
        <div>
            <div class="dashboard-subtitle">ARENA GYM · MEMBER MANAGEMENT</div>
            <h1 class="dashboard-title">Manajemen Member</h1>
        </div>
        <button class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            <i class="fas fa-plus me-2"></i> Tambah Member
        </button>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 bg-success text-white rounded-3 mb-4 py-2 small shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
        </div>
    @endif

    <div class="member-summary-grid">
        <div class="member-summary-card"><div class="summary-label">Aktif</div><div class="summary-value text-success">{{ $activeMembers->count() }}</div></div>
        <div class="member-summary-card"><div class="summary-label">Expired</div><div class="summary-value text-danger">{{ $expiredMembers->count() }}</div></div>
        <div class="member-summary-card summary-danger"><div class="summary-label">7 Hari Lagi</div><div class="summary-value text-white">{{ $expiringSoonMembers->count() }}</div></div>
        <div class="member-summary-card"><div class="summary-label">Total Data</div><div class="summary-value text-info">{{ $members->count() }}</div></div>
    </div>

    <div class="panel-card">
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="{{ route('admin.members', ['section' => 'active']) }}" class="btn btn-sm rounded-pill {{ $isExpiredSection ? 'btn-outline-secondary' : 'btn-light text-dark fw-bold' }}">Member Aktif</a>
            <a href="{{ route('admin.members', ['section' => 'expired']) }}" class="btn btn-sm rounded-pill {{ $isExpiredSection ? 'btn-light text-dark fw-bold' : 'btn-outline-secondary' }}">Member Expired</a>
        </div>

        <div class="member-table-wrap">
            <table class="table align-middle mb-0 member-table">
                <thead>
                    <tr>
                        <th style="width: 70px">Foto</th>
                        <th>Member</th>
                        <th>Telepon</th>
                        <th>Tgl Daftar</th> <th>Status</th>
                        <th>Masa Aktif</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($currentSection['items'] as $member)
                        <tr>
                            <td>
                                @if ($member->profile_photo_url)
                                    <img src="{{ $member->profile_photo_url }}" class="member-photo">
                                @else
                                    <span class="member-photo-placeholder">{{ $member->profile_initials }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-white">{{ $member->full_name }}</div>
                                <div class="small text-muted-custom">{{ $member->email ?: '-' }}</div>
                            </td>
                            <td class="small text-white">{{ $member->phone ?: '-' }}</td>
                            <td class="small text-white">
                                {{ $member->joined_at?->format('d M Y') ?: '-' }}
                            </td>
                            <td><span class="badge {{ $currentSection['badge'] }} rounded-pill" style="font-size: 10px;">{{ $currentSection['statusLabel'] }}</span></td>
                            <td class="small">
                                <div class="fw-bold {{ $isExpiredSection ? 'text-danger' : 'text-white' }}">Hingga: {{ $member->expires_at?->format('d M Y') }}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn-action btn-action-info" data-bs-toggle="modal" data-bs-target="#detailMemberModal{{ $member->id }}" title="Detail"><i class="fas fa-eye"></i></button>
                                    <button class="btn-action btn-action-warning" data-bs-toggle="modal" data-bs-target="#editMemberModal{{ $member->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('admin.members.destroy', $member) }}" method="POST" onsubmit="return confirm('Hapus member {{ $member->full_name }}?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action btn-action-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-secondary">Tidak ada data member dalam kategori ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                <h5 class="modal-title fw-bold">Tambah Member Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">Nama Lengkap</label>
                        <input type="text" name="full_name" class="form-control bg-white bg-opacity-10 border-0 text-white p-3" style="border-radius: 0.8rem;" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">Email</label>
                        <input type="email" name="email" class="form-control bg-white bg-opacity-10 border-0 text-white p-3" style="border-radius: 0.8rem;" placeholder="Email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">No. Telepon</label>
                        <input type="text" name="phone" class="form-control bg-white bg-opacity-10 border-0 text-white p-3" style="border-radius: 0.8rem;" placeholder="08xxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">Tanggal Bergabung</label>
                        <input type="date" name="joined_at" class="form-control bg-white bg-opacity-10 border-0 text-white p-3" style="border-radius: 0.8rem;" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">Foto Profil</label>
                        <input type="file" name="profile_photo" class="form-control bg-white bg-opacity-10 border-0 text-white p-2" style="border-radius: 0.8rem;">
                    </div>
                    <div class="alert alert-info py-2 small border-0 bg-info bg-opacity-10 text-info mb-0" style="border-radius: 0.8rem;"><i class="fas fa-info-circle me-2"></i> Masa aktif diset otomatis 1 bulan.</div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 p-4">
                    <button type="button" class="btn btn-link text-white text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Simpan Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($currentSection['items'] as $member)
    <div class="modal fade" id="detailMemberModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold">Profil Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    @if ($member->profile_photo_url)
                        <img src="{{ $member->profile_photo_url }}" class="rounded-circle mb-3 border border-3 border-danger shadow" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="member-photo-placeholder mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2rem;">
                            {{ $member->profile_initials }}
                        </div>
                    @endif

                    <h4 class="fw-bold mb-1 text-white">{{ $member->full_name }}</h4>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem;" class="mb-4">
                        {{ $member->email ?: 'Email tidak tersedia' }}
                    </p>

                    <div class="row g-3 text-start bg-white bg-opacity-10 p-4 rounded-4">
                        <div class="col-6">
                            <label class="small d-block text-uppercase fw-bold mb-1" style="color: rgba(255,255,255,0.5); letter-spacing: 0.05em;">Telepon</label>
                            <span class="text-white fw-bold">{{ $member->phone ?: '-' }}</span>
                        </div>
                        <div class="col-6">
                            <label class="small d-block text-uppercase fw-bold mb-1" style="color: rgba(255,255,255,0.5); letter-spacing: 0.05em;">Status</label>
                            <span class="badge {{ $isExpiredSection ? 'bg-danger' : 'bg-success' }} px-3 py-2 rounded-pill">{{ $isExpiredSection ? 'Expired' : 'Aktif' }}</span>
                        </div>
                        <div class="col-6">
                            <label class="small d-block text-uppercase fw-bold mb-1" style="color: rgba(255,255,255,0.5); letter-spacing: 0.05em;">Gabung</label>
                            <span class="text-white fw-bold">{{ $member->joined_at?->format('d M Y') }}</span>
                        </div>
                        <div class="col-6">
                            <label class="small d-block text-uppercase fw-bold mb-1" style="color: rgba(255,255,255,0.5); letter-spacing: 0.05em;">Hingga</label>
                            <span class="text-danger fw-bold" style="font-size: 1.1rem;">{{ $member->expires_at?->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 justify-content-center">
                    <button type="button" class="btn btn-outline-light rounded-pill px-5" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editMemberModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                <div class="modal-header border-bottom border-white border-opacity-10 p-4"><h5 class="modal-title fw-bold text-warning">Edit Member</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <form action="{{ route('admin.members.update', $member) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">Nama Lengkap</label><input type="text" name="full_name" class="form-control bg-white bg-opacity-10 border-0 text-white p-3" style="border-radius: 0.8rem;" value="{{ $member->full_name }}" required></div>
                        <div class="mb-3"><label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">Email</label><input type="email" name="email" class="form-control bg-white bg-opacity-10 border-0 text-white p-3" style="border-radius: 0.8rem;" value="{{ $member->email }}"></div>
                        <div class="mb-3"><label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">No. Telepon</label><input type="text" name="phone" class="form-control bg-white bg-opacity-10 border-0 text-white p-3" style="border-radius: 0.8rem;" value="{{ $member->phone }}"></div>
                        <div class="mb-3"><label class="form-label small text-uppercase fw-bold mb-2" style="color: rgba(255,255,255,0.6);">Foto Baru (Opsional)</label><input type="file" name="profile_photo" class="form-control bg-white bg-opacity-10 border-0 text-white p-2" style="border-radius: 0.8rem;"></div>
                    </div>
                    <div class="modal-footer border-top border-white border-opacity-10 p-4">
                        <button type="button" class="btn btn-link text-white text-decoration-none" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection
