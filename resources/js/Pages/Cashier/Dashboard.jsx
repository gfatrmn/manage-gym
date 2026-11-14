import React from 'react';
import { Link, router } from '@inertiajs/react';
import {
    CheckCircle2,
    Clock,
    CreditCard,
    DollarSign,
    Printer,
    QrCode,
    Receipt,
    ShoppingBag,
    Ticket,
    UserPlus,
    Zap,
} from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, StatCard } from '../../Components/UI';

const STAT_ICONS = [CreditCard, Ticket, Receipt, DollarSign];
const STAT_ACCENTS = ['rose', 'sky', 'rose', 'emerald'];

export default function Dashboard({
    cashierShift = {},
    cashierStats = [],
    cashierPendingCheckins = [],
    recentPendingQris = [],
    paymentMethods = [],
}) {
    return (
        <AppLayout
            title="Dashboard Kasir POS"
            subtitle={`Shift Aktif: ${cashierShift.label || '08:00 - 16:00'} · Arena Gym Front-Office`}
            panel="cashier"
            active="dashboard"
        >
            {/* Quick POS Action Bar */}
            <Card className="mb-6 p-4">
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <h2 className="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                            <Zap className="h-4 w-4 text-red-600" />
                            Terminal Kasir & Check-in
                        </h2>
                        <p className="text-xs text-slate-500">
                            Shift Aktif: {cashierShift.label || '08:00 - 16:00'} · Akses cepat operasional front-desk
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Link
                            href="/cashier/checkins"
                            className="inline-flex items-center gap-1.5 rounded-xl bg-red-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-red-700 shadow-sm shadow-red-600/20"
                        >
                            <QrCode className="h-4 w-4" />
                            Scan Check-in
                        </Link>
                        <Link
                            href="/cashier/transactions/products"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs"
                        >
                            <ShoppingBag className="h-4 w-4 text-red-600" />
                            POS Produk
                        </Link>
                        <Link
                            href="/cashier/member-payments"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs"
                        >
                            <CreditCard className="h-4 w-4 text-red-600" />
                            Perpanjang Member
                        </Link>
                        <Link
                            href="/cashier/daily-payments"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs"
                        >
                            <Ticket className="h-4 w-4 text-red-600" />
                            Daily Pass
                        </Link>
                        <Link
                            href="/cashier/transactions/register-member"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs"
                        >
                            <UserPlus className="h-4 w-4 text-emerald-600" />
                            Daftar Member Baru
                        </Link>
                    </div>
                </div>
            </Card>

            {/* KPIs */}
            <div className="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {cashierStats.map((stat, idx) => (
                    <StatCard
                        key={idx}
                        title={stat.label}
                        value={stat.value}
                        subtitle={stat.change}
                        icon={STAT_ICONS[idx] || Receipt}
                        accent={STAT_ACCENTS[idx] || 'rose'}
                    />
                ))}
            </div>

            <div className="grid gap-6 lg:grid-cols-3">
                {/* Pending Check-in Verification */}
                <Card>
                    <div className="mb-4 flex items-center justify-between">
                        <h3 className="flex items-center gap-2 text-base font-bold text-slate-900">
                            <Clock className="h-4 w-4 text-red-600" />
                            Antrean Check-in QR
                        </h3>
                        <Link
                            href="/cashier/checkins"
                            className="text-xs font-semibold text-red-600 hover:underline"
                        >
                            Kelola
                        </Link>
                    </div>
                    {cashierPendingCheckins.length === 0 ? (
                        <p className="py-8 text-center text-xs text-slate-500">
                            Tidak ada check-in pending saat ini.
                        </p>
                    ) : (
                        <div className="space-y-3">
                            {cashierPendingCheckins.map((item) => (
                                <div
                                    key={item.id}
                                    className="rounded-xl border border-slate-200 p-3 dark:border-slate-800"
                                >
                                    <p className="font-bold text-slate-900 dark:text-white">
                                        {item.member?.full_name || item.submitted_name || '-'}
                                    </p>
                                    <p className="text-xs text-slate-500">
                                        Kode: {item.member?.checkin_code || '-'}
                                    </p>
                                    <div className="mt-2.5 flex gap-2">
                                        <Button
                                            variant="success"
                                            size="xs"
                                            onClick={() => router.post(`/cashier/checkins/${item.id}/verify`)}
                                        >
                                            <CheckCircle2 className="h-3.5 w-3.5" />
                                            Verifikasi
                                        </Button>
                                        <Button
                                            variant="danger"
                                            size="xs"
                                            onClick={() => router.post(`/cashier/checkins/${item.id}/reject`)}
                                        >
                                            Tolak
                                        </Button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>

                {/* Pending QRIS Payments */}
                <Card>
                    <div className="mb-4 flex items-center justify-between">
                        <h3 className="text-base font-bold text-slate-900 dark:text-white">
                            QRIS Menunggu Verifikasi
                        </h3>
                        <Link
                            href="/cashier/receipts"
                            className="text-xs font-semibold text-rose-600 hover:underline dark:text-rose-400"
                        >
                            Semua Struk
                        </Link>
                    </div>
                    {recentPendingQris.length === 0 ? (
                        <p className="py-8 text-center text-xs text-slate-500">
                            Semua pembayaran QRIS sudah terverifikasi.
                        </p>
                    ) : (
                        <div className="space-y-3">
                            {recentPendingQris.map((trx) => (
                                <div
                                    key={trx.id}
                                    className="rounded-xl border border-slate-200 p-3 dark:border-slate-800"
                                >
                                    <div className="flex items-start justify-between gap-2">
                                        <div>
                                            <p className="font-mono text-xs font-bold text-rose-600 dark:text-rose-400">
                                                {trx.invoice}
                                            </p>
                                            <p className="text-sm font-bold text-slate-900 dark:text-white">
                                                {trx.customer_name}
                                            </p>
                                            <p className="text-xs text-slate-500">
                                                {trx.transaction_type} · Rp{' '}
                                                {Number(trx.amount || 0).toLocaleString('id-ID')}
                                            </p>
                                        </div>
                                        <Badge variant="warning">Pending</Badge>
                                    </div>
                                    <div className="mt-2.5 flex gap-2">
                                        <Button
                                            variant="success"
                                            size="xs"
                                            onClick={() =>
                                                router.post(`/cashier/verifications/${trx.id}`, {
                                                    return_to: 'dashboard',
                                                })
                                            }
                                        >
                                            Konfirmasi Lunas
                                        </Button>
                                        <Link
                                            href={`/cashier/receipts/${trx.invoice}/print`}
                                            className="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold dark:border-slate-700"
                                        >
                                            <Printer className="h-3.5 w-3.5" />
                                            Struk
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>

                {/* Payment method breakdown */}
                <Card>
                    <h3 className="mb-4 text-base font-bold text-slate-900 dark:text-white">
                        Komposisi Pembayaran Hari Ini
                    </h3>
                    {paymentMethods.length === 0 ? (
                        <p className="py-8 text-center text-xs text-slate-500">
                            Belum ada pembayaran terverifikasi hari ini.
                        </p>
                    ) : (
                        <div className="space-y-4">
                            {paymentMethods.map((pm, idx) => (
                                <div key={idx}>
                                    <div className="mb-1 flex items-center justify-between text-xs font-semibold">
                                        <span className="text-slate-700 dark:text-slate-200">{pm.label}</span>
                                        <span className="text-slate-900 dark:text-white">{pm.value}</span>
                                    </div>
                                    <div className="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div
                                            style={{ width: `${pm.progress || 0}%` }}
                                            className="h-full rounded-full bg-red-600"
                                        />
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>
            </div>
        </AppLayout>
    );
}

