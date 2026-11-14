import React, { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowRight, BarChart3, DollarSign, Download, Plus, TrendingDown, TrendingUp } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    FieldError,
    Input,
    InputLabel,
    Modal,
    Select,
    StatCard,
    Textarea,
} from '../../Components/UI';
import { formatRupiah } from '../../lib/format';

export default function Reports({
    financialSummary = {},
    financialMonthlyHistory = [],
    dailyTrainingStats = {},
    membershipReportSummary = {},
    reportCatalog = [],
}) {
    const [showExpenseModal, setShowExpenseModal] = useState(false);

    const expenseForm = useForm({
        title: '',
        category: 'Operasional',
        amount: '',
        payment_method: 'cash',
        expense_date: new Date().toISOString().slice(0, 10),
        notes: '',
    });

    const submitExpense = (e) => {
        e.preventDefault();
        expenseForm.post('/admin/reports/expenses', {
            onSuccess: () => {
                expenseForm.reset('title', 'amount', 'notes');
                setShowExpenseModal(false);
            },
        });
    };

    return (
        <AppLayout
            title="Laporan & Analitik"
            subtitle={`Ringkasan keuangan aktual periode ${financialSummary.month_label || 'bulan ini'} serta katalog laporan ekspor`}
            panel="admin"
            active="reports"
        >
            {/* Header Actions */}
            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div className="flex flex-wrap items-center gap-2">
                    <Badge variant="success">Aktif: {membershipReportSummary.active || 0}</Badge>
                    <Badge variant="warning">Segera Berakhir: {membershipReportSummary.ending || 0}</Badge>
                    <Badge variant="danger">Expired: {membershipReportSummary.expired || 0}</Badge>
                    <Badge variant="info">Check-in Hari Ini: {dailyTrainingStats.today_checkins || 0}</Badge>
                </div>
                <Button size="sm" onClick={() => setShowExpenseModal(true)}>
                    <Plus className="h-4 w-4" />
                    Catat Pengeluaran Operasional
                </Button>
            </div>

            {/* Financial KPI Cards */}
            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard
                    title={`Pemasukan (${financialSummary.month_label || '-'})`}
                    value={formatRupiah(financialSummary.total_revenue)}
                    subtitle={`${financialSummary.verified_transaction_count || 0} transaksi terverifikasi`}
                    icon={TrendingUp}
                    accent="emerald"
                />
                <StatCard
                    title="Pengeluaran Operasional"
                    value={formatRupiah(financialSummary.total_expense)}
                    subtitle={`${financialSummary.expense_count || 0} catatan pengeluaran`}
                    icon={TrendingDown}
                    accent="rose"
                />
                <StatCard
                    title="Laba Bersih"
                    value={formatRupiah(financialSummary.net_revenue)}
                    subtitle="Pendapatan aktual dikurangi biaya"
                    icon={DollarSign}
                    accent="rose"
                />
            </div>

            {/* 6-Month Financial History */}
            {financialMonthlyHistory.length > 0 && (
                <Card className="mb-6">
                    <h2 className="mb-4 text-base font-bold text-slate-900">
                        Tren Keuangan 6 Bulan Terakhir
                    </h2>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                                    <th className="pb-3 pr-4">Bulan</th>
                                    <th className="pb-3 pr-4">Pemasukan</th>
                                    <th className="pb-3 pr-4">Pengeluaran</th>
                                    <th className="pb-3">Laba Bersih</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {financialMonthlyHistory.map((row, idx) => (
                                    <tr key={idx}>
                                        <td className="py-3 pr-4 font-semibold text-slate-900">
                                            {row.month_label}
                                        </td>
                                        <td className="py-3 pr-4 text-emerald-600">
                                            {formatRupiah(row.total_revenue)}
                                        </td>
                                        <td className="py-3 pr-4 text-red-600">
                                            {formatRupiah(row.total_expense)}
                                        </td>
                                        <td className="py-3 font-bold text-slate-900 dark:text-white">
                                            {formatRupiah(row.net_revenue)}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </Card>
            )}

            {/* Report Catalog */}
            <h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                Katalog Laporan Detail & Ekspor Excel
            </h2>
            <div className="grid gap-5 md:grid-cols-2">
                {reportCatalog.map((rep) => (
                    <Card key={rep.slug} className="flex flex-col justify-between">
                        <div>
                            <div className="flex items-start justify-between gap-2">
                                <div>
                                    <Badge variant="info">{rep.group}</Badge>
                                    <h3 className="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                                        {rep.title}
                                    </h3>
                                </div>
                                <BarChart3 className="h-5 w-5 text-rose-500" />
                            </div>
                            <p className="mt-1.5 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                                {rep.summary}
                            </p>
                            <div className="mt-4 flex flex-wrap gap-3 rounded-xl bg-slate-100 p-3 text-xs dark:bg-slate-800/80">
                                <div>
                                    <span className="text-slate-500">Periode: </span>
                                    <strong>{rep.date_label}</strong>
                                </div>
                                <div>
                                    <span className="text-slate-500">Volume: </span>
                                    <strong>{rep.count_label}</strong>
                                </div>
                                <div>
                                    <span className="text-slate-500">Highlight: </span>
                                    <strong className="text-rose-600 dark:text-rose-400">{rep.highlight}</strong>
                                </div>
                            </div>
                        </div>

                        <div className="mt-5 flex flex-wrap items-center justify-between gap-2 border-t border-slate-200 pt-4 dark:border-slate-800">
                            <a
                                href={`/admin/reports/${rep.slug}?export=1`}
                                className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >
                                <Download className="h-3.5 w-3.5" />
                                Export Excel
                            </a>
                            <Link
                                href={`/admin/reports/${rep.slug}`}
                                className="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-rose-500"
                            >
                                Lihat Detail
                                <ArrowRight className="h-3.5 w-3.5" />
                            </Link>
                        </div>
                    </Card>
                ))}
            </div>

            {/* Modal Pengeluaran */}
            <Modal
                open={showExpenseModal}
                onClose={() => setShowExpenseModal(false)}
                title="Catat Pengeluaran Operasional"
            >
                <form onSubmit={submitExpense} className="space-y-4">
                    <div>
                        <InputLabel required>Nama Pengeluaran</InputLabel>
                        <Input
                            value={expenseForm.data.title}
                            onChange={(e) => expenseForm.setData('title', e.target.value)}
                            placeholder="Contoh: Listrik Bulanan / Servis Treadmill"
                            error={expenseForm.errors.title}
                        />
                        <FieldError message={expenseForm.errors.title} />
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel required>Kategori</InputLabel>
                            <Select
                                value={expenseForm.data.category}
                                onChange={(e) => expenseForm.setData('category', e.target.value)}
                            >
                                <option value="Operasional">Operasional</option>
                                <option value="Utilitas">Listrik & Air</option>
                                <option value="Maintenance">Maintenance Alat</option>
                                <option value="Gaji">Gaji & Insentif</option>
                                <option value="Lainnya">Lainnya</option>
                            </Select>
                        </div>
                        <div>
                            <InputLabel required>Tanggal</InputLabel>
                            <Input
                                type="date"
                                value={expenseForm.data.expense_date}
                                onChange={(e) => expenseForm.setData('expense_date', e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel required>Nominal (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={expenseForm.data.amount}
                                onChange={(e) => expenseForm.setData('amount', e.target.value)}
                            />
                            <FieldError message={expenseForm.errors.amount} />
                        </div>
                        <div>
                            <InputLabel>Metode</InputLabel>
                            <Select
                                value={expenseForm.data.payment_method}
                                onChange={(e) => expenseForm.setData('payment_method', e.target.value)}
                            >
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                            </Select>
                        </div>
                    </div>
                    <div>
                        <InputLabel>Catatan</InputLabel>
                        <Textarea
                            rows={2}
                            value={expenseForm.data.notes}
                            onChange={(e) => expenseForm.setData('notes', e.target.value)}
                        />
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowExpenseModal(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={expenseForm.processing}>
                            Simpan Pengeluaran
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}

