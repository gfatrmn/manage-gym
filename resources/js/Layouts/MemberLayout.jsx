import React, { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    Activity,
    AlertTriangle,
    Bell,
    CreditCard,
    Dumbbell,
    History,
    Home,
    LogOut,
    Package,
    QrCode,
    Receipt,
    User,
    ExternalLink,
} from 'lucide-react';
import { Button, FlashBanner, Modal, ThemeToggle } from '../Components/UI';
import { cn } from '../lib/format';

export default function MemberLayout({ title, active = 'dashboard', children }) {
    const { auth = {}, memberNotifications = {} } = usePage().props;
    const [showWaModal, setShowWaModal] = useState(
        Boolean(memberNotifications.showWhatsAppPrompt && memberNotifications.whatsAppChannelUrl)
    );

    const navItems = [
        { key: 'dashboard', label: 'Beranda', href: '/member/dashboard', icon: Home },
        { key: 'barcode', label: 'Kartu QR', href: '/member/barcode', icon: QrCode },
        { key: 'membership', label: 'Membership', href: '/member/membership', icon: CreditCard },
        { key: 'packages', label: 'Paket', href: '/member/packages', icon: Package },
        { key: 'payments', label: 'Riwayat Bayar', href: '/member/payments', icon: Receipt },
        { key: 'history', label: 'Kunjungan', href: '/member/history', icon: History },
        { key: 'statistics', label: 'Statistik', href: '/member/statistics', icon: Activity },
        {
            key: 'messages',
            label: 'Pesan',
            href: '/member/messages',
            icon: Bell,
            badge: memberNotifications.unreadAnnouncementsCount || 0,
        },
        { key: 'profile', label: 'Profil', href: '/member/profile', icon: User },
    ];

    const mobileNav = navItems.filter((i) =>
        ['dashboard', 'barcode', 'membership', 'payments', 'profile'].includes(i.key)
    );

    return (
        <div className="min-h-screen bg-slate-50 pb-20 text-slate-900 dark:bg-slate-950 dark:text-slate-100 md:pb-8">
            <Head title={title} />

            {/* Top Navbar */}
            <header className="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/90">
                <div className="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
                    <Link href="/member/dashboard" className="flex items-center gap-2.5">
                        <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-rose-600 to-orange-500 text-white shadow-sm">
                            <Dumbbell className="h-5 w-5" />
                        </div>
                        <div>
                            <p className="text-sm font-bold tracking-tight text-slate-900 dark:text-white">
                                ARENA MEMBER
                            </p>
                            <p className="text-[11px] text-slate-500 dark:text-slate-400">
                                Halo, {auth.name || 'Member'}
                            </p>
                        </div>
                    </Link>

                    <nav className="hidden items-center gap-1 md:flex">
                        {navItems.map((item) => {
                            const Icon = item.icon;
                            const isActive = active === item.key;
                            return (
                                <Link
                                    key={item.key}
                                    href={item.href}
                                    className={cn(
                                        'relative flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold transition',
                                        isActive
                                            ? 'bg-rose-600 text-white shadow-sm shadow-rose-500/20'
                                            : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                                    )}
                                >
                                    <Icon className="h-4 w-4" />
                                    <span>{item.label}</span>
                                    {item.badge > 0 && (
                                        <span className="ml-0.5 rounded-full bg-amber-400 px-1.5 py-0.2 text-[10px] font-bold text-slate-950">
                                            {item.badge}
                                        </span>
                                    )}
                                </Link>
                            );
                        })}
                    </nav>

                    <div className="flex items-center gap-2">
                        <ThemeToggle />
                        <button
                            type="button"
                            onClick={() => router.post('/member/logout')}
                            title="Keluar"
                            className="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-slate-800 dark:bg-slate-900 dark:text-rose-400 dark:hover:bg-rose-950/40"
                        >
                            <LogOut className="h-4 w-4" />
                            <span className="hidden sm:inline">Keluar</span>
                        </button>
                    </div>
                </div>
            </header>

            <main className="mx-auto max-w-6xl px-4 py-6 sm:px-6">
                {memberNotifications.isExpiringSoon && (
                    <div className="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-500/40 bg-amber-500/10 px-4 py-3 text-sm text-amber-900 dark:text-amber-200">
                        <div className="flex items-center gap-2.5">
                            <AlertTriangle className="h-5 w-5 shrink-0 text-amber-500" />
                            <span>
                                Masa aktif membership Anda tersisa{' '}
                                <strong>{memberNotifications.remainingDays} hari</strong> (berakhir{' '}
                                {memberNotifications.expiresAtFormatted}). Segera perpanjang di kasir agar tetap aktif!
                            </span>
                        </div>
                        <Link
                            href="/member/membership"
                            className="rounded-xl bg-amber-500 px-3 py-1.5 text-xs font-bold text-slate-950 hover:bg-amber-400"
                        >
                            Lihat Detail
                        </Link>
                    </div>
                )}

                <FlashBanner />
                {children}
            </main>

            {/* Mobile Bottom Navigation */}
            <nav className="fixed inset-x-0 bottom-0 z-30 flex items-center justify-around border-t border-slate-200 bg-white/95 px-2 py-2 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/95 md:hidden">
                {mobileNav.map((item) => {
                    const Icon = item.icon;
                    const isActive = active === item.key;
                    return (
                        <Link
                            key={item.key}
                            href={item.href}
                            className={cn(
                                'relative flex flex-col items-center gap-0.5 rounded-xl px-2.5 py-1 text-[10px] font-semibold',
                                isActive
                                    ? 'text-rose-600 dark:text-rose-400'
                                    : 'text-slate-500 dark:text-slate-400'
                            )}
                        >
                            <Icon className="h-5 w-5" />
                            <span>{item.label}</span>
                            {item.badge > 0 && (
                                <span className="absolute right-1 top-0.5 h-2 w-2 rounded-full bg-rose-500" />
                            )}
                        </Link>
                    );
                })}
            </nav>

            <Modal
                open={showWaModal}
                onClose={() => setShowWaModal(false)}
                title="Gabung Saluran WhatsApp Resmi Arena Gym"
                subtitle="Dapatkan info promo, jadwal maintenance, dan pengumuman latihan langsung di WhatsApp Anda."
            >
                <div className="space-y-4">
                    <div className="flex items-center justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowWaModal(false)}>
                            Nanti Saja
                        </Button>
                        <a
                            href={memberNotifications.whatsAppChannelUrl}
                            target="_blank"
                            rel="noreferrer"
                            onClick={() => setShowWaModal(false)}
                            className="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500"
                        >
                            Buka Saluran WhatsApp
                            <ExternalLink className="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </Modal>
        </div>
    );
}

