@extends('admin.layout')

@section('content')
    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Pembayaran Member</div>
        <h1 class="display-6 fw-bold mt-2 mb-0">Pembayaran Member</h1>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Riwayat</h2>
            <button
                class="btn btn-dark rounded-pill px-4"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#memberPaymentFormCollapse"
                aria-expanded="{{ $errors->any() ? 'true' : 'false' }}"
                aria-controls="memberPaymentFormCollapse">
                Tambah
            </button>
        </div>

        <div class="collapse @if($errors->any() || request()->has('members_page') || request()->filled('member_q')) show @endif mb-4" id="memberPaymentFormCollapse">
            <div class="list-card p-4">
                <h3 class="h5 fw-bold mb-3">Pembayaran / Aktivasi Member</h3>

                <form method="GET" action="{{ route('cashier.member-payments') }}" class="mb-4">
                    <label class="form-label fw-semibold">Member atau daily pass</label>
                    <div class="d-flex gap-2">
                        <input type="text" id="member-search" name="member_q" value="{{ $memberSearch ?? '' }}" class="form-control" placeholder="Cari nama, email, kode, atau nomor HP">
                        <button type="submit" class="btn btn-dark rounded-pill px-4">Cari</button>
                        @if (! empty($memberSearch))
                            <a href="{{ route('cashier.member-payments') }}" class="btn btn-outline-secondary rounded-pill px-4">Reset</a>
                        @endif
                    </div>
                </form>

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
                                        <th>Status</th>
                                        <th>Expire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($members as $member)
                                        <tr data-name="{{ strtolower($member->full_name) }}" data-phone="{{ preg_replace('/\D+/', '', (string) $member->phone) }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input member-select-radio" type="radio" name="gym_member_id" id="member-{{ $member->id }}" value="{{ $member->id }}" @checked((string) old('gym_member_id') === (string) $member->id)>
                                                </div>
                                            </td>
                                            <td class="fw-semibold">{{ $member->full_name }}</td>
                                            <td>{{ $member->phone ?: '-' }}</td>
                                            <td>
                                                <span class="badge text-bg-{{ $member->status === 'member' ? 'success' : 'secondary' }}">
                                                    {{ $member->status === 'member' ? 'Member' : 'Daily Pass' }}
                                                </span>
                                            </td>
                                            <td>{{ optional($member->expires_at)->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-secondary">Member tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($members->count() > 0)
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                                <div class="text-muted small">
                                    Menampilkan {{ $members->firstItem() ?: 0 }} - {{ $members->lastItem() ?: 0 }} dari {{ $members->total() }} member
                                </div>
                                <nav>
                                    {{ $members->links('pagination::bootstrap-5') }}
                                </nav>
                            </div>
                        @endif
                        @error('gym_member_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold">Nominal</label>
                        <input name="amount_display" type="text" class="form-control" value="Rp90.000" readonly>
                        <input name="amount" type="hidden" value="90000">
                        <div class="form-text text-white-50">Biaya membership 1 bulan sudah pakem.</div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold">Metode</label>
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
                        <label class="form-label fw-semibold">Uang diterima</label>
                        <input name="paid_amount" type="number" min="0" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', 90000) }}" data-cash-paid data-cash-total="90000" required>
                        <div class="form-text text-white-50">Kembalian: <strong data-cash-change>Rp0</strong></div>
                        @error('paid_amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Opsional">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">Reset</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3">
            <label for="member-payment-history-search" class="form-label fw-semibold">Cari riwayat pembayaran</label>
            <input id="member-payment-history-search" type="text" class="form-control" placeholder="Cari invoice, nama, paket, atau metode">
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="member-payment-history-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Paket</th>
                        <th>Nominal</th>
                        <th>Diterima</th>
                        <th>Kembali</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
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
                            <td>Rp{{ number_format($item->paid_amount ?? $item->amount, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->change_amount ?? 0, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($item->payment_method) }}</td>
                            <td><span class="badge text-bg-{{ $item->payment_status === 'verified' ? 'success' : 'warning' }}">{{ $item->payment_status === 'verified' ? 'Terverifikasi' : 'Pending' }}</span></td>
                            <td class="text-end">
                                @if ($item->payment_status === 'verified')
                                    <a href="{{ route('cashier.receipts.print', $item->invoice) }}" class="btn btn-sm btn-outline-light rounded-pill px-3" target="_blank">
                                        Cetak
                                    </a>
                                @else
                                    <span class="badge text-bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-secondary">Belum ada pembayaran member yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($memberPayments->count() > 0)
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan {{ $memberPayments->firstItem() ?: 0 }} - {{ $memberPayments->lastItem() ?: 0 }} dari {{ $memberPayments->total() }} transaksi
                </div>
                <nav>
                    {{ $memberPayments->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        @endif
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

            const formatCurrency = (value) => 'Rp' + new Intl.NumberFormat('id-ID').format(Math.max(Number(value) || 0, 0));
            const paidInput = document.querySelector('[data-cash-paid]');
            const changeText = document.querySelector('[data-cash-change]');
            const methodInputs = document.querySelectorAll('input[name="payment_method"]');

            const syncChange = () => {
                if (!paidInput || !changeText) {
                    return;
                }

                const total = Number(paidInput.dataset.cashTotal || 0);
                const method = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';

                if (method === 'qris') {
                    paidInput.value = total;
                    paidInput.readOnly = true;
                } else {
                    paidInput.readOnly = false;
                }

                changeText.textContent = formatCurrency((Number(paidInput.value) || 0) - total);
            };

            paidInput?.addEventListener('input', syncChange);
            methodInputs.forEach((input) => input.addEventListener('change', syncChange));
            syncChange();

            const historySearchInput = document.getElementById('member-payment-history-search');
            const historyRows = document.querySelectorAll('#member-payment-history-table tbody tr');

            if (historySearchInput) {
                historySearchInput.addEventListener('input', function () {
                    const query = normalize(this.value.trim());

                    historyRows.forEach((row) => {
                        const text = normalize(row.textContent || '');
                        row.style.display = query === '' || text.includes(query) ? '' : 'none';
                    });
                });
            }
        });
    </script>
@endsection
