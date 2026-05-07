@extends('admin.layout')

@section('content')
    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Verifikasi & Bukti Pembayaran</div>
        <h1 class="display-6 fw-bold mt-2 mb-2">Bukti pembayaran terverifikasi</h1>
        <p class="muted-copy mb-0">Pembayaran cash langsung siap cetak. Pembayaran non-cash diverifikasi di halaman ini sebelum bukti pembayaran bisa dicetak.</p>
    </div>

    <div class="panel-card p-4 mb-4">
        <form method="GET" action="{{ route('cashier.receipts') }}" class="row g-3 align-items-end">
            <div class="col-12 col-lg-8">
                <label class="form-label fw-semibold">Cari nama pelanggan</label>
                <input type="text" name="q" class="form-control" value="{{ $receiptSearch ?? '' }}" placeholder="Cari berdasarkan nama untuk verifikasi atau cetak bukti">
            </div>
            <div class="col-12 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark rounded-pill px-4 w-100">Cari</button>
                <a href="{{ route('cashier.receipts') }}" class="btn btn-outline-secondary rounded-pill px-4 w-100">Reset</a>
            </div>
        </form>
    </div>

    <div class="panel-card p-4">
        <div class="d-grid gap-3">
            @forelse ($receiptQueue as $item)
                <div class="list-card p-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-semibold">{{ $item->invoice }}</div>
                            <div class="small muted-copy">{{ $item->customer_name }} - {{ $item->transaction_type }} - {{ $item->payment_method === 'later' ? 'Bayar Nanti' : strtoupper($item->payment_method) }}</div>
                        </div>
                        <span class="badge text-bg-light border text-dark">
                            {{ $item->receipt_status === 'printed' ? 'Sudah Dicetak' : ($item->receipt_status === 'ready' ? 'Siap Cetak' : 'Menunggu Verifikasi') }}
                        </span>
                    </div>
                    <div class="mt-3">
                        @if ($item->payment_status === 'verified')
                            <a href="{{ route('cashier.receipts.print', $item->invoice) }}" class="btn btn-outline-secondary btn-sm rounded-pill" target="_blank">Cetak bukti</a>
                        @else
                            <form method="POST" action="{{ route('cashier.verifications.confirm', $item->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-dark btn-sm rounded-pill" type="submit">Verifikasi pembayaran</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-secondary">Belum ada transaksi yang cocok untuk verifikasi atau cetak bukti{{ ($receiptSearch ?? '') !== '' ? ' dengan nama tersebut' : '' }}.</div>
            @endforelse
        </div>
    </div>
@endsection
