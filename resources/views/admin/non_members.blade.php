@extends('admin.layout')

@section('content')
    <style>
        .dashboard-page { padding: 1rem 2rem; }
        .dashboard-heading { position: relative; padding-bottom: 1.2rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,.06); }
        .dashboard-subtitle { font-size: .75rem; letter-spacing: .18em; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: .7rem; font-weight: 500; }
        .dashboard-title { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 700; margin: 0; color: #fff; }

        .panel-card { background: rgba(255,255,255,.025); border: 1px solid rgba(255,255,255,.05); border-radius: 1.2rem; padding: 1.5rem; }

        /* Memaksa teks tabel menjadi putih */
        .guest-table tbody td { color: #ffffff !important; vertical-align: middle; padding: 1rem; border-bottom: 1px solid rgba(255,255,255,.04); }
        .guest-table thead th { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; padding: 1rem; border-bottom: 1px solid rgba(255,255,255,.06); }

        .btn-action-danger { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(239, 68, 68, 0.2); color: #f87171; border: none; transition: 0.2s; }
        .btn-action-danger:hover { background: #ef4444; color: white; }
    </style>

    <div class="dashboard-page">
        <div class="dashboard-heading">
            <div class="dashboard-subtitle">ARENA GYM · VISITORS</div>
            <h1 class="dashboard-title">Riwayat Tamu Harian</h1>
        </div>

        @if(session('status'))
            <div class="alert alert-success border-0 bg-success text-white rounded-3 mb-4 py-2 small shadow-sm">
                <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
            </div>
        @endif

        <div class="panel-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-white fw-bold mb-0">Data Non-Member</h5>
                <form action="{{ route('admin.non-members') }}" method="GET" style="max-width: 300px;">
                    <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control form-control-sm bg-dark border-secondary text-white rounded-pill px-3" placeholder="Cari nama tamu...">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 guest-table">
                    <thead>
                        <tr>
                            <th>Nama Tamu</th>
                            <th>Telepon</th>
                            <th>Metode Bayar</th>
                            <th>Biaya</th>
                            <th>Waktu Kunjungan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- PERBAIKAN: Menggunakan $guests sesuai kiriman dari Route --}}
                        @forelse ($guests as $guest)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white">{{ $guest->full_name }}</div>
                                </td>
                                <td>{{ $guest->phone ?: '-' }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-20 text-primary rounded-pill" style="font-size: 10px;">
                                        {{ strtoupper($guest->payment_method ?? 'CASH') }}
                                    </span>
                                </td>
                                <td class="fw-bold text-white">
                                    Rp{{ number_format($guest->payment_amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    {{-- PERBAIKAN: Menggunakan visit_at sesuai tabel daily_guests --}}
                                    <div class="small">{{ $guest->visit_at ? $guest->visit_at->format('d M Y') : '-' }}</div>
                                    <div class="small text-muted" style="font-size: 11px;">Jam: {{ $guest->visit_at ? $guest->visit_at->format('H:i') : '-' }}</div>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.non-members.destroy', $guest) }}" method="POST" onsubmit="return confirm('Hapus riwayat kunjungan {{ $guest->full_name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">Belum ada data kunjungan tamu harian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $guests->links() }}
            </div>
        </div>
    </div>
@endsection
