import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import {
    CheckCircle2,
    Clock,
    CreditCard,
    DollarSign,
    Dumbbell,
    Eye,
    Printer,
    Receipt,
} from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    Modal,
    Pagination,
} from '../../Components/UI';
import { formatCurrency, formatDateTime } from '../../lib/format';

export default function Payments({
    transactions,
    gymProfile = {},
}) {
    const [selectedInvoice, setSelectedInvoice] = useState(null);
    const [showInvoiceModal, setShowInvoiceModal] = useState(false);

    const openInvoice = (item) => {
        setSelectedInvoice(item);
        setShowInvoiceModal(true);
    };

    const handlePrint = () => {
        window.print();
    };

    return (
        <MemberLayout title="Riwayat Pembayaran & Invoice" active="payments">
            <Head title="Riwayat Pembayaran" />

            <div className="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-2xl font-black text-slate-900 dark:text-white">
                        Riwayat Pembayaran & Invoice
                    </h1>
                    <p className="text-sm text-slate-500">
                        Bukti transaksi resmi pendaftaran dan perpanjangan membership gym Anda
                    </p>
                </div>
            </div>

            {/* Transactions Card */}
            <Card className="overflow-hidden p-0">
                {transactions.data && transactions.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                            <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-400">
                                <tr>
                                    <th className="px-5 py-3.5 font-bold">No. Invoice</th>
                                    <th className="px-5 py-3.5 font-bold">Tanggal Pembayaran</th>
                                    <th className="px-5 py-3.5 font-bold">Paket / Layanan</th>
                                    <th className="px-5 py-3.5 font-bold">Metode</th>
                                    <th className="px-5 py-3.5 font-bold">Total Bayar</th>
                                    <th className="px-5 py-3.5 font-bold">Status</th>
                                    <th className="px-5 py-3.5 font-bold text-right">Invoice</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {transactions.data.map((trx) => (
                                    <tr key={trx.id} className="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                        <td className="px-5 py-3.5 font-mono text-xs font-bold text-slate-900 dark:text-white">
                                            {trx.invoice}
                                        </td>
                                        <td className="px-5 py-3.5 text-xs">
                                            {formatDateTime(trx.transaction_at)}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <p className="font-semibold text-slate-900 dark:text-white">
                                                {trx.package?.name || trx.notes || 'Membership Gym'}
                                            </p>
                                        </td>
                                        <td className="px-5 py-3.5 uppercase text-xs font-semibold">
                                            {trx.payment_method}
                                        </td>
                                        <td className="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                            {formatCurrency(trx.amount)}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <Badge variant={trx.payment_status === 'verified' ? 'green' : 'amber'}>
                                                {trx.payment_status === 'verified' ? 'LUNAS' : trx.payment_status}
                                            </Badge>
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                onClick={() => openInvoice(trx)}
                                                className="gap-1.5 text-xs"
                                            >
                                                <Eye className="h-3.5 w-3.5" />
                                                Lihat
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="p-8">
                        <EmptyState
                            title="Belum ada riwayat transaksi"
                            description="Transaksi pembayaran pendaftaran atau perpanjangan akan otomatis tercatat di sini."
                        />
                    </div>
                )}
            </Card>

            <div className="mt-6">
                <Pagination links={transactions.links} />
            </div>

            {/* Digital Invoice Modal */}
            <Modal
                show={showInvoiceModal}
                onClose={() => setShowInvoiceModal(false)}
                title={selectedInvoice ? `Invoice #${selectedInvoice.invoice}` : 'Invoice'}
                maxWidth="lg"
            >
                {selectedInvoice && (
                    <div className="space-y-6">
                        {/* Printable Area */}
                        <div className="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
                            {/* Header */}
                            <div className="flex items-start justify-between border-b border-slate-200 pb-5 dark:border-slate-800">
                                <div className="flex items-center gap-3">
                                    <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-600 to-orange-500 text-white shadow-sm">
                                        <Dumbbell className="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p className="text-base font-black text-slate-900 dark:text-white">
                                            {gymProfile.name || 'ARENA GYM & FITNESS'}
                                        </p>
                                        <p className="text-xs text-slate-500">{gymProfile.address}</p>
                                    </div>
                                </div>

                                <div className="text-right">
                                    <span className="font-mono text-xs font-bold text-slate-600 dark:text-slate-400">
                                        {selectedInvoice.invoice}
                                    </span>
                                    <p className="text-[11px] text-slate-400">
                                        {formatDateTime(selectedInvoice.transaction_at)}
                                    </p>
                                </div>
                            </div>

                            {/* Details */}
                            <div className="my-5 space-y-3 text-xs">
                                <div className="flex justify-between">
                                    <span className="text-slate-500">Layanan / Paket:</span>
                                    <span className="font-bold text-slate-900 dark:text-white">
                                        {selectedInvoice.package?.name || selectedInvoice.notes || 'Membership'}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-slate-500">Metode Bayar:</span>
                                    <span className="font-semibold uppercase text-slate-900 dark:text-white">
                                        {selectedInvoice.payment_method}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-slate-500">Status Pembayaran:</span>
                                    <Badge variant={selectedInvoice.payment_status === 'verified' ? 'green' : 'amber'}>
                                        {selectedInvoice.payment_status === 'verified' ? 'LUNAS' : selectedInvoice.payment_status}
                                    </Badge>
                                </div>
                                <div className="flex justify-between border-t border-slate-100 pt-3 text-sm font-bold text-slate-900 dark:border-slate-800 dark:text-white">
                                    <span>Total Nominal:</span>
                                    <span className="text-rose-600 dark:text-rose-400">
                                        {formatCurrency(selectedInvoice.amount)}
                                    </span>
                                </div>
                            </div>

                            <div className="border-t border-slate-100 pt-3 text-center text-[11px] text-slate-400 dark:border-slate-800">
                                {gymProfile.footer || 'Terima kasih atas kunjungan Anda di Arena Gym!'}
                            </div>
                        </div>

                        <div className="flex justify-end gap-3">
                            <Button
                                variant="secondary"
                                onClick={() => setShowInvoiceModal(false)}
                            >
                                Tutup
                            </Button>
                            <Button onClick={handlePrint} className="gap-2">
                                <Printer className="h-4 w-4" />
                                Cetak Invoice
                            </Button>
                        </div>
                    </div>
                )}
            </Modal>
        </MemberLayout>
    );
}
