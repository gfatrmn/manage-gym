@extends('admin.layout')

@section('content')
    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Persetujuan Foto Profil</div>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="display-6 fw-bold mt-2 mb-1">Request Ganti Foto Member</h1>
                <p class="muted-copy mb-0">Setelah member mengganti foto langsung 3x, pengajuan berikutnya akan muncul di sini.</p>
            </div>
            <span class="status-badge badge-soft-green">{{ $requests->where('status', 'pending')->count() }} pending di halaman ini</span>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3">{{ session('status') }}</div>
    @endif

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
            <h2 class="h4 fw-bold mb-0">Daftar Pengajuan</h2>
            <input id="photo-request-search" type="text" class="form-control" style="max-width: 340px;" placeholder="Cari nama, status, reviewer">
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="photo-request-table">
                <thead>
                <tr>
                    <th>Member</th>
                    <th>Foto Diajukan</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th>Direview</th>
                    <th class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requests as $item)
                    @php
                        $statusColor = match ($item->status) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'warning',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($item->member?->profile_photo_url)
                                    <img
                                        src="{{ $item->member->profile_photo_url }}"
                                        alt="Foto saat ini {{ $item->member->full_name }}"
                                        style="width:44px;height:44px;object-fit:cover;border-radius:999px;border:1px solid rgba(255,255,255,.18);">
                                @else
                                    <div class="d-inline-flex align-items-center justify-content-center"
                                         style="width:44px;height:44px;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);font-weight:700;">
                                        {{ $item->member?->profile_initials ?? '--' }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $item->member?->full_name ?? 'Member tidak ditemukan' }}</div>
                                    <div class="small text-white-50">{{ $item->member?->phone ?: $item->member?->email ?: '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.profile-photo-requests.photo', $item) }}" target="_blank" class="d-inline-block position-relative" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                <img
                                    src="{{ route('admin.profile-photo-requests.photo', $item) }}"
                                    alt="Foto diajukan"
                                    style="width:50px;height:50px;object-fit:cover;border-radius:8px;border:1px solid rgba(255,255,255,.18);">
                            </a>
                        </td>
                        <td><span class="badge text-bg-{{ $statusColor }}">{{ strtoupper($item->status) }}</span></td>
                        <td>{{ $item->created_at?->format('d M Y H:i') }}</td>
                        <td>
                            @if($item->reviewed_at)
                                <div>{{ $item->reviewed_at->format('d M Y H:i') }}</div>
                                <div class="small text-white-50">{{ $item->reviewed_by }}</div>
                            @else
                                <span class="text-secondary">Belum direview</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($item->status === 'pending')
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <form method="POST" action="{{ route('admin.profile-photo-requests.approve', $item) }}" onsubmit="return confirm('Setujui foto profil baru member ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.profile-photo-requests.reject', $item) }}" onsubmit="return confirm('Tolak permintaan ganti foto ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">Tolak</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-secondary small">Sudah diproses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-secondary">Belum ada pengajuan ganti foto profil.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $requests->links() }}</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('photo-request-search');
            const rows = document.querySelectorAll('#photo-request-table tbody tr');

            searchInput?.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();

                rows.forEach((row) => {
                    const text = (row.textContent || '').toLowerCase();
                    row.style.display = query === '' || text.includes(query) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
