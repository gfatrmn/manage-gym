@extends('admin.layout')

@section('content')
<div class="topbar-card p-4 mb-4">
    <div class="section-label">Admin</div>
    <h1 class="display-6 fw-bold mt-2 mb-0">Manajemen Akun Kasir</h1>
</div>

<div class="panel-card p-4 mb-4">
    <h2 class="h5 fw-bold mb-3">Tambah Akun Kasir</h2>
    <form method="POST" action="{{ route('admin.cashier-accounts.store') }}" class="row g-3">
        @csrf
        <div class="col-md-4"><input name="name" class="form-control" placeholder="Nama" required></div>
        <div class="col-md-4"><input name="login" class="form-control" placeholder="Username login" required></div>
        <div class="col-md-4"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
        <div class="col-12 d-flex justify-content-end"><button class="btn btn-danger rounded-pill px-4">Simpan</button></div>
    </form>
</div>

<div class="panel-card p-4">
    <h2 class="h5 fw-bold mb-3">Daftar Akun Kasir</h2>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Nama</th><th>Username</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse ($cashiers as $cashier)
                <tr>
                    <td>{{ $cashier->name }}</td>
                    <td>{{ $cashier->login }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.cashier-accounts.destroy', $cashier) }}" onsubmit="return confirm('Hapus akun kasir ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-secondary py-4">Belum ada akun kasir.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

