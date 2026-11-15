import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    CheckCircle2,
    Clock,
    CreditCard,
    DollarSign,
    Printer,
    QrCode,
    Receipt,
    ShoppingBag,
    Ticket,
    User,
    UserCheck,
    Users,
    Zap,
} from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Card } from '../../Components/UI';
import { cn } from '../../lib/format';

export default function Dashboard({
    cashierShift = {},
    cashierStats = [],
    latestTransactions = [],
    latestCheckins = [],
    todayMemberCheckinCount = 0,
    todayDailyPassCount = 0,
}) {
    return (
        <AppLayout
            title="Dashboard Kasir"
            subtitle={`Shift Aktif: ${cashierShift.label || '08:00 - 16:00'} · Arena Gym POS`}
            panel="cashier"
            active="dashboard"
        >
            <div className="space-y-5">
                {/* 1. TOP BANNER (SOLID RED, NO GRADIENT) */}
                <div className="flex flex-col justify-between gap-4 rounded-2xl bg-red-600 p-4 sm:p-5 text-white shadow-md shadow-red-600/15 sm:flex-row sm:items-center">
                    <div className="space-y-1">
                        <h2 className="text-lg sm:text-xl font-bold tracking-tight text-white">
                            Dashboard Kasir & Front-Office
                        </h2>
                        <p className="text-xs text-red-100">
                            Ringkasan operasional harian, transaksi penjualan POS, dan absensi kedatangan member.
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <Link
                            href="/cashier/checkins"
                            className="inline-flex items-center gap-1.5 rounded-xl bg-white px-3.5 py-2 text-xs font-bold text-red-600 shadow-sm transition hover:bg-red-50"
                        >
                            <QrCode className="h-4 w-4" />
                            Scan Check-in
                        </Link>
                        <Link
                            href="/cashier/transactions/products"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-white/25 bg-red-700/50 px-3 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-white hover:text-red-600"
                        >
                            <ShoppingBag className="h-4 w-4" />
                            POS Produk
                        </Link>
                        <Link
                            href="/cashier/daily-payments"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-white/25 bg-red-700/50 px-3 py-2 text-xs font-semibold text-white shadow-2xs transition hover:bg-white hover:text-red-600"
                        >
                            <Ticket className="h-4 w-4" />
                            Daily Pass
                        </Link>
                    </div>
                </div>

                {/* 2. COMPACT 4 KPI CARDS (1 ROW, NOT TOO BIG) */}
                <div className="grid grid-cols-1 gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
                    {/* KPI 1: Pemasukan Hari Ini */}
                    <div className="flex items-center justify-between rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-2xs">
                        <div>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Pemasukan Hari Ini
                            </p>
                            <p className="mt-1 text-lg sm:text-xl font-extrabold tracking-tight text-slate-900">
                                {cashierStats[3]?.value || 'Rp 0'}
                            </p>
                            <p className="mt-0.5 text-[11px] font-semibold text-emerald-600">
                                {cashierStats[3]?.change || 'Lunas'}
                            </p>
                        </div>
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                            <DollarSign className="h-5 w-5" />
                        </div>
                    </div>

                    {/* KPI 2: Total Transaksi */}
                    <div className="flex items-center justify-between rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-2xs">
                        <div>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Total Transaksi
                            </p>
                            <p className="mt-1 text-lg sm:text-xl font-extrabold tracking-tight text-slate-900">
                                {cashierStats[2]?.value || '0'}
                            </p>
                            <p className="mt-0.5 text-[11px] text-slate-500">
                                {cashierStats[2]?.change || 'Shift hari ini'}
                            </p>
                        </div>
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                            <Receipt className="h-5 w-5" />
                        </div>
                    </div>

                    {/* KPI 3: Check-in Member */}
                    <div className="flex items-center justify-between rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-2xs">
                        <div>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Member Hadir
                            </p>
                            <p className="mt-1 text-lg sm:text-xl font-extrabold tracking-tight text-slate-900">
                                {todayMemberCheckinCount}
                                <span className="ml-1 text-xs font-normal text-slate-500">Orang</span>
                            </p>
                            <p className="mt-0.5 text-[11px] text-slate-500">
                                Absensi member aktif
                            </p>
                        </div>
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                            <UserCheck className="h-5 w-5" />
                        </div>
                    </div>

                    {/* KPI 4: Tamu Harian (Daily Pass) */}
                    <div className="flex items-center justify-between rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-2xs">
                        <div>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Tamu Daily Pass
                            </p>
                            <p className="mt-1 text-lg sm:text-xl font-extrabold tracking-tight text-slate-900">
                                {todayDailyPassCount}
                                <span className="ml-1 text-xs font-normal text-slate-500">Orang</span>
                            </p>
                            <p className="mt-0.5 text-[11px] text-slate-500">
                                Kunjungan non-member
                            </p>
                        </div>
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                            <Ticket className="h-5 w-5" />
                        </div>
                    </div>
                </div>

                {/* 3. TWO TABLES: 5 TRANSAKSI TERBARU & 5 RIWAYAT CHECK-IN TERBARU */}
                <div className="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    {/* TABEL 1: 5 TRANSAKSI POS TERBARU */}
                    <div className="rounded-2xl border border-slate-200/90 bg-white shadow-2xs overflow-hidden">
                        <div className="flex items-center justify-between border-b border-slate-100 p-4">
                            <div className="flex items-center gap-2">
                                <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600">
                                    <Receipt className="h-4 w-4" />
                                </div>
                                <div>
                                    <h3 className="text-sm font-bold text-slate-900">
                                        5 Transaksi POS Terbaru
                                    </h3>
                                    <p className="text-[11px] text-slate-500">
                                        Penjualan produk, paket, dan daily pass
                                    </p>
                                </div>
                            </div>
                            <Link
                                href="/cashier/transactions"
                                className="inline-flex items-center gap-1 text-xs font-bold text-red-600 hover:underline"
                            >
                                Semua
                                <ArrowRight className="h-3 w-3" />
                            </Link>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                        <th className="py-2.5 px-3.5">Pelanggan & Transaksi</th>
                                        <th className="py-2.5 px-3">Metode</th>
                                        <th className="py-2.5 px-3 text-right">Total</th>
                                        <th className="py-2.5 px-3 text-center">Struk</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 text-xs">
                                    {latestTransactions.length === 0 ? (
                                        <tr>
                                            <td colSpan={4} className="py-8 text-center text-slate-400">
                                                Belum ada transaksi tercatat hari ini.
                                            </td>
                                        </tr>
                                    ) : (
                                        latestTransactions.map((trx) => (
                                            <tr key={trx.id} className="hover:bg-slate-50/80 transition">
                                                <td className="py-2.5 px-3.5 min-w-[140px]">
                                                    <p className="font-bold text-slate-900 truncate">
                                                        {trx.customer_name}
                                                    </p>
                                                    <p className="text-[11px] text-slate-500 truncate">
                                                        {trx.transaction_type} · {trx.time}
                                                    </p>
                                                </td>
                                                <td className="py-2.5 px-3 whitespace-nowrap">
                                                    <span className="inline-flex rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-700">
                                                        {trx.payment_method}
                                                    </span>
                                                </td>
                                                <td className="py-2.5 px-3 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                                    {trx.amount_formatted}
                                                </td>
                                                <td className="py-2.5 px-3 text-center whitespace-nowrap">
                                                    <Link
                                                        href={`/cashier/receipts/${trx.invoice}/print`}
                                                        title="Cetak Struk"
                                                        className="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-red-300 hover:text-red-600 transition"
                                                    >
                                                        <Printer className="h-3.5 w-3.5" />
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* TABEL 2: 5 RIWAYAT CHECK-IN MEMBER & NON MEMBER TERBARU */}
                    <div className="rounded-2xl border border-slate-200/90 bg-white shadow-2xs overflow-hidden">
                        <div className="flex items-center justify-between border-b border-slate-100 p-4">
                            <div className="flex items-center gap-2">
                                <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600">
                                    <UserCheck className="h-4 w-4" />
                                </div>
                                <div>
                                    <h3 className="text-sm font-bold text-slate-900">
                                        5 Check-in Member & Tamu Terbaru
                                    </h3>
                                    <p className="text-[11px] text-slate-500">
                                        Absensi member gym dan tiket tamu harian
                                    </p>
                                </div>
                            </div>
                            <Link
                                href="/cashier/checkins"
                                className="inline-flex items-center gap-1 text-xs font-bold text-red-600 hover:underline"
                            >
                                Semua
                                <ArrowRight className="h-3 w-3" />
                            </Link>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                        <th className="py-2.5 px-3.5">Nama</th>
                                        <th className="py-2.5 px-3">Tipe</th>
                                        <th className="py-2.5 px-3">Jam Masuk</th>
                                        <th className="py-2.5 px-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 text-xs">
                                    {latestCheckins.length === 0 ? (
                                        <tr>
                                            <td colSpan={4} className="py-8 text-center text-slate-400">
                                                Belum ada data check-in hari ini.
                                            </td>
                                        </tr>
                                    ) : (
                                        latestCheckins.map((chk) => (
                                            <tr key={chk.id} className="hover:bg-slate-50/80 transition">
                                                <td className="py-2.5 px-3.5">
                                                    <div className="flex items-center gap-2 min-w-[130px]">
                                                        <div className={cn(
                                                            "flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white",
                                                            chk.is_member ? "bg-red-600" : "bg-slate-700"
                                                        )}>
                                                            {chk.name.split(' ').map((n) => n[0]).slice(0, 2).join('').toUpperCase()}
                                                        </div>
                                                        <span className="font-bold text-slate-900 truncate">
                                                            {chk.name}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="py-2.5 px-3 whitespace-nowrap">
                                                    {chk.is_member ? (
                                                        <span className="inline-flex items-center rounded-full bg-red-50 border border-red-200/60 px-2 py-0.5 text-[10px] font-bold text-red-600">
                                                            Member
                                                        </span>
                                                    ) : (
                                                        <span className="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-700">
                                                            Daily Pass
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="py-2.5 px-3 whitespace-nowrap text-slate-600 font-medium">
                                                    {chk.time}
                                                </td>
                                                <td className="py-2.5 px-3 text-center whitespace-nowrap">
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200/50">
                                                        <CheckCircle2 className="h-3 w-3" />
                                                        {chk.status}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
