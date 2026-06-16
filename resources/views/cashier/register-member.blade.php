@extends('admin.layout')

@section('content')
    <style>
        .register-member-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
        }
        .register-member-change {
            margin-top: .45rem;
            color: rgba(255, 255, 255, .75);
            font-size: .85rem;
        }
        @media (max-width: 767.98px) {
            .register-member-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Transaksi Member Baru</div>
        <h1 class="display-6 fw-bold mt-2 mb-0">Pendaftaran Member Baru</h1>
    </div>

    @if (session('member_credentials'))
        @php($memberCredentials = session('member_credentials'))
        <div class="panel-card p-4 mb-4 border border-success border-opacity-25">
            <div class="section-label text-success">Kredensial Member</div>
            <h2 class="h4 fw-bold mt-2 mb-3">Berikan data login ini ke member</h2>
            <div class="register-member-grid">
                <div>
                    <div class="small text-secondary mb-1">Nama</div>
                    <div class="fw-semibold">{{ $memberCredentials['name'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="small text-secondary mb-1">Username</div>
                    <div class="fw-semibold">{{ $memberCredentials['username'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="small text-secondary mb-1">Email</div>
                    <div class="fw-semibold">{{ $memberCredentials['email'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="small text-secondary mb-1">Password Awal</div>
                    <div class="fw-semibold">{{ $memberCredentials['password'] ?? '-' }}</div>
                </div>
            </div>
            <div class="small text-secondary mt-3">
                Jika member ingin mengganti password, arahkan ke menu <strong>Lupa Password</strong> di halaman login member.
            </div>
        </div>
    @endif

    <div class="panel-card p-4">
        <form method="POST" action="{{ route('cashier.transactions.register-member') }}">
            @csrf
            <div class="register-member-grid mb-3">
                <div>
                    <label class="form-label fw-semibold" for="reg_full_name">Nama Lengkap</label>
                    <input id="reg_full_name" name="full_name" type="text" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label fw-semibold" for="reg_phone">No. HP</label>
                    <input id="reg_phone" name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label fw-semibold" for="reg_email">Email Login Member</label>
                    <input id="reg_email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@email.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label fw-semibold" for="reg_username">Username Member</label>
                    <input id="reg_username" name="username" type="text" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="contoh: budiarena" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label fw-semibold" for="reg_password">Password Awal</label>
                    <input id="reg_password" name="password" type="text" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" placeholder="Minimal 8 karakter" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label fw-semibold" for="reg_duration">Durasi Membership</label>
                    <select id="reg_duration" name="duration" class="form-select @error('duration') is-invalid @enderror" required>
                        @foreach ([1,3,6,12] as $month)
                            <option value="{{ $month }}" @selected(old('duration', 1) == $month)>{{ $month }} Bulan</option>
                        @endforeach
                    </select>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label fw-semibold">Metode Bayar</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="payment_method" id="reg_payment_cash" value="cash" @checked(old('payment_method', 'cash') === 'cash') required>
                        <label class="btn btn-outline-light" for="reg_payment_cash">Tunai</label>
                        <input type="radio" class="btn-check" name="payment_method" id="reg_payment_qris" value="qris" @checked(old('payment_method') === 'qris') required>
                        <label class="btn btn-outline-light" for="reg_payment_qris">QRIS</label>
                    </div>
                    @error('payment_method')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label fw-semibold" for="reg_payment_amount">Nominal (Opsional)</label>
                    <input id="reg_payment_amount" name="payment_amount" type="number" min="1" class="form-control @error('payment_amount') is-invalid @enderror" value="{{ old('payment_amount') }}" placeholder="Default: 90.000 x durasi">
                    @error('payment_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="register-member-grid mb-3" data-register-cash-wrap>
                <div>
                    <label class="form-label fw-semibold" for="reg_paid_amount">Uang Diterima</label>
                    <input id="reg_paid_amount" name="paid_amount" type="number" min="0" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount') }}" placeholder="Contoh: 100000">
                    <div class="register-member-change">Kembalian: <strong data-register-change>Rp0</strong></div>
                    @error('paid_amount')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex align-items-end">
                    <div class="small text-secondary">Nominal transaksi: <strong data-register-total>Rp0</strong></div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="reg_notes">Catatan (Opsional)</label>
                <textarea id="reg_notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Catatan tambahan">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="small text-secondary mb-4">
                Username dan password awal diberikan oleh kasir. Jika nanti ingin ganti password, member bisa memakai menu <strong>Lupa Password</strong> di halaman login.
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('cashier.transactions') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-danger rounded-pill px-4">Simpan Pendaftaran</button>
            </div>
        </form>
    </div>

    <div class="panel-card p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Riwayat Pendaftaran Member</h2>
            <a href="{{ route('cashier.transactions', ['type' => 'member_payment']) }}" class="btn btn-outline-light rounded-pill px-3">Lihat Semua</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Invoice</th>
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
                    @forelse ($registerMemberHistory as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->transaction_at?->format('H:i') ?? '-' }}</div>
                                <div class="small text-secondary">{{ $item->transaction_at?->format('d M Y') ?? '-' }}</div>
                            </td>
                            <td class="fw-semibold">{{ $item->invoice }}</td>
                            <td>{{ $item->customer_name }}</td>
                            <td>{{ $item->transaction_type }}</td>
                            <td>Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->paid_amount ?? $item->amount, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->change_amount ?? 0, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($item->payment_method ?? '-') }}</td>
                            <td>
                                <span class="badge text-bg-{{ $item->payment_status === 'verified' ? 'success' : 'warning' }}">
                                    {{ $item->payment_status === 'verified' ? 'Lunas' : 'Pending' }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if ($item->payment_status === 'verified')
                                    <a href="{{ route('cashier.receipts.print', $item->invoice) }}" class="btn btn-sm btn-outline-light rounded-pill px-3" target="_blank">Cetak</a>
                                @else
                                    <span class="badge text-bg-warning">Verifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-secondary">Belum ada riwayat pendaftaran member.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (() => {
            const durationInput = document.getElementById('reg_duration');
            const amountInput = document.getElementById('reg_payment_amount');
            const paidInput = document.getElementById('reg_paid_amount');
            const methodInputs = document.querySelectorAll('input[name="payment_method"]');
            const cashWrap = document.querySelector('[data-register-cash-wrap]');
            const totalText = document.querySelector('[data-register-total]');
            const changeText = document.querySelector('[data-register-change]');
            const PRICE_PER_MONTH = 90000;

            const formatCurrency = (value) => `Rp${(Math.max(value, 0) || 0).toLocaleString('id-ID')}`;
            const isCash = () => (document.querySelector('input[name="payment_method"]:checked')?.value || 'cash') === 'cash';
            const getTotal = () => {
                const custom = Number(amountInput?.value || 0);
                if (custom > 0) return custom;
                return (Number(durationInput?.value || 1) || 1) * PRICE_PER_MONTH;
            };

            const syncRegisterPayment = () => {
                const total = getTotal();
                const paid = Number(paidInput?.value || 0);
                const cash = isCash();
                if (cashWrap) cashWrap.style.display = cash ? '' : 'none';
                if (paidInput) paidInput.required = cash;
                if (totalText) totalText.textContent = formatCurrency(total);
                if (changeText) changeText.textContent = formatCurrency(cash ? (paid - total) : 0);
            };

            durationInput?.addEventListener('change', syncRegisterPayment);
            amountInput?.addEventListener('input', syncRegisterPayment);
            paidInput?.addEventListener('input', syncRegisterPayment);
            methodInputs.forEach((input) => input.addEventListener('change', syncRegisterPayment));
            syncRegisterPayment();
        })();
    </script>
@endsection
