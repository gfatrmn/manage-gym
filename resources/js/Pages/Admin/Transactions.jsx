import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import {
    ArrowUpDown,
    Calendar,
    CreditCard,
    DollarSign,
    Download,
    Eye,
    FileSpreadsheet,
    FileText,
    Filter,
    Receipt,
    RotateCcw,
    Search,
    ShoppingBag,
    Ticket,
    User,
} from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    Input,
    InputLabel,
    Pagination,
    Select,
} from '../../Components/UI';
import { formatCurrency, formatDate, formatDateTime } from '../../lib/format';

export default function Transactions({
    transactions,
    totalRevenue = 0,
    totalTransactions = 0,
    filters = {},
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [group, setGroup] = useState(filters.group || 'all');
    const [status, setStatus] = useState(filters.status || 'all');
    const [paymentMethod, setPaymentMethod] = useState(filters.payment_method || 'all');
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');

    const handleFilter = (e) => {
        e?.preventDefault();
        window.location.href = `/admin/transactions?${new URLSearchParams({
            q: search,
            group,
            status,
            payment_method: paymentMethod,
            start_date: startDate,
            end_date: endDate,
        }).toString()}`;
    };

    const handleReset = () => {
        window.location.href = '/admin/transactions';
    };

    const getGroupBadge = (grp) => {
        switch (grp) {
            case 'member_payment':
                return <Badge variant="green">Membership</Badge>;
            case 'daily_pass':
                return <Badge variant="blue">Tamu Harian</Badge>;
            case 'product_pos':
                return <Badge variant="purple">Produk POS</Badge>;
            default:
                return <Badge variant="slate">{grp}</Badge>;
        }
    };

    const getStatusBadge = (st) => {
        switch (st) {
            case 'verified':
                return <Badge variant="green">Sukses / Lunas</Badge>;
            case 'pending':
                return <Badge variant="warning">Menunggu</Badge>;
            case 'cancelled':
            case 'failed':
                return <Badge variant="rose">Dibatalkan</Badge>;
            default:
                return <Badge variant="slate">{st}</Badge>;
        }
    };

    return (
        <AppLayout
            title="Semua Transaksi"
            subtitle="Daftar seluruh riwayat pembayaran, membership, tiket harian, dan penjualan produk"
            active="transactions"
            panel="admin"
        >
            <Head title="Semua Transaksi" />

            {/* Stat Cards */}
            <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card className="flex items-center gap-4 p-5">
                    <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400">
                        <DollarSign className="h-6 w-6" />
                    </div>
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Total Pendapatan Terverifikasi
                        </p>
                        <p className="text-2xl font-black text-slate-900 dark:text-white">
                            {formatCurrency(totalRevenue)}
                        </p>
                    </div>
                </Card>

                <Card className="flex items-center gap-4 p-5">
                    <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                        <Receipt className="h-6 w-6" />
                    </div>
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Total Transaksi
                        </p>
                        <p className="text-2xl font-black text-slate-900 dark:text-white">
                            {totalTransactions} Transaksi
                        </p>
                    </div>
                </Card>

                <Card className="flex items-center justify-between p-5">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Aksi Cepat
                        </p>
                        <p className="text-sm font-medium text-slate-600 dark:text-slate-400 mt-1">
                            Download dan arsip laporan transaksi
                        </p>
                    </div>
                    <a
                        href="/admin/reports"
                        className="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200"
                    >
                        <FileSpreadsheet className="h-4 w-4" />
                        Buka Laporan
                    </a>
                </Card>
            </div>

            {/* Filter Form Card */}
            <Card className="mb-6 p-4">
                <form onSubmit={handleFilter} className="space-y-3">
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <InputLabel htmlFor="search">Cari Invoice / Nama</InputLabel>
                            <Input
                                id="search"
                                placeholder="INV-..., nama member..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                        </div>

                        <div>
                            <InputLabel htmlFor="group">Jenis Transaksi</InputLabel>
                            <Select
                                id="group"
                                value={group}
                                onChange={(e) => setGroup(e.target.value)}
                            >
                                <option value="all">Semua Jenis</option>
                                <option value="member_payment">Membership (Daftar / Perpanjang)</option>
                                <option value="daily_pass">Tamu Harian</option>
                                <option value="product_pos">Penjualan Produk POS</option>
                            </Select>
                        </div>

                        <div>
                            <InputLabel htmlFor="status">Status Pembayaran</InputLabel>
                            <Select
                                id="status"
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                            >
                                <option value="all">Semua Status</option>
                                <option value="verified">Sukses / Lunas</option>
                                <option value="pending">Menunggu Verifikasi</option>
                                <option value="cancelled">Dibatalkan</option>
                            </Select>
                        </div>

                        <div>
                            <InputLabel htmlFor="payment_method">Metode Pembayaran</InputLabel>
                            <Select
                                id="payment_method"
                                value={paymentMethod}
                                onChange={(e) => setPaymentMethod(e.target.value)}
                            >
                                <option value="all">Semua Metode</option>
                                <option value="cash">Tunai (Cash)</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer Bank</option>
                            </Select>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div className="flex flex-wrap items-center gap-2">
                            <span className="text-xs font-semibold text-slate-500">Periode:</span>
                            <Input
                                type="date"
                                value={startDate}
                                onChange={(e) => setStartDate(e.target.value)}
                                className="w-auto py-1 text-xs"
                            />
                            <span className="text-xs text-slate-400">s/d</span>
                            <Input
                                type="date"
                                value={endDate}
                                onChange={(e) => setEndDate(e.target.value)}
                                className="w-auto py-1 text-xs"
                            />
                        </div>

                        <div className="flex items-center gap-2">
                            <Button type="button" variant="ghost" size="sm" onClick={handleReset}>
                                <RotateCcw className="mr-1 h-3.5 w-3.5" />
                                Reset
                            </Button>
                            <Button type="submit" size="sm">
                                <Filter className="mr-1 h-3.5 w-3.5" />
                                Terapkan Filter
                            </Button>
                        </div>
                    </div>
                </form>
            </Card>

            {/* Transactions Table */}
            <Card className="overflow-hidden p-0">
                {transactions.data && transactions.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                            <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-400">
                                <tr>
                                    <th className="px-5 py-3.5 font-bold">No. Invoice</th>
                                    <th className="px-5 py-3.5 font-bold">Tanggal & Waktu</th>
                                    <th className="px-5 py-3.5 font-bold">Jenis & Detail</th>
                                    <th className="px-5 py-3.5 font-bold">Pelanggan / Member</th>
                                    <th className="px-5 py-3.5 font-bold">Metode</th>
                                    <th className="px-5 py-3.5 font-bold">Nominal</th>
                                    <th className="px-5 py-3.5 font-bold">Status</th>
                                    <th className="px-5 py-3.5 font-bold text-right">Aksi</th>
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
                                            <div className="flex flex-col gap-1">
                                                <div>{getGroupBadge(trx.transaction_group)}</div>
                                                <span className="text-xs text-slate-700 dark:text-slate-300">
                                                    {trx.notes || (trx.product ? trx.product.name : 'Pembayaran Layanan')}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <p className="font-semibold text-slate-900 dark:text-white text-xs">
                                                {trx.member ? trx.member.full_name : trx.customer_name || 'Tamu / Umum'}
                                            </p>
                                            {trx.member && (
                                                <p className="text-[11px] text-slate-400 font-mono">
                                                    {trx.member.checkin_code}
                                                </p>
                                            )}
                                        </td>
                                        <td className="px-5 py-3.5 uppercase text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            {trx.payment_method}
                                        </td>
                                        <td className="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                            {formatCurrency(trx.amount)}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            {getStatusBadge(trx.payment_status)}
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <a
                                                href={`/admin/transactions/${trx.id}`}
                                                className="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                            >
                                                <Eye className="h-3.5 w-3.5" />
                                                Invoice
                                            </a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="p-8">
                        <EmptyState
                            title="Tidak ada transaksi"
                            description="Belum ada data transaksi yang cocok dengan kriteria filter yang Anda pilih."
                        />
                    </div>
                )}
            </Card>

            {/* Pagination */}
            <div className="mt-6">
                <Pagination links={transactions.links} />
            </div>
        </AppLayout>
    );
}
