import React, { useState } from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import { Filter, Plus, Printer, Receipt } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    FieldError,
    Input,
    InputLabel,
    Modal,
    Select,
    StatCard,
    Textarea,
} from '../../Components/UI';
import { formatRupiah } from '../../lib/format';

export default function Transactions({
    paymentHistory = [],
    paymentFilters = {},
    paymentSummary = {},
}) {
    const [filters, setFilters] = useState({
        type: paymentFilters.type || 'all',
        status: paymentFilters.status || 'all',
        method: paymentFilters.method || 'all',
        period: paymentFilters.period || 'today',
        q: paymentFilters.q || '',
    });
    const [showOtherModal, setShowOtherModal] = useState(false);

    const otherForm = useForm({
        transaction_group: 'other',
        customer_name: '',
        transaction_type: 'Sewa Loker',
        amount: 20000,
        paid_amount: 50000,
        payment_method: 'cash',
        notes: '',
    });

    const applyFilter = (e) => {
        e.preventDefault();
        router.get('/cashier/transactions', filters, { preserveState: true });
    };

    const submitOther = (e) => {
        e.preventDefault();
        otherForm.post('/cashier/transactions', {
            onSuccess: () => {
                otherForm.reset('customer_name', 'notes');
                setShowOtherModal(false);
            },
        });
    };

    const change =
        otherForm.data.payment_method === 'cash'
            ? Math.max(0, Number(otherForm.data.paid_amount || 0) - Number(otherForm.data.amount || 0))
            : 0;

    return (
        <AppLayout
            title="Riwayat & Transaksi Kasir"
            subtitle="Pantau seluruh transaksi member, daily pass, produk, dan transaksi lainnya"
            panel="cashier"
            active="transactions"
        >
            <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard title="Ditampilkan" value={paymentSummary.shown_count || 0} subtitle={paymentSummary.shown_total || 'Rp 0'} icon={Receipt} accent="rose" />
                <StatCard title="Hari Ini" value={paymentSummary.today_count || 0} subtitle={paymentSummary.today_total || 'Rp 0'} icon={Receipt} accent="emerald" />
                <StatCard title="Pending QRIS" value={paymentSummary.pending_count || 0} icon={Receipt} accent="amber" />
                <StatCard title="Total Semua" value={paymentSummary.all_count || 0} icon={Receipt} accent="sky" />
            </div>

            <Card>
                <div className="mb-5 flex flex-col justify-between gap-3 xl:flex-row xl:items-end">
                    <form onSubmit={applyFilter} className="flex flex-wrap items-end gap-2">
                        <div>
                            <InputLabel>Cari</InputLabel>
                            <Input
                                value={filters.q}
                                onChange={(e) => setFilters({ ...filters, q: e.target.value })}
                                placeholder="Invoice / Nama..."
                                className="w-44"
                            />
                        </div>
                        <div>
                            <InputLabel>Grup</InputLabel>
                            <Select
                                value={filters.type}
                                onChange={(e) => setFilters({ ...filters, type: e.target.value })}
                            >
                                <option value="all">Semua</option>
                                <option value="member_payment">Member</option>
                                <option value="daily_pass">Daily Pass</option>
                                <option value="product_sale">Produk</option>
                                <option value="other">Lainnya</option>
                            </Select>
                        </div>
                        <div>
                            <InputLabel>Status</InputLabel>
                            <Select
                                value={filters.status}
                                onChange={(e) => setFilters({ ...filters, status: e.target.value })}
                            >
                                <option value="all">Semua</option>
                                <option value="verified">Terverifikasi</option>
                                <option value="pending">Pending</option>
                            </Select>
                        </div>
                        <div>
                            <InputLabel>Metode</InputLabel>
                            <Select
                                value={filters.method}
                                onChange={(e) => setFilters({ ...filters, method: e.target.value })}
                            >
                                <option value="all">Semua</option>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                            </Select>
                        </div>
                        <div>
                            <InputLabel>Periode</InputLabel>
                            <Select
                                value={filters.period}
                                onChange={(e) => setFilters({ ...filters, period: e.target.value })}
                            >
                                <option value="today">Hari Ini</option>
                                <option value="month">Bulan Ini</option>
                                <option value="all">Semua Waktu</option>
                            </Select>
                        </div>
                        <Button type="submit" variant="secondary">
                            <Filter className="h-4 w-4" />
                            Filter
                        </Button>
                    </form>

                    <Button size="sm" onClick={() => setShowOtherModal(true)}>
                        <Plus className="h-4 w-4" />
                        Transaksi Lainnya
                    </Button>
                </div>

                {paymentHistory.length === 0 ? (
                    <EmptyState icon={Receipt} title="Tidak ada transaksi sesuai filter" />
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                    <th className="pb-3 pr-4">Invoice</th>
                                    <th className="pb-3 pr-4">Pelanggan</th>
                                    <th className="pb-3 pr-4">Jenis Transaksi</th>
                                    <th className="pb-3 pr-4">Nominal</th>
                                    <th className="pb-3 pr-4">Metode</th>
                                    <th className="pb-3 pr-4">Status</th>
                                    <th className="pb-3 pr-4">Waktu</th>
                                    <th className="pb-3 text-right">Struk</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {paymentHistory.map((t) => (
                                    <tr key={t.id}>
                                        <td className="py-3 pr-4 font-mono text-xs font-bold">{t.invoice}</td>
                                        <td className="py-3 pr-4 font-semibold text-slate-900 dark:text-white">
                                            {t.customer_name}
                                        </td>
                                        <td className="py-3 pr-4 text-xs">
                                            <p className="font-medium">{t.transaction_type}</p>
                                            <p className="text-slate-400">{t.transaction_group}</p>
                                        </td>
                                        <td className="py-3 pr-4 font-bold">{t.amount_formatted}</td>
                                        <td className="py-3 pr-4">{t.payment_method}</td>
                                        <td className="py-3 pr-4">
                                            <Badge variant={t.payment_status === 'verified' ? 'success' : 'warning'}>
                                                {t.payment_status}
                                            </Badge>
                                        </td>
                                        <td className="py-3 pr-4 text-xs text-slate-500">{t.transaction_at}</td>
                                        <td className="py-3 text-right">
                                            <Link
                                                href={`/cashier/receipts/${t.invoice}/print`}
                                                className="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                                            >
                                                <Printer className="h-3.5 w-3.5" />
                                                Cetak
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </Card>

            <Modal
                open={showOtherModal}
                onClose={() => setShowOtherModal(false)}
                title="Input Transaksi Layanan Lainnya"
                subtitle="Catat transaksi seperti sewa loker, handuk, atau biaya admin"
            >
                <form onSubmit={submitOther} className="space-y-4">
                    <div>
                        <InputLabel required>Nama Pelanggan</InputLabel>
                        <Input
                            value={otherForm.data.customer_name}
                            onChange={(e) => otherForm.setData('customer_name', e.target.value)}
                            error={otherForm.errors.customer_name}
                        />
                        <FieldError message={otherForm.errors.customer_name} />
                    </div>
                    <div>
                        <InputLabel required>Keterangan Layanan</InputLabel>
                        <Input
                            value={otherForm.data.transaction_type}
                            onChange={(e) => otherForm.setData('transaction_type', e.target.value)}
                        />
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel required>Nominal (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={otherForm.data.amount}
                                onChange={(e) => otherForm.setData('amount', e.target.value)}
                            />
                        </div>
                        <div>
                            <InputLabel required>Metode</InputLabel>
                            <Select
                                value={otherForm.data.payment_method}
                                onChange={(e) => otherForm.setData('payment_method', e.target.value)}
                            >
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                            </Select>
                        </div>
                    </div>
                    {otherForm.data.payment_method === 'cash' && (
                        <div>
                            <InputLabel required>Uang Diterima (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={otherForm.data.paid_amount}
                                onChange={(e) => otherForm.setData('paid_amount', e.target.value)}
                            />
                            <FieldError message={otherForm.errors.paid_amount} />
                            <p className="mt-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                Kembalian: {formatRupiah(change)}
                            </p>
                        </div>
                    )}
                    <div>
                        <InputLabel>Catatan</InputLabel>
                        <Textarea
                            rows={2}
                            value={otherForm.data.notes}
                            onChange={(e) => otherForm.setData('notes', e.target.value)}
                        />
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowOtherModal(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={otherForm.processing}>
                            Simpan Transaksi
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}

