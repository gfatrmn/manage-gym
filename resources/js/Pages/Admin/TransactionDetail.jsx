import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, CheckCircle2, Clock, Dumbbell, Printer, Receipt } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card } from '../../Components/UI';
import { formatCurrency, formatDateTime } from '../../lib/format';

export default function TransactionDetail({ transaction, gymProfile = {} }) {
    const handlePrint = () => {
        window.print();
    };

    return (
        <AppLayout
            title={`Invoice #${transaction.invoice}`}
            subtitle="Detail rincian pembayaran dan bukti transaksi resmi"
            active="transactions"
            panel="admin"
        >
            <Head title={`Invoice #${transaction.invoice}`} />

            <div className="mb-6 flex items-center justify-between">
                <Link
                    href="/admin/transactions"
                    className="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Kembali ke Semua Transaksi
                </Link>

                <Button onClick={handlePrint} variant="secondary" className="gap-2 print:hidden">
                    <Printer className="h-4 w-4" />
                    Cetak / Simpan PDF
                </Button>
            </div>

            {/* Printable Invoice Sheet */}
            <div className="mx-auto max-w-3xl">
                <Card className="overflow-hidden p-8 sm:p-10 border border-slate-200 bg-white shadow-lg print:border-none print:shadow-none dark:border-slate-800 dark:bg-slate-900">
                    {/* Header */}
                    <div className="flex flex-col justify-between gap-6 border-b border-slate-200 pb-8 sm:flex-row sm:items-start dark:border-slate-800">
                        <div className="flex items-center gap-3">
                            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-600 text-white shadow-md shadow-red-600/20">
                                <Dumbbell className="h-6 w-6" />
                            </div>
                            <div>
                                <h2 className="text-xl font-black text-slate-900 dark:text-white">
                                    {gymProfile.name || 'ARENA GYM & FITNESS'}
                                </h2>
                                <p className="text-xs text-slate-500">{gymProfile.address || 'Pusat Kebugaran & Olahraga'}</p>
                                <p className="text-xs text-slate-500">Telp: {gymProfile.phone || '0812-3456-7890'}</p>
                            </div>
                        </div>

                        <div className="sm:text-right">
                            <span className="rounded-md bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                {transaction.invoice}
                            </span>
                            <h3 className="mt-2 text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                INVOICE
                            </h3>
                            <p className="text-xs text-slate-500">
                                Tanggal: {formatDateTime(transaction.transaction_at)}
                            </p>
                        </div>
                    </div>

                    {/* Customer & Payment Info */}
                    <div className="grid grid-cols-1 gap-6 py-6 sm:grid-cols-2 border-b border-slate-200 dark:border-slate-800">
                        <div>
                            <p className="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Ditagihkan Kepada:
                            </p>
                            <p className="mt-1 text-base font-bold text-slate-900 dark:text-white">
                                {transaction.member ? transaction.member.full_name : transaction.customer_name || 'Pelanggan Tamu'}
                            </p>
                            {transaction.member && (
                                <>
                                    <p className="text-xs text-slate-500 font-mono">ID: {transaction.member.checkin_code}</p>
                                    <p className="text-xs text-slate-500">Email: {transaction.member.email || '-'}</p>
                                    <p className="text-xs text-slate-500">No. HP: {transaction.member.phone || '-'}</p>
                                </>
                            )}
                        </div>

                        <div className="sm:text-right">
                            <p className="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Status & Kasir:
                            </p>
                            <div className="mt-1 flex items-center gap-2 sm:justify-end">
                                <Badge variant={transaction.payment_status === 'verified' ? 'success' : 'warning'}>
                                    {transaction.payment_status === 'verified' ? 'LUNAS / VERIFIED' : transaction.payment_status.toUpperCase()}
                                </Badge>
                            </div>
                            <p className="mt-2 text-xs text-slate-500">
                                Kasir: <span className="font-semibold text-slate-700 dark:text-slate-300">{transaction.cashier?.name || 'Kasir Staff'}</span>
                            </p>
                            <p className="text-xs text-slate-500">
                                Metode: <span className="font-semibold uppercase text-slate-700 dark:text-slate-300">{transaction.payment_method}</span>
                            </p>
                        </div>
                    </div>

                    {/* Items Table */}
                    <div className="py-6">
                        <table className="w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800">
                                    <th className="pb-3 font-bold">Deskripsi Layanan / Produk</th>
                                    <th className="pb-3 font-bold text-center">Qty</th>
                                    <th className="pb-3 font-bold text-right">Harga Satuan</th>
                                    <th className="pb-3 font-bold text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                                <tr>
                                    <td className="py-4">
                                        <p className="font-bold text-slate-900 dark:text-white">
                                            {transaction.package
                                                ? `Membership: ${transaction.package.name}`
                                                : transaction.product
                                                ? `Produk: ${transaction.product.name}`
                                                : transaction.notes || 'Layanan Gym & Kunjungan'}
                                        </p>
                                        <p className="text-xs text-slate-500">
                                            Kategori: {transaction.transaction_group}
                                        </p>
                                    </td>
                                    <td className="py-4 text-center font-semibold">
                                        {transaction.quantity || 1}
                                    </td>
                                    <td className="py-4 text-right font-mono">
                                        {formatCurrency(transaction.amount + (transaction.discount || 0))}
                                    </td>
                                    <td className="py-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                        {formatCurrency(transaction.amount + (transaction.discount || 0))}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {/* Summary Calculation */}
                    <div className="border-t border-slate-200 pt-4 dark:border-slate-800">
                        <div className="flex flex-col items-end gap-1.5 text-sm">
                            <div className="flex w-64 justify-between text-slate-500">
                                <span>Subtotal:</span>
                                <span className="font-mono">{formatCurrency(transaction.amount + (transaction.discount || 0))}</span>
                            </div>
                            {Boolean(transaction.discount) && (
                                <div className="flex w-64 justify-between text-emerald-600 dark:text-emerald-400">
                                    <span>Diskon:</span>
                                    <span className="font-mono">-{formatCurrency(transaction.discount)}</span>
                                </div>
                            )}
                            <div className="flex w-64 justify-between border-t border-slate-200 pt-2 text-base font-bold text-slate-900 dark:border-slate-800 dark:text-white">
                                <span>Total Bayar:</span>
                                <span className="font-mono text-lg text-rose-600 dark:text-rose-400">
                                    {formatCurrency(transaction.amount)}
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="mt-10 border-t border-slate-200 pt-6 text-center text-xs text-slate-500 dark:border-slate-800">
                        <p className="font-semibold text-slate-700 dark:text-slate-300">
                            {gymProfile.footer || 'Terima kasih atas kunjungan dan kepercayaan Anda berlatih di Arena Gym.'}
                        </p>
                        <p className="mt-1 text-[11px] text-slate-400">
                            Harap simpan bukti pembayaran ini sebagai tanda bukti sah pendaftaran / perpanjangan membership.
                        </p>
                    </div>
                </Card>
            </div>
        </AppLayout>
    );
}
