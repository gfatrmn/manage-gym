import React, { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import {
    Activity,
    AlertTriangle,
    ArrowUpRight,
    DollarSign,
    TrendingDown,
    TrendingUp,
    UserCheck,
    Users,
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
const STAT_ACCENTS = ['rose', 'emerald', 'amber', 'sky'];

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
            subtitle="Ringkasan operasional, keuangan harian, dan pemantauan keanggotaan Arena Gym"
            panel="admin"
            active="dashboard"
        >
            {/* Hero banner */}
            <Card className="mb-6 bg-gradient-to-br from-slate-900 via-slate-900 to-rose-950 text-white dark:border-slate-800">
                <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                    <div>
                        <Badge variant="danger" className="bg-rose-500/20 text-rose-200 ring-rose-400/30">
                            Arena Gym Management System
                        </Badge>
                        <h2 className="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">
                            Kontrol Operasional & Membership Terpadu
                        </h2>
                        <p className="mt-1 max-w-2xl text-xs text-slate-300 sm:text-sm">
                            Catat kunjungan member & tamu harian, kelola pengingat jatuh tempo, dan pantau laporan keuangan real-time.
                        </p>
                        <div className="mt-4 flex flex-wrap gap-2.5">
                            <Button onClick={() => setShowCheckinModal(true)} size="sm">
                                <UserCheck className="h-4 w-4" />
                                Catat Check-in Member
                            </Button>
                            <Button
                                variant="secondary"
                                size="sm"
                                onClick={() => setShowDailyPassModal(true)}
                            >
                                + Tamu Harian (Daily Pass)
                            </Button>
                            <Link
                                href="/admin/reports"
                                className="inline-flex items-center gap-1.5 rounded-xl bg-white/10 px-3.5 py-2 text-xs font-semibold text-white hover:bg-white/20"
                            >
                                Buka Laporan Keuangan
                                <ArrowUpRight className="h-4 w-4" />
                            </Link>
                        </div>
                    </div>

                    <div className="grid gap-3 sm:grid-cols-2">
                        {heroSummary.map((item, idx) => (
                            <div
                                key={idx}
                                className="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur"
                            >
                                <p className="text-[11px] font-semibold uppercase tracking-wider text-slate-300">
                                    {item.label}
                                </p>
                                <p className="mt-1 text-lg font-extrabold text-white">{item.value}</p>
                                <p className="text-[11px] text-slate-400">{item.note}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </Card>

            {/* Primary Stats */}
            <div className="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {stats.map((stat, idx) => (
                    <StatCard
                        key={idx}
                        title={stat.label}
                        value={stat.value}
                        subtitle={stat.note}
                        icon={STAT_ICONS[idx] || Activity}
                        accent={STAT_ACCENTS[idx] || 'rose'}
                    />
                ))}
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
                                <AlertTriangle className="h-4 w-4 text-amber-500" />
                                Segera Berakhir (≤ 7 Hari)
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400">
                                Prioritas follow-up perpanjangan membership
                            </p>
                        </div>
                        <Link
                            href="/admin/announcements"
                            className="text-xs font-semibold text-rose-600 hover:underline dark:text-rose-400"
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
                                    <Badge variant={m.days_left <= 2 ? 'danger' : 'warning'}>
                                        {m.days_left} hari lagi
                                    </Badge>
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

