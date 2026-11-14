import React from 'react';
import { Link } from '@inertiajs/react';
import { Activity, Bell, CalendarDays, CreditCard, Flame, QrCode, User } from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import { Badge, Card, StatCard } from '../../Components/UI';
import { formatDate } from '../../lib/format';

export default function Dashboard({
    user,
    member,
    totalCheckins = 0,
    thisMonthCheckins = 0,
    remainingDays = null,
    membershipStatus = 'member',
}) {
    const quickLinks = [
        {
            title: 'Kartu & QR Check-in',
            desc: 'Tunjukkan QR pribadi Anda di meja kasir',
            href: '/member/barcode',
            icon: QrCode,
            color: 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
        },
        {
            title: 'Riwayat Latihan',
            desc: 'Lihat log kehadiran dan konsistensi 30 hari',
            href: '/member/history',
            icon: CalendarDays,
            color: 'bg-sky-500/10 text-sky-600 dark:text-sky-400',
        },
        {
            title: 'Statistik Performa',
            desc: 'Grafik mingguan & bulanan kehadiran gym',
            href: '/member/statistics',
            icon: Activity,
            color: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
        },
        {
            title: 'Info Membership',
            desc: 'Cek masa aktif & riwayat pembayaran',
            href: '/member/membership',
            icon: CreditCard,
            color: 'bg-violet-500/10 text-violet-600 dark:text-violet-400',
        },
        {
            title: 'Pesan & Pengumuman',
            desc: 'Pesan resmi dari admin & pengelola gym',
            href: '/member/messages',
            icon: Bell,
            color: 'bg-red-50 text-red-600',
        },
        {
            title: 'Profil & Kritik Saran',
            desc: 'Kelola foto profil, kontak, dan kirim masukan',
            href: '/member/profile',
            icon: User,
            color: 'bg-slate-500/10 text-slate-600',
        },
    ];

    return (
        <MemberLayout title="Dashboard Member" active="dashboard">
            {/* Profile Quick Strip */}
            <Card className="mb-6 p-4 sm:p-5">
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex items-center gap-3.5">
                        {member?.profile_photo_url ? (
                            <img
                                src={member.profile_photo_url}
                                alt={user?.name}
                                className="h-14 w-14 rounded-2xl border-2 border-red-500/20 object-cover shadow-xs"
                            />
                        ) : (
                            <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-600 text-lg font-extrabold text-white shadow-xs">
                                {member?.profile_initials || 'AG'}
                            </div>
                        )}
                        <div>
                            <div className="flex flex-wrap items-center gap-2">
                                <h1 className="text-lg font-bold sm:text-xl text-slate-900">
                                    {member?.name || user?.name}
                                </h1>
                                <Badge variant="success">{membershipStatus}</Badge>
                            </div>
                            <p className="mt-0.5 text-xs text-slate-500">
                                Kode Check-in: <span className="font-mono font-bold text-red-600">{member?.checkin_code || '-'}</span> · Berakhir{' '}
                                {member?.expires_at ? formatDate(member.expires_at) : '-'}
                            </p>
                        </div>
                    </div>

                    <Link
                        href="/member/barcode"
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm shadow-red-600/30 transition hover:bg-red-700"
                    >
                        <QrCode className="h-4 w-4" />
                        Buka QR Check-in
                    </Link>
                </div>
            </Card>

            {/* Stats */}
            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard
                    title="Total Check-in"
                    value={totalCheckins}
                    subtitle="Kehadiran terverifikasi"
                    icon={Flame}
                    accent="rose"
                />
                <StatCard
                    title="Latihan Bulan Ini"
                    value={thisMonthCheckins}
                    subtitle="Sesi latihan bulan berjalan"
                    icon={Activity}
                    accent="emerald"
                />
                <StatCard
                    title="Sisa Masa Aktif"
                    value={remainingDays !== null ? `${remainingDays} Hari` : '-'}
                    subtitle={member?.expires_at ? `Aktif s/d ${formatDate(member.expires_at)}` : 'Status membership'}
                    icon={CreditCard}
                    accent={remainingDays !== null && remainingDays <= 7 ? 'rose' : 'sky'}
                />
            </div>

            {/* Quick Navigation Grid */}
            <h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                Menu Cepat Member
            </h2>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {quickLinks.map((item) => {
                    const Icon = item.icon;
                    return (
                        <Link
                            key={item.href}
                            href={item.href}
                            className="group rounded-2xl border border-slate-200/80 bg-white p-5 transition hover:-translate-y-0.5 hover:border-rose-500/40 hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
                        >
                            <div className={`mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl ${item.color}`}>
                                <Icon className="h-5 w-5" />
                            </div>
                            <h3 className="text-base font-bold text-slate-900 group-hover:text-rose-600 dark:text-white dark:group-hover:text-rose-400">
                                {item.title}
                            </h3>
                            <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {item.desc}
                            </p>
                        </Link>
                    );
                })}
            </div>
        </MemberLayout>
    );
}

