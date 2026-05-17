@extends('admin.layout')

@section('content')
    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Verifikasi</div>
        <h1 class="display-6 fw-bold mt-2 mb-0">Verifikasi</h1>
    </div>

    <div class="panel-card dark-panel p-4">
        <div class="d-grid gap-3">
            @forelse ($transactions->where('payment_status', 'pending') as $item)
                <div class="rounded-4 p-3" style="background: rgba(255,255,255,.06);">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="fw-semibold">{{ $item->customer_name }}</div>
                            <div class="small muted-copy">{{ $item->transaction_type }} - Rp{{ number_format($item->amount, 0, ',', '.') }}</div>
                        </div>
                        <span class="badge text-bg-warning">Menunggu Verifikasi</span>
                    </div>
                    <div class="mt-3">
                        <form method="POST" action="{{ route('cashier.verifications.confirm', $item->id) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-light rounded-pill" type="submit">Verifikasi</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-white-50">Tidak ada pembayaran yang menunggu verifikasi.</div>
            @endforelse
        </div>
    </div>
@endsection
