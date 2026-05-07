@extends('admin.layout')

@section('content')
    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Daily Non Member</div>
        <h1 class="display-6 fw-bold mt-2 mb-2">Pembayaran daily non member</h1>
        <p class="muted-copy mb-0">Halaman ini hanya menampilkan riwayat pembayaran non member 24 jam terakhir. Data yang lebih lama tetap tersedia di halaman transaksi.</p>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Transaksi daily pass</h2>
            <button
                class="btn btn-dark rounded-pill px-4"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#dailyPaymentFormCollapse"
                aria-expanded="{{ $errors->any() ? 'true' : 'false' }}"
                aria-controls="dailyPaymentFormCollapse">
                Catat daily pass
            </button>
        </div>

        <div class="collapse @if($errors->any()) show @endif mb-4" id="dailyPaymentFormCollapse">
            <div class="list-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h5 fw-bold mb-0">Form daily non member</h3>
                    <span class="badge text-bg-light border text-dark">Isi data lalu simpan</span>
                </div>

                <form method="POST" action="{{ route('cashier.daily-payments.store') }}" class="row g-3">
                    @csrf

                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold">Nama pengunjung</label>
                        <input name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="Nama pengunjung" required>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold">Jenis transaksi</label>
                        <input class="form-control" value="Daily Pass" readonly>
                        <input type="hidden" name="transaction_type" value="Daily Pass">
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold">Nominal pembayaran</label>
                        <input name="amount" type="number" min="1" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="Nominal pembayaran" required>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold">Metode pembayaran</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="radio" class="btn-check" name="payment_method" id="daily_payment_cash" value="cash" @checked(old('payment_method', 'cash') === 'cash') required>
                            <label class="btn btn-outline-light rounded-pill px-4" for="daily_payment_cash">Cash</label>

                            <input type="radio" class="btn-check" name="payment_method" id="daily_payment_qris" value="qris" @checked(old('payment_method') === 'qris') required>
                            <label class="btn btn-outline-light rounded-pill px-4" for="daily_payment_qris">QRIS</label>
                        </div>
                        @error('payment_method')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan transaksi</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Catatan transaksi">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">Reset</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan transaksi</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Pengunjung</th>
                        <th>Tipe</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dailyPayments as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->customer_name }}</td>
                            <td>{{ $item->transaction_type }}</td>
                            <td>Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($item->payment_method) }}</td>
                            <td><span class="badge text-bg-light border text-dark">{{ $item->invoice }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary">Belum ada transaksi daily pass yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
