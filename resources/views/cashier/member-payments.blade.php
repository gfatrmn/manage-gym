@extends('admin.layout')

@section('content')
    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Pembayaran Member</div>
        <h1 class="display-6 fw-bold mt-2 mb-2">Pencatatan pembayaran member</h1>
        <p class="muted-copy mb-0">Halaman ini hanya menampilkan riwayat pembayaran member 24 jam terakhir. Data yang lebih lama tetap tersedia di halaman transaksi.</p>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Daftar pembayaran member</h2>
            <button
                class="btn btn-dark rounded-pill px-4"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#memberPaymentFormCollapse"
                aria-expanded="{{ $errors->any() ? 'true' : 'false' }}"
                aria-controls="memberPaymentFormCollapse">
                Catat pembayaran member
            </button>
        </div>

        <div class="collapse @if($errors->any()) show @endif mb-4" id="memberPaymentFormCollapse">
            <div class="list-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="h5 fw-bold mb-0">Form pembayaran member</h3>
                        <div class="small text-secondary">Pilih member aktif dengan checklist lalu isi nominal dan metode.</div>
                    </div>
                    <span class="badge text-bg-light border text-dark">Isi data lalu simpan</span>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Cari member</label>
                    <input type="text" id="member-search" class="form-control" placeholder="Cari nama member">
                </div>

                <form method="POST" action="{{ route('cashier.member-payments.store') }}" class="row g-3" id="memberPaymentForm">
                    @csrf

                    <div class="col-12">
                        <div class="table-responsive mb-3">
                            <table class="table align-middle mb-0" id="member-select-table">
                                <thead>
                                    <tr>
                                        <th>Pilih</th>
                                        <th>Nama</th>
                                        <th>HP</th>
                                        <th>Expire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($members as $member)
                                        <tr data-name="{{ strtolower($member->full_name) }}" data-phone="{{ preg_replace('/\D+/', '', (string) $member->phone) }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input member-select-radio" type="radio" name="gym_member_id" id="member-{{ $member->id }}" value="{{ $member->id }}" @checked((string) old('gym_member_id') === (string) $member->id)>
                                                </div>
                                            </td>
                                            <td>{{ $member->full_name }}</td>
                                            <td>{{ $member->phone ?: '-' }}</td>
                                            <td>{{ optional($member->expires_at)->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('gym_member_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold">Nominal pembayaran</label>
                        <input name="amount" type="number" min="1" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="Nominal pembayaran" required>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold">Metode pembayaran</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="radio" class="btn-check" name="payment_method" id="member_payment_cash" value="cash" @checked(old('payment_method', 'cash') === 'cash') required>
                            <label class="btn btn-outline-light rounded-pill px-4" for="member_payment_cash">Cash</label>

                            <input type="radio" class="btn-check" name="payment_method" id="member_payment_qris" value="qris" @checked(old('payment_method') === 'qris') required>
                            <label class="btn btn-outline-light rounded-pill px-4" for="member_payment_qris">QRIS</label>
                        </div>
                        @error('payment_method')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Catatan pembayaran">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">Reset</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan pembayaran</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Paket</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($memberPayments as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->invoice }}</td>
                            <td>
                                @if ($item->member?->profile_photo_url)
                                    <img src="{{ $item->member->profile_photo_url }}" alt="Foto {{ $item->customer_name }}" class="table-avatar">
                                @elseif ($item->member)
                                    <span class="table-avatar-placeholder">{{ $item->member->profile_initials }}</span>
                                @else
                                    <span class="table-avatar-placeholder">-</span>
                                @endif
                            </td>
                            <td>{{ $item->customer_name }}</td>
                            <td>{{ $item->transaction_type }}</td>
                            <td>Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($item->payment_method) }}</td>
                            <td><span class="badge text-bg-{{ $item->payment_status === 'verified' ? 'success' : 'warning' }}">{{ $item->payment_status === 'verified' ? 'Terverifikasi' : 'Pending' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">Belum ada pembayaran member yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('member-search');
            const rows = document.querySelectorAll('#member-select-table tbody tr');

            const normalize = (value) => value.toLowerCase();

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = normalize(this.value.trim());

                    rows.forEach((row) => {
                        const name = normalize(row.dataset.name || '');
                        const phone = normalize(row.dataset.phone || '');
                        const visible = query === '' || name.includes(query) || phone.includes(query);
                        row.style.display = visible ? '' : 'none';
                    });
                });
            }

            document.querySelectorAll('.member-select-radio').forEach((radio) => {
                radio.addEventListener('change', function () {
                    document.querySelectorAll('#member-select-table tbody tr').forEach((row) => {
                        row.classList.toggle('table-active', row.querySelector('input') === this);
                    });
                });
            });
        });
    </script>
@endsection
