import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { CheckCircle2, FileText, Printer, Search } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, EmptyState, Input, Pagination } from '../../Components/UI';

export default function Receipts({ receiptSearch = '', receiptQueue }) {
    const [search, setSearch] = useState(receiptSearch || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/cashier/receipts', { q: search }, { preserveState: true });
    };

    const rows = receiptQueue?.data || [];

    return (
        <AppLayout
            title="Verifikasi QRIS & Cetak Struk"
            subtitle="Konfirmasi pembayaran QRIS tertunda dan cetak bukti transaksi pelanggan"
            panel="cashier"
            active="receipts"
        >
            <Card>
                <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <form onSubmit={handleSearch} className="flex gap-2">
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari nomor invoice atau nama..."
                            className="w-64"
                        />
                        <Button type="submit" variant="secondary" size="sm">
                            <Search className="h-4 w-4" />
                            Cari
                        </Button>
                    </form>
                </div>

                {rows.length === 0 ? (
                    <EmptyState icon={FileText} title="Antrean verifikasi QRIS kosong" />
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                        <th className="pb-3 pr-4">Invoice</th>
                                        <th className="pb-3 pr-4">Pelanggan</th>
                                        <th className="pb-3 pr-4">Transaksi</th>
                                        <th className="pb-3 pr-4">Nominal</th>
                                        <th className="pb-3 pr-4">Status</th>
                                        <th className="pb-3 pr-4">Waktu</th>
                                        <th className="pb-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                    {rows.map((r) => (
                                        <tr key={r.id}>
                                            <td className="py-3 pr-4 font-mono text-xs font-bold">{r.invoice}</td>
                                            <td className="py-3 pr-4 font-semibold text-slate-900 dark:text-white">
                                                {r.customer_name}
                                            </td>
                                            <td className="py-3 pr-4 text-xs">{r.transaction_type}</td>
                                            <td className="py-3 pr-4 font-bold">{r.amount_formatted}</td>
                                            <td className="py-3 pr-4">
                                                <Badge variant={r.payment_status === 'verified' ? 'success' : 'warning'}>
                                                    {r.payment_status}
                                                </Badge>
                                            </td>
                                            <td className="py-3 pr-4 text-xs text-slate-500">{r.transaction_at}</td>
                                            <td className="py-3 text-right">
                                                <div className="inline-flex gap-2">
                                                    {r.payment_status !== 'verified' && (
                                                        <Button
                                                            variant="success"
                                                            size="xs"
                                                            onClick={() => router.post(`/cashier/verifications/${r.id}`)}
                                                        >
                                                            <CheckCircle2 className="h-3.5 w-3.5" />
                                                            Konfirmasi
                                                        </Button>
                                                    )}
                                                    <Link
                                                        href={`/cashier/receipts/${r.invoice}/print`}
                                                        className="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                                                    >
                                                        <Printer className="h-3.5 w-3.5" />
                                                        Print
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={receiptQueue} />
                    </>
                )}
            </Card>
        </AppLayout>
    );
}

