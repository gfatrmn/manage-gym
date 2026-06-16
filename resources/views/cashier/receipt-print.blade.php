<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
        <style>
            :root {
                --ink: #0f172a;
                --muted: #64748b;
                --line: #e2e8f0;
                --soft: #f8fafc;
                --brand: #e11d2e;
            }

            * {
                box-sizing: border-box;
            }

            body {
                min-height: 100vh;
                margin: 0;
                padding: 32px;
                color: var(--ink);
                background:
                    radial-gradient(circle at top left, rgba(225, 29, 46, .08), transparent 26rem),
                    #f3f6fb;
                font-family: 'Inter', system-ui, sans-serif;
            }

            .receipt-shell {
                width: min(900px, 100%);
                margin: 0 auto;
            }

            .receipt {
                overflow: hidden;
                border: 1px solid var(--line);
                border-radius: 20px;
                background: #fff;
                box-shadow: 0 24px 70px rgba(15, 23, 42, .12);
            }

            .receipt-head {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 18px;
                align-items: start;
                padding: 28px;
                color: #fff;
                background:
                    linear-gradient(135deg, #101827, #1e293b 58%, #6f111d);
            }

            .brand {
                display: flex;
                gap: 14px;
                align-items: center;
            }

            .brand-logo {
                width: 56px;
                height: auto;
                object-fit: contain;
                border-radius: 14px;
                background: rgba(255,255,255,0.08);
                padding: 6px;
            }

            .brand-mark {
                width: 48px;
                height: 48px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 14px;
                background: linear-gradient(135deg, #ff4652, #b80f24);
                font-weight: 800;
                letter-spacing: .04em;
            }

            .brand-title {
                font-size: 12px;
                font-weight: 800;
                letter-spacing: .12em;
                text-transform: uppercase;
                opacity: .78;
            }

            h1 {
                margin: 4px 0 0;
                font-size: clamp(26px, 4vw, 38px);
                line-height: 1.05;
            }

            .receipt-status {
                min-width: 150px;
                padding: 10px 12px;
                border: 1px solid rgba(255,255,255,.18);
                border-radius: 14px;
                background: rgba(255,255,255,.08);
                text-align: right;
            }

            .label {
                color: var(--muted);
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .receipt-head .label {
                color: rgba(255,255,255,.72);
            }

            .value {
                margin-top: 5px;
                font-weight: 800;
                overflow-wrap: anywhere;
            }

            .receipt-body {
                padding: 24px 28px 28px;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(145px, 1fr));
                gap: 12px;
                margin-bottom: 22px;
            }

            .info-card {
                min-width: 0;
                min-height: 92px;
                padding: 14px;
                border: 1px solid var(--line);
                border-radius: 14px;
                background: var(--soft);
            }

            .info-card .value {
                line-height: 1.35;
                word-break: break-word;
                overflow-wrap: anywhere;
            }

            .items {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                overflow: hidden;
                border: 1px solid var(--line);
                border-radius: 14px;
                table-layout: fixed;
            }

            .items th,
            .items td {
                padding: 14px 12px;
                text-align: left;
                border-bottom: 1px solid var(--line);
                vertical-align: top;
            }

            .items th {
                color: var(--muted);
                background: #f1f5f9;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .1em;
                text-transform: uppercase;
            }

            .items tr:last-child td {
                border-bottom: 0;
            }

            .items .right {
                text-align: right;
                white-space: nowrap;
            }

            .items .item-col {
                width: 46%;
            }

            .items .qty-col {
                width: 12%;
            }

            .items .money-col {
                width: 21%;
            }

            .item-name {
                display: block;
                line-height: 1.35;
                overflow-wrap: anywhere;
            }

            .total-box {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(280px, 340px);
                gap: 20px;
                align-items: stretch;
                margin-top: 24px;
            }

            .note {
                color: var(--muted);
                font-size: 13px;
                line-height: 1.6;
            }

            .receipt-note {
                min-height: 100%;
                padding: 16px;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: #fff;
            }

            .total {
                display: grid;
                gap: 0;
                padding: 0;
                border-radius: 16px;
                color: #fff;
                background: linear-gradient(135deg, #111827, #1f2937);
                overflow: hidden;
                text-align: left;
            }

            .total-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                padding: 14px 16px;
                border-bottom: 1px solid rgba(255,255,255,.1);
            }

            .total-row:last-child {
                border-bottom: 0;
                background: rgba(255,255,255,.06);
            }

            .total-row span {
                color: rgba(255,255,255,.68);
                font-size: 12px;
                font-weight: 800;
                letter-spacing: .1em;
                text-transform: uppercase;
            }

            .total-row strong {
                font-size: 23px;
                line-height: 1.15;
                white-space: nowrap;
            }

            .actions {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
                margin-top: 18px;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                padding: 10px 16px;
                border: 1px solid #cbd5e1;
                border-radius: 999px;
                color: var(--ink);
                background: #fff;
                font-weight: 800;
                text-decoration: none;
                cursor: pointer;
            }

            .btn-dark {
                color: #fff;
                border-color: #111827;
                background: #111827;
            }

            @media (max-width: 720px) {
                body {
                    padding: 16px;
                }

                .receipt-head,
                .total-box {
                    grid-template-columns: 1fr;
                }

                .receipt-status {
                    text-align: left;
                }

                .info-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .items {
                    table-layout: auto;
                }
            }

            @media (max-width: 520px) {
                .receipt-body,
                .receipt-head {
                    padding: 20px;
                }

                .brand {
                    align-items: flex-start;
                }

                .info-grid {
                    grid-template-columns: 1fr;
                }

                .items th,
                .items td {
                    padding: 12px 10px;
                    font-size: 13px;
                }

                .total-row {
                    align-items: flex-start;
                    flex-direction: column;
                    gap: 6px;
                }

                .actions {
                    flex-direction: column;
                }
            }

            @media print {
                @page {
                    size: A4;
                    margin: 14mm;
                }

                body {
                    min-height: auto;
                    padding: 0;
                    background: #fff;
                }

                .receipt-shell {
                    width: 100%;
                }

                .receipt {
                    border-radius: 0;
                    box-shadow: none;
                }

                .receipt-body {
                    padding: 18px;
                }

                .actions {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        @php
            $statusLabel = $receipt->receipt_status === 'printed'
                ? 'Sudah Dicetak'
                : ($receipt->receipt_status === 'ready' ? 'Siap Cetak' : 'Menunggu Lunas');
            $quantity = max((int) ($receipt->quantity ?? 1), 1);
            $unitPrice = (int) floor($receipt->amount / $quantity);
            $paidAmount = (int) ($receipt->paid_amount ?? $receipt->amount);
            $changeAmount = (int) ($receipt->change_amount ?? 0);
        @endphp

        <main class="receipt-shell">
            <section class="receipt">
                <header class="receipt-head">
                    <div class="brand">
                        <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" class="brand-logo">
                        <div>
                            <div class="brand-title">Arena Fitness</div>
                            <h1>Struk Pembayaran</h1>
                        </div>
                    </div>
                    <div class="receipt-status">
                        <div class="label">Status</div>
                        <div class="value">{{ $statusLabel }}</div>
                    </div>
                </header>

                <div class="receipt-body">
                    <div class="info-grid">
                        <div class="info-card">
                            <div class="label">Invoice</div>
                            <div class="value">{{ $receipt->invoice }}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Tanggal</div>
                            <div class="value">{{ $receipt->transaction_at?->format('d M Y H:i') ?? now()->format('d M Y H:i') }}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Pelanggan</div>
                            <div class="value">{{ $receipt->customer_name }}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Metode</div>
                            <div class="value">{{ strtoupper($receipt->payment_method) }}</div>
                        </div>
                    </div>

                    <table class="items">
                        <thead>
                            <tr>
                                <th class="item-col">Item</th>
                                <th class="right qty-col">Qty</th>
                                <th class="right money-col">Harga</th>
                                <th class="right money-col">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong class="item-name">{{ $receipt->transaction_type }}</strong>
                                    <div class="note">{{ ucfirst(str_replace('_', ' ', $receipt->transaction_group ?? 'Transaksi')) }}</div>
                                </td>
                                <td class="right">{{ $quantity }}</td>
                                <td class="right">Rp{{ number_format($unitPrice, 0, ',', '.') }}</td>
                                <td class="right">Rp{{ number_format($receipt->amount, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="total-box">
                        <div class="note receipt-note">
                            Terima kasih telah bertransaksi di Arena Fitness. Simpan struk ini sebagai bukti pembayaran resmi.
                            @if ($receipt->notes)
                                <br>Catatan: {{ $receipt->notes }}
                            @endif
                        </div>
                        <div class="total">
                            <div class="total-row">
                                <span>Total</span>
                                <strong>Rp{{ number_format($receipt->amount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="total-row">
                                <span>Diterima</span>
                                <strong>Rp{{ number_format($paidAmount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="total-row">
                                <span>Kembalian</span>
                                <strong>Rp{{ number_format($changeAmount, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="actions">
                        <button class="btn btn-dark" onclick="window.print()">Cetak Sekarang</button>
                        <a href="{{ route('cashier.receipts') }}" class="btn">Kembali</a>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
