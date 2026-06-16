@extends('admin.layout')

@section('content')
    <style>
        .qris-action-list {
            display: grid;
            gap: .9rem;
        }

        .qris-action-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: 1rem;
            background: rgba(255,255,255,.035);
        }

        .qris-action-title {
            color: var(--text-main);
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .qris-action-meta {
            margin-top: .25rem;
            color: var(--text-muted);
            font-size: .9rem;
        }

        .qris-action-side {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .qris-print-btn {
            border-color: rgba(34,197,94,.4);
            background: rgba(34,197,94,.16);
            color: #d8ffe9;
            font-weight: 800;
        }

        .qris-print-btn:hover,
        .qris-print-btn:focus {
            border-color: rgba(34,197,94,.65);
            background: rgba(34,197,94,.28);
            color: #fff;
        }

        @media (max-width: 767.98px) {
            .qris-action-card {
                grid-template-columns: 1fr;
            }

            .qris-action-side {
                justify-content: stretch;
            }

            .qris-action-side .btn,
            .qris-action-side form {
                width: 100%;
            }

            .qris-action-side form .btn {
                width: 100%;
            }
        }
    </style>

    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Verifikasi QRIS</div>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="display-6 fw-bold mt-2 mb-1">Verifikasi QRIS</h1>
                <p class="muted-copy mb-0">Pembayaran cash langsung cetak struk. Halaman ini hanya untuk mengecek QRIS sebelum struk dicetak.</p>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3">
            {{ session('status') }}
        </div>
    @endif

    <div class="panel-card p-4 mb-4">
        <form method="GET" action="{{ route('cashier.receipts') }}" class="row g-3 align-items-end">
            <div class="col-12 col-lg-8">
                <label class="form-label fw-semibold">Cari</label>
                <input type="text" name="q" class="form-control" value="{{ $receiptSearch ?? '' }}" placeholder="Nama pelanggan">
            </div>
            <div class="col-12 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark rounded-pill px-4 w-100">Cari</button>
                <a href="{{ route('cashier.receipts') }}" class="btn btn-outline-secondary rounded-pill px-4 w-100">Reset</a>
            </div>
        </form>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
            <h2 class="h4 fw-bold mb-0">QRIS dari Kasir</h2>
            <span class="small muted-copy">Verifikasi pembayaran QRIS, lalu cetak struk.</span>
        </div>
        <div class="qris-action-list">
            @forelse ($receiptQueue as $item)
                <div class="qris-action-card">
                    <div>
                        <div class="qris-action-title">{{ $item->invoice }}</div>
                        <div class="qris-action-meta">
                            {{ $item->customer_name }} - {{ $item->transaction_type }} - {{ strtoupper($item->payment_method) }}
                            <br>Total Rp{{ number_format($item->amount, 0, ',', '.') }} - Diterima Rp{{ number_format($item->paid_amount ?? $item->amount, 0, ',', '.') }} - Kembali Rp{{ number_format($item->change_amount ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="qris-action-side">
                        <span class="badge text-bg-light border text-dark">
                            {{ $item->receipt_status === 'printed' ? 'Sudah Dicetak' : ($item->receipt_status === 'ready' ? 'Siap Cetak' : 'Menunggu Verifikasi') }}
                        </span>
                        @if ($item->payment_status === 'verified')
                            <a href="{{ route('cashier.receipts.print', $item->invoice) }}" class="btn qris-print-btn btn-sm rounded-pill px-3" target="_blank">
                                Cetak Struk
                            </a>
                        @else
                            <form method="POST" action="{{ route('cashier.verifications.confirm', $item->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-dark btn-sm rounded-pill px-3" type="submit">Verifikasi QRIS</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-secondary">Belum ada pembayaran QRIS yang perlu diproses{{ ($receiptSearch ?? '') !== '' ? ' dengan nama tersebut' : '' }}.</div>
            @endforelse
        </div>

        @if(($receiptQueue ?? null) && $receiptQueue->hasPages())
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">
                <div class="small muted-copy">
                    Menampilkan {{ $receiptQueue->firstItem() }}-{{ $receiptQueue->lastItem() }} dari {{ $receiptQueue->total() }} transaksi
                </div>
                <div class="d-flex gap-2">
                    @if($receiptQueue->onFirstPage())
                        <button class="btn btn-outline-secondary rounded-pill px-4" type="button" disabled>Previous</button>
                    @else
                        <a class="btn btn-outline-light rounded-pill px-4" href="{{ $receiptQueue->previousPageUrl() }}">Previous</a>
                    @endif

                    @if($receiptQueue->hasMorePages())
                        <a class="btn btn-dark rounded-pill px-4" href="{{ $receiptQueue->nextPageUrl() }}">Next</a>
                    @else
                        <button class="btn btn-outline-secondary rounded-pill px-4" type="button" disabled>Next</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
