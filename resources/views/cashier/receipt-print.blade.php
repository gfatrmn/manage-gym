<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
        <style>
            body { font-family: 'Inter', sans-serif; background: #f5f7fb; margin: 0; padding: 32px; color: #0f172a; }
            .sheet { max-width: 720px; margin: 0 auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 24px; padding: 32px; box-shadow: 0 18px 40px rgba(15,23,42,.08); }
            .muted { color: #64748b; }
            .row { display: flex; justify-content: space-between; gap: 16px; margin-top: 16px; }
            .label { font-size: 12px; text-transform: uppercase; letter-spacing: .1em; color: #64748b; }
            .value { font-weight: 700; margin-top: 4px; }
            .actions { margin-top: 24px; display: flex; gap: 12px; }
            .btn { padding: 10px 16px; border-radius: 999px; text-decoration: none; border: 1px solid #cbd5e1; color: #0f172a; }
            .btn-dark { background: #0f172a; color: #fff; border-color: #0f172a; }
            @media print { .actions { display: none; } body { background: #fff; padding: 0; } .sheet { box-shadow: none; border: 0; max-width: none; } }
        </style>
    </head>
    <body>
        <div class="sheet">
            <div class="label">Arena Gym</div>
            <h1 style="margin:8px 0 4px;">Bukti Pembayaran</h1>
            <div class="muted">Dokumen ini dapat dicetak sebagai bukti transaksi kasir.</div>

            <div class="row">
                <div>
                    <div class="label">Invoice</div>
                    <div class="value">{{ $receipt->invoice }}</div>
                </div>
                <div>
                    <div class="label">Status</div>
                    <div class="value">{{ $receipt->receipt_status === 'printed' ? 'Sudah Dicetak' : ($receipt->receipt_status === 'ready' ? 'Siap Cetak' : 'Menunggu Lunas') }}</div>
                </div>
            </div>

            <div class="row">
                <div>
                    <div class="label">Pelanggan</div>
                    <div class="value">{{ $receipt->customer_name }}</div>
                </div>
                <div>
                    <div class="label">Jenis</div>
                    <div class="value">{{ $receipt->transaction_type }}</div>
                </div>
            </div>

            <div class="row">
                <div>
                    <div class="label">Nominal</div>
                    <div class="value">Rp{{ number_format($receipt->amount, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="label">Metode</div>
                    <div class="value">{{ strtoupper($receipt->payment_method) }}</div>
                </div>
            </div>

            <div class="actions">
                <button class="btn btn-dark" onclick="window.print()">Cetak Sekarang</button>
                <a href="{{ route('cashier.receipts') }}" class="btn">Kembali</a>
            </div>
        </div>
    </body>
</html>
