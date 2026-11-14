import React, { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import {
    Activity,
    AlertTriangle,
    ArrowUpRight,
    CreditCard,
    DollarSign,
    MessageSquare,
    ShoppingBag,
    Ticket,
    TrendingDown,
    TrendingUp,
    UserCheck,
    Users,
    Zap,
} from 'lucide-react';
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
} from '../../Components/UI';

const STAT_ICONS = [Users, DollarSign, TrendingDown, TrendingUp];
const STAT_ACCENTS = ['rose', 'emerald', 'rose', 'sky'];

export default function Dashboard({
    stats = [],
    heroSummary = [],
    recentMembers = [],
    expiringMembers = [],
    memberOptions = [],
    paymentMethods = ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'],
}) {
    const [showCheckinModal, setShowCheckinModal] = useState(false);
    const [showDailyPassModal, setShowDailyPassModal] = useState(false);

    const checkinForm = useForm({
        gym_member_id: memberOptions[0]?.id || '',
        notes: '',
    });

    const dailyPassForm = useForm({
        name: '',
        phone: '',
        price: 30000,
        payment_method: paymentMethods[0] || 'Cash',
    });

    const submitCheckin = (e) => {
        e.preventDefault();
        checkinForm.post('/admin/checkins', {
            onSuccess: () => {
                checkinForm.reset('notes');
                setShowCheckinModal(false);
            },
        });
    };

    const submitDailyPass = (e) => {
        e.preventDefault();
        dailyPassForm.post('/admin/checkins/daily-pass', {
            onSuccess: () => {
                dailyPassForm.reset('name', 'phone');
                setShowDailyPassModal(false);
            },
        });
    };

    return (
        <AppLayout
            title="Dashboard Admin"
            panel="admin"
            active="dashboard"
        >
            {/* Quick Actions Strip */}
            <Card className="mb-6 p-4">
                <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 className="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                            <Zap className="h-4 w-4 text-red-600" />
                            Aksi Cepat Front-Office
                        </h2>
                        <p className="text-xs text-slate-500">
                            Pencatatan check-in cepat, tamu harian, POS penjualan produk, dan perpanjangan
                        </p>
                    </div>
                    <div className="flex flex-wrap items-center gap-2">
                        <Button onClick={() => setShowCheckinModal(true)} size="sm">
                            <UserCheck className="h-4 w-4" />
                            Check-in Member
                        </Button>
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={() => setShowDailyPassModal(true)}
                        >
                            <Ticket className="h-4 w-4" />
                            + Tamu Harian
                        </Button>
                        <Link
                            href="/cashier/transactions/products"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs"
                        >
                            <ShoppingBag className="h-4 w-4 text-red-600" />
                            POS Jual Produk
                        </Link>
                        <Link
                            href="/cashier/member-payments"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs"
                        >
                            <CreditCard className="h-4 w-4 text-red-600" />
                            Perpanjangan Paket
                        </Link>
                        <Link
                            href="/admin/reports"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs"
                        >
                            <ArrowUpRight className="h-4 w-4 text-slate-400" />
                            Laporan Keuangan
                        </Link>
                    </div>
                </div>
            </Card>

            {/* Primary Stats Grid */}
            <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title={stats[0]?.label || 'Total Member Aktif'}
                    value={stats[0]?.value || '0'}
                    subtitle={stats[0]?.note || 'Status member aktif'}
                    icon={Users}
                    accent="rose"
                />
                <StatCard
                    title={stats[1]?.label || 'Pemasukan Hari Ini'}
                    value={stats[1]?.value || 'Rp 0'}
                    subtitle={stats[1]?.note || 'Kasir & Daily Pass'}
                    icon={DollarSign}
                    accent="emerald"
                />
                <StatCard
                    title="Aktivitas Hari Ini"
                    value={heroSummary[0]?.value || '0 Kunjungan'}
                    subtitle={heroSummary[0]?.note || 'Check-in & Tamu Harian'}
                    icon={Activity}
                    accent="sky"
                />
                <StatCard
                    title="Jatuh Tempo (≤ 7 Hari)"
                    value={heroSummary[1]?.value || '0 Jatuh Tempo'}
                    subtitle="Perlu follow-up perpanjangan"
                    icon={AlertTriangle}
                    accent="rose"
                />
            </div>

            {/* Two-column lists */}
            <div className="grid gap-6 lg:grid-cols-2">
                <Card>
                    <div className="mb-4 flex items-center justify-between">
                        <div>
                            <h3 className="text-base font-bold text-slate-900 dark:text-white">
                                Member Terbaru
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400">
                                5 pendaftaran keanggotaan terakhir
                            </p>
                        </div>
                        <Link
                            href="/admin/members"
                            className="text-xs font-semibold text-rose-600 hover:underline dark:text-rose-400"
                        >
                            Lihat Semua
                        </Link>
                    </div>

                    {recentMembers.length === 0 ? (
                        <p className="py-8 text-center text-xs text-slate-500">
                            Belum ada member terdaftar.
                        </p>
                    ) : (
                        <div className="divide-y divide-slate-100 dark:divide-slate-800">
                            {recentMembers.map((m) => (
                                <div key={m.id} className="flex items-center justify-between gap-3 py-3">
                                    <div className="min-w-0">
                                        <p className="truncate text-sm font-semibold text-slate-900 dark:text-white">
                                            {m.full_name}
                                        </p>
                                        <p className="text-xs text-slate-500 dark:text-slate-400">
                                            {m.phone} · Kode: <span className="font-mono">{m.checkin_code}</span>
                                        </p>
                                    </div>
                                    <div className="text-right text-xs">
                                        <Badge variant="success">Aktif</Badge>
                                        <p className="mt-1 text-[11px] text-slate-400">
                                            s/d {m.expires_at}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>

                <Card>
                    <div className="mb-4 flex items-center justify-between">
                        <div>
                            <h3 className="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                                <AlertTriangle className="h-4 w-4 text-red-600" />
                                Segera Berakhir (≤ 7 Hari)
                            </h3>
                            <p className="text-xs text-slate-500">
                                Prioritas follow-up perpanjangan membership
                            </p>
                        </div>
                        <Link
                            href="/admin/announcements"
                            className="text-xs font-semibold text-red-600 hover:underline"
                        >
                            Kirim Pengingat
                        </Link>
                    </div>

                    {expiringMembers.length === 0 ? (
                        <p className="py-8 text-center text-xs text-slate-500">
                            Tidak ada member yang mendekati masa kedaluwarsa saat ini.
                        </p>
                    ) : (
                        <div className="divide-y divide-slate-100 dark:divide-slate-800">
                            {expiringMembers.map((m) => (
                                <div key={m.id} className="flex items-center justify-between gap-3 py-3">
                                    <div className="min-w-0">
                                        <p className="truncate text-sm font-semibold text-slate-900 dark:text-white">
                                            {m.full_name}
                                        </p>
                                        <p className="text-xs text-slate-500 dark:text-slate-400">
                                            {m.phone} · Terakhir diingatkan: {m.last_reminder}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2 shrink-0">
                                        {m.phone && (
                                            <a
                                                href={`https://wa.me/${m.phone.replace(/^0/, '62').replace(/\D/g, '')}?text=${encodeURIComponent(`Halo ${m.full_name}, pengingat membership Arena Gym Anda tersisa ${m.days_left} hari lagi. Silakan perpanjang di meja kasir kami agar latihan tetap lancar. Terima kasih!`)}`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700 hover:bg-emerald-100 transition"
                                                title="Kirim Pengingat WhatsApp"
                                            >
                                                <MessageSquare className="h-3 w-3" />
                                                Kirim WA
                                            </a>
                                        )}
                                        <Badge variant={m.days_left <= 2 ? 'danger' : 'warning'}>
                                            {m.days_left} hari lagi
                                        </Badge>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>
            </div>

            {/* Modal Check-in Member */}
            <Modal
                open={showCheckinModal}
                onClose={() => setShowCheckinModal(false)}
                title="Catat Check-in Member"
                subtitle="Catat kunjungan member aktif secara langsung"
            >
                <form onSubmit={submitCheckin} className="space-y-4">
                    <div>
                        <InputLabel required>Pilih Member Aktif</InputLabel>
                        <Select
                            value={checkinForm.data.gym_member_id}
                            onChange={(e) => checkinForm.setData('gym_member_id', e.target.value)}
                        >
                            <option value="">-- Pilih Member --</option>
                            {memberOptions.map((m) => (
                                <option key={m.id} value={m.id}>
                                    {m.full_name} ({m.checkin_code})
                                </option>
                            ))}
                        </Select>
                        <FieldError message={checkinForm.errors.gym_member_id} />
                    </div>
                    <div>
                        <InputLabel>Catatan (Opsional)</InputLabel>
                        <Input
                            value={checkinForm.data.notes}
                            onChange={(e) => checkinForm.setData('notes', e.target.value)}
                            placeholder="Catatan check-in..."
                        />
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowCheckinModal(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={checkinForm.processing}>
                            Simpan Check-in
                        </Button>
                    </div>
                </form>
            </Modal>

            {/* Modal Daily Pass */}
            <Modal
                open={showDailyPassModal}
                onClose={() => setShowDailyPassModal(false)}
                title="Catat Tamu Harian (Daily Pass)"
                subtitle="Input tamu non-member yang membayar tiket harian"
            >
                <form onSubmit={submitDailyPass} className="space-y-4">
                    <div>
                        <InputLabel required>Nama Lengkap Tamu</InputLabel>
                        <Input
                            value={dailyPassForm.data.name}
                            onChange={(e) => dailyPassForm.setData('name', e.target.value)}
                            error={dailyPassForm.errors.name}
                        />
                        <FieldError message={dailyPassForm.errors.name} />
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel>No. HP (Opsional)</InputLabel>
                            <Input
                                value={dailyPassForm.data.phone}
                                onChange={(e) => dailyPassForm.setData('phone', e.target.value)}
                            />
                        </div>
                        <div>
                            <InputLabel required>Nominal (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={dailyPassForm.data.price}
                                onChange={(e) => dailyPassForm.setData('price', e.target.value)}
                            />
                        </div>
                    </div>
                    <div>
                        <InputLabel required>Metode Pembayaran</InputLabel>
                        <Select
                            value={dailyPassForm.data.payment_method}
                            onChange={(e) => dailyPassForm.setData('payment_method', e.target.value)}
                        >
                            {paymentMethods.map((pm) => (
                                <option key={pm} value={pm}>
                                    {pm}
                                </option>
                            ))}
                        </Select>
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowDailyPassModal(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={dailyPassForm.processing}>
                            Simpan Daily Pass
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}

