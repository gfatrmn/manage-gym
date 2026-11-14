import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Dumbbell, Printer } from 'lucide-react';
import { Button } from '../../Components/UI';

export default function ReceiptPrint({ pageTitle, receipt = {} }) {
    return (
        <div className="min-h-screen bg-slate-100 p-4 text-slate-900 dark:bg-slate-950 sm:p-8">
            <Head title={pageTitle || `Struk ${receipt.invoice || ''}`} />

            <div className="no-print mx-auto mb-6 flex max-w-md items-center justify-between gap-2">
                <Link
                    href="/cashier/transactions"
                    className="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Kembali
                </Link>
                <Button size="sm" onClick={() => window.print()}>
                    <Printer className="h-4 w-4" />
                    Cetak Struk
                </Button>
            </div>

            <div className="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-6 font-mono text-xs text-slate-900 shadow-lg">
                <div className="border-b border-dashed border-slate-300 pb-4 text-center">
                    <div className="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white">
                        <Dumbbell className="h-5 w-5" />
                    </div>
                    <h1 className="text-base font-extrabold tracking-tight">ARENA GYM & FITNESS</h1>
                    <p className="text-[11px] text-slate-500">Bukti Pembayaran Resmi Kasir</p>
                </div>

                <div className="space-y-1.5 border-b border-dashed border-slate-300 py-4">
                    <div className="flex justify-between">
                        <span className="text-slate-500">Invoice</span>
                        <span className="font-bold">{receipt.invoice}</span>
                    </div>
                    <div className="flex justify-between">
                        <span className="text-slate-500">Tanggal</span>
                        <span>{receipt.transaction_at}</span>
                    </div>
                    <div className="flex justify-between">
                        <span className="text-slate-500">Pelanggan</span>
                        <span className="font-bold">{receipt.customer_name}</span>
                    </div>
                    <div className="flex justify-between">
                        <span className="text-slate-500">Metode</span>
                        <span>{receipt.payment_method}</span>
                    </div>
                    <div className="flex justify-between">
                        <span className="text-slate-500">Status</span>
                        <span className="uppercase">{receipt.payment_status}</span>
                    </div>
                </div>

                <div className="space-y-2 border-b border-dashed border-slate-300 py-4">
                    <div className="flex justify-between font-bold">
                        <span>
                            {receipt.transaction_type} ({receipt.quantity}x)
                        </span>
                        <span>{receipt.amount_formatted}</span>
                    </div>
                    {receipt.notes && (
                        <p className="text-[11px] text-slate-500">Catatan: {receipt.notes}</p>
                    )}
                </div>

                <div className="space-y-1.5 py-4">
                    <div className="flex justify-between text-sm font-extrabold">
                        <span>TOTAL TAGIHAN</span>
                        <span>{receipt.amount_formatted}</span>
                    </div>
                    <div className="flex justify-between">
                        <span className="text-slate-500">Dibayar</span>
                        <span>{receipt.paid_amount_formatted}</span>
                    </div>
                    <div className="flex justify-between">
                        <span className="text-slate-500">Kembalian</span>
                        <span>{receipt.change_amount_formatted}</span>
                    </div>
                </div>

                <div className="border-t border-dashed border-slate-300 pt-4 text-center text-[11px] text-slate-500">
                    Terima kasih telah berlatih di Arena Gym!
                    <br />
                    Simpan struk ini sebagai bukti transaksi yang sah.
                </div>
            </div>
        </div>
    );
}

