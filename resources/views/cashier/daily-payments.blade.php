@extends('admin.layout')

@section('content')
    <style>
        .daily-pass-page {
            display: grid;
            gap: 1.25rem;
        }

        .daily-pass-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            overflow: hidden;
            position: relative;
        }

        .daily-pass-hero::after {
            content: '';
            position: absolute;
            inset: auto -8rem -10rem auto;
            width: 20rem;
            height: 20rem;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(255, 59, 59, 0.18), transparent 68%);
            pointer-events: none;
        }

        .daily-pass-actions {
            position: relative;
            z-index: 1;
        }

        .daily-pass-form-modal .modal-content {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.25rem;
            background: linear-gradient(180deg, rgba(24, 24, 27, 0.98), rgba(10, 10, 10, 0.98));
            color: var(--text-main);
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.5);
        }

        .daily-pass-modal-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 18rem;
            gap: 1rem;
        }

        .daily-pass-fieldset,
        .daily-pass-summary {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.035);
        }

        .daily-pass-fieldset {
            padding: 1rem;
        }

        .daily-pass-summary {
            padding: 1rem;
            display: grid;
            align-content: start;
            gap: .8rem;
        }

        .daily-pass-summary-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.68);
            font-size: .85rem;
        }

        .daily-pass-summary-row strong {
            color: #fff;
            white-space: nowrap;
        }

        .daily-pass-change-card {
            border-radius: .9rem;
            padding: 1rem;
            background: rgba(255, 59, 59, 0.12);
            border: 1px solid rgba(255, 59, 59, 0.2);
        }

        .daily-pass-change-card span {
            display: block;
            color: rgba(255, 255, 255, 0.62);
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: .35rem;
        }

        .daily-pass-change-card strong {
            display: block;
            color: #fff;
            font-size: 1.55rem;
            line-height: 1.1;
        }

        .daily-pass-methods {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
            padding: .35rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .daily-pass-methods .btn {
            border-radius: 999px;
            border: 0;
            min-height: 2.5rem;
            color: rgba(255, 255, 255, 0.76);
        }

        .daily-pass-methods .btn-check:checked + .btn {
            background: #fff;
            color: #111;
        }

        .daily-pass-history-tools {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: .75rem;
            align-items: end;
        }

        @media (max-width: 991.98px) {
            .daily-pass-modal-grid,
            .daily-pass-history-tools {
                grid-template-columns: 1fr;
            }

            .daily-pass-hero {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <div class="daily-pass-page">
    <div class="topbar-card p-4 mb-4">
        <div class="daily-pass-hero">
            <div>
                <div class="section-label">Daily Pass</div>
                <h1 class="display-6 fw-bold mt-2 mb-0">Daily Pass</h1>
            </div>
            <div class="daily-pass-actions">
                <button
                    class="btn btn-dark rounded-pill px-4"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#dailyPaymentModal">
                    Tambah Daily Pass
                </button>
            </div>
        </div>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold mb-0">Riwayat</h2>
        </div>

        <div class="daily-pass-history-tools mb-3">
            <div>
                <label for="daily-payment-history-search" class="form-label fw-semibold">Cari riwayat transaksi</label>
                <input id="daily-payment-history-search" type="text" class="form-control" placeholder="Cari nama, tipe, metode, atau invoice">
            </div>
            <button
                class="btn btn-dark rounded-pill px-4"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#dailyPaymentModal">
                Tambah
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0" id="daily-payment-history-table">
                <thead>
                    <tr>
                        <th>Pengunjung</th>
                        <th>Tipe</th>
                        <th>Nominal</th>
                        <th>Diterima</th>
                        <th>Kembali</th>
                        <th>Metode</th>
                        <th>Bukti</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dailyPayments as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->customer_name }}</td>
                            <td>{{ $item->transaction_type }}</td>
                            <td>Rp{{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->paid_amount ?? $item->amount, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->change_amount ?? 0, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($item->payment_method) }}</td>
                            <td><span class="badge text-bg-light border text-dark">{{ $item->invoice }}</span></td>
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
                            <td colspan="8" class="text-center py-4 text-secondary">Belum ada transaksi daily pass yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <div class="modal fade daily-pass-form-modal" id="dailyPaymentModal" tabindex="-1" aria-labelledby="dailyPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('cashier.daily-payments.store') }}">
                    @csrf
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <div>
                            <div class="section-label">Transaksi Baru</div>
                            <h3 class="modal-title h4 fw-bold mt-2 mb-0" id="dailyPaymentModalLabel">Daily Pass Baru</h3>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="daily-pass-modal-grid">
                            <div class="daily-pass-fieldset">
                                <div class="row g-3">
                                    <div class="col-12 col-lg-7">
                                        <label class="form-label fw-semibold">Nama Pengunjung</label>
                                        <input name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="Nama pengunjung" required>
                                        @error('customer_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-lg-5">
                                        <label class="form-label fw-semibold">Tipe</label>
                                        <input class="form-control" value="Daily Pass" readonly>
                                        <input type="hidden" name="transaction_type" value="Daily Pass">
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fw-semibold">Nominal</label>
                                        <input name="amount" type="number" min="1" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="100000" required data-payment-total>
                                        @error('amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label class="form-label fw-semibold">Uang Diterima</label>
                                        <input name="paid_amount" type="number" min="0" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount') }}" placeholder="200000" required data-cash-paid>
                                        @error('paid_amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Metode Pembayaran</label>
                                        <div class="daily-pass-methods">
                                            <input type="radio" class="btn-check" name="payment_method" id="daily_payment_cash" value="cash" @checked(old('payment_method', 'cash') === 'cash') required>
                                            <label class="btn fw-semibold" for="daily_payment_cash">Cash</label>

                                            <input type="radio" class="btn-check" name="payment_method" id="daily_payment_qris" value="qris" @checked(old('payment_method') === 'qris') required>
                                            <label class="btn fw-semibold" for="daily_payment_qris">QRIS</label>
                                        </div>
                                        @error('payment_method')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Catatan</label>
                                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="Opsional">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <aside class="daily-pass-summary">
                                <div class="section-label">Ringkasan</div>
                                <div class="daily-pass-summary-row">
                                    <span>Nominal</span>
                                    <strong data-summary-total>Rp0</strong>
                                </div>
                                <div class="daily-pass-summary-row">
                                    <span>Diterima</span>
                                    <strong data-summary-paid>Rp0</strong>
                                </div>
                                <div class="daily-pass-summary-row">
                                    <span>Metode</span>
                                    <strong data-summary-method>Cash</strong>
                                </div>
                                <div class="daily-pass-change-card">
                                    <span>Kembalian</span>
                                    <strong data-cash-change>Rp0</strong>
                                </div>
                            </aside>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">Reset</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('daily-payment-history-search');
            const rows = document.querySelectorAll('#daily-payment-history-table tbody tr');
            const normalize = (value) => value.toLowerCase();
            const formatCurrency = (value) => 'Rp' + new Intl.NumberFormat('id-ID').format(Math.max(Number(value) || 0, 0));
            const totalInput = document.querySelector('[data-payment-total]');
            const paidInput = document.querySelector('[data-cash-paid]');
            const changeText = document.querySelector('[data-cash-change]');
            const summaryTotal = document.querySelector('[data-summary-total]');
            const summaryPaid = document.querySelector('[data-summary-paid]');
            const summaryMethod = document.querySelector('[data-summary-method]');
            const methodInputs = document.querySelectorAll('input[name="payment_method"]');
            const modalElement = document.getElementById('dailyPaymentModal');

            const syncPayment = () => {
                if (!totalInput || !paidInput || !changeText) {
                    return;
                }

                const total = Number(totalInput.value || 0);
                const method = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';

                if (method === 'qris') {
                    paidInput.value = total || '';
                    paidInput.readOnly = true;
                } else {
                    paidInput.readOnly = false;
                }

                const paid = Number(paidInput.value) || 0;
                changeText.textContent = formatCurrency(paid - total);
                if (summaryTotal) summaryTotal.textContent = formatCurrency(total);
                if (summaryPaid) summaryPaid.textContent = formatCurrency(paid);
                if (summaryMethod) summaryMethod.textContent = method.toUpperCase();
            };

            totalInput?.addEventListener('input', syncPayment);
            paidInput?.addEventListener('input', syncPayment);
            methodInputs.forEach((input) => input.addEventListener('change', syncPayment));
            document.querySelector('#dailyPaymentModal form')?.addEventListener('reset', () => {
                window.setTimeout(syncPayment, 0);
            });
            syncPayment();

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = normalize(this.value.trim());
                    rows.forEach((row) => {
                        const text = normalize(row.textContent || '');
                        row.style.display = query === '' || text.includes(query) ? '' : 'none';
                    });
                });
            }

            @if ($errors->any())
                if (modalElement && window.bootstrap) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            @endif
        });
    </script>
@endsection
