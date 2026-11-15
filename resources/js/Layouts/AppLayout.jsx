import React, { useState, useEffect, useRef } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    Activity,
    BarChart3,
    Building2,
    Camera,
    CreditCard,
    DollarSign,
    Dumbbell,
    FileText,
    FolderKanban,
    LayoutDashboard,
    LogOut,
    Megaphone,
    Menu,
    MessageSquare,
    Package,
    QrCode,
    Receipt,
    Shield,
    ShoppingBag,
    Ticket,
    User,
    UserCheck,
    UserCog,
    UserPlus,
    Users,
    Wallet,
    X,
} from 'lucide-react';
import { Badge, FlashBanner } from '../Components/UI';
import { cn } from '../lib/format';

export default function AppLayout({
    title,
    subtitle,
    panel = 'admin',
    active = 'dashboard',
    children,
}) {
    const pageProps = usePage().props || {};
    const auth = pageProps.auth || {};
    const adminBadges = pageProps.adminBadges || {};
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const adminSections = [
        {
            title: 'Utama',
            items: [
                { key: 'dashboard', label: 'Dashboard', href: '/admin', icon: LayoutDashboard },
            ],
        },
        {
            title: 'Kasir & Front-Office',
            items: [
                { key: 'cashier-checkins', label: 'Scan QR Check-in', href: '/cashier/checkins', icon: QrCode },
                { key: 'pos-products', label: 'POS Jual Produk', href: '/cashier/transactions/products', icon: ShoppingBag },
                { key: 'daily-passes', label: 'Tamu Harian (Daily Pass)', href: '/admin/daily-passes', icon: Ticket },
            ],
        },
        {
            title: 'Master Data',
            items: [
                { key: 'members', label: 'Data Member', href: '/admin/members', icon: Users },
                { key: 'packages', label: 'Paket Membership', href: '/admin/packages', icon: Package },
                { key: 'cashier-accounts', label: 'Akun Kasir', href: '/admin/cashier-accounts', icon: UserCog },
                { key: 'products', label: 'Produk & Kategori', href: '/admin/products', icon: ShoppingBag },
            ],
        },
        {
            title: 'Operasional',
            items: [
                { key: 'memberships', label: 'Status Membership', href: '/admin/memberships', icon: CreditCard },
                { key: 'transactions', label: 'Semua Transaksi', href: '/admin/transactions', icon: Receipt },
                { key: 'checkins', label: 'Absensi & Masuk', href: '/admin/checkins', icon: UserCheck },
            ],
        },
        {
            title: 'Keuangan & Laporan',
            items: [
                { key: 'expenses', label: 'Catat Pengeluaran', href: '/admin/expenses', icon: Wallet },
                { key: 'expense-categories', label: 'Kategori Biaya', href: '/admin/expense-categories', icon: FolderKanban },
                { key: 'reports', label: 'Laporan & Keuangan', href: '/admin/reports', icon: BarChart3 },
            ],
        },
        {
            title: 'Layanan & Log',
            items: [
                { key: 'announcements', label: 'Pengumuman & WA', href: '/admin/announcements', icon: Megaphone },
                {
                    key: 'profile-photo-requests',
                    label: 'Verifikasi Foto',
                    href: '/admin/profile-photo-requests',
                    icon: Camera,
                    badge: adminBadges?.pendingPhotoRequests || 0,
                },
                {
                    key: 'feedbacks',
                    label: 'Kritik & Saran',
                    href: '/admin/feedbacks',
                    icon: MessageSquare,
                    badge: adminBadges?.unreadFeedbacks || 0,
                },
                { key: 'settings', label: 'Profil Gym', href: '/admin/settings', icon: Building2 },
                { key: 'activity-logs', label: 'Activity Log', href: '/admin/activity-logs', icon: Activity },
            ],
        },
    ];

    const cashierSections = [
        {
            title: 'Utama',
            items: [
                { key: 'dashboard', label: 'Dashboard Kasir', href: '/cashier', icon: LayoutDashboard },
                { key: 'checkins', label: 'Scan QR Check-in', href: '/cashier/checkins', icon: QrCode },
            ],
        },
        {
            title: 'Layanan Member',
            items: [
                { key: 'members', label: 'Data Member', href: '/cashier/members', icon: Users },
                { key: 'packages', label: 'Paket Membership', href: '/cashier/packages', icon: Package },
            ],
        },
        {
            title: 'Transaksi & POS',
            items: [
                {
                    key: 'daily-payments',
                    label: 'Pembayaran Harian',
                    href: '/cashier/daily-payments',
                    icon: Ticket,
                },
                {
                    key: 'product-transactions',
                    label: 'POS Penjualan Produk',
                    href: '/cashier/transactions/products',
                    icon: ShoppingBag,
                },
                {
                    key: 'transactions',
                    label: 'Riwayat Transaksi',
                    href: '/cashier/transactions',
                    icon: Receipt,
                },
                { key: 'receipts', label: 'Cetak Struk', href: '/cashier/receipts', icon: FileText },
            ],
        },
    ];

    const menuSections = auth.role === 'cashier' ? cashierSections : adminSections;
    const navContainerRef = useRef(null);
    const activeItemRef = useRef(null);

    // Save scroll position on scroll so position persists on navigate
    useEffect(() => {
        const container = navContainerRef.current;
        if (!container) return;
        const handleScroll = () => {
            sessionStorage.setItem('arena_sidebar_scroll', String(container.scrollTop));
        };
        container.addEventListener('scroll', handleScroll, { passive: true });
        return () => container.removeEventListener('scroll', handleScroll);
    }, []);

    // Restore previous scroll position and scroll active item into view
    useEffect(() => {
        const savedScroll = sessionStorage.getItem('arena_sidebar_scroll');
        if (savedScroll !== null && navContainerRef.current) {
            navContainerRef.current.scrollTop = Number(savedScroll);
        }

        if (activeItemRef.current) {
            const timer = setTimeout(() => {
                activeItemRef.current?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'nearest',
                });
            }, 50);
            return () => clearTimeout(timer);
        }
    }, [active]);

    const handleLogout = () => {
        sessionStorage.removeItem('arena_sidebar_scroll');
        router.post('/logout');
    };

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
            <Head title={title} />

            {/* Mobile drawer backdrop */}
            {sidebarOpen && (
                <div
                    className="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-xs lg:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            {/* Sidebar */}
            <aside
                className={cn(
                    'fixed inset-y-0 left-0 z-50 flex h-full flex-col w-60 border-r border-slate-200 bg-white transition-transform duration-200 dark:border-slate-800 dark:bg-slate-900 lg:translate-x-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                )}
            >
                <div className="flex h-full min-h-0 flex-col">
                    <div className="flex shrink-0 items-center justify-between border-b border-slate-200 px-4 py-3.5">
                        <Link
                            href={auth.role === 'cashier' ? '/cashier' : '/admin'}
                            className="flex items-center gap-2.5"
                        >
                            <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-red-600 text-white shadow-md shadow-red-600/20">
                                <Dumbbell className="h-4.5 w-4.5" />
                            </div>
                            <div>
                                <p className="text-sm font-bold tracking-tight text-slate-900">
                                    ARENA GYM
                                </p>
                                <p className="text-[10px] font-bold uppercase tracking-wider text-red-600">
                                    {auth.role === 'cashier' ? 'Cashier POS' : 'Admin Console'}
                                </p>
                            </div>
                        </Link>
                        <button
                            type="button"
                            onClick={() => setSidebarOpen(false)}
                            className="rounded-lg p-1 text-slate-400 hover:bg-slate-100 lg:hidden"
                        >
                            <X className="h-5 w-5" />
                        </button>
                    </div>

                    <div
                        ref={navContainerRef}
                        scroll-region="true"
                        className="flex-1 min-h-0 overflow-y-auto p-2.5 overscroll-contain"
                    >
                        <nav className="space-y-3.5">
                            {menuSections.map((sec, secIdx) => (
                                <div key={secIdx} className="space-y-1">
                                    {sec.title && (
                                        <p className="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                            {sec.title}
                                        </p>
                                    )}
                                    {sec.items.map((item) => {
                                        const Icon = item.icon;
                                        const isMemberActive = item.key === 'members' && (active === 'members' || active === 'register-member' || active === 'member-payments');
                                        const isActive = active === item.key || isMemberActive;
                                        return (
                                            <Link
                                                key={item.key}
                                                ref={isActive ? activeItemRef : null}
                                                href={item.href}
                                                onClick={() => {
                                                    if (navContainerRef.current) {
                                                        sessionStorage.setItem(
                                                            'arena_sidebar_scroll',
                                                            String(navContainerRef.current.scrollTop)
                                                        );
                                                    }
                                                    setSidebarOpen(false);
                                                }}
                                                className={cn(
                                                    'group flex items-center justify-between gap-2 rounded-xl px-2.5 py-2 text-xs font-semibold transition scroll-my-6',
                                                    isActive
                                                        ? 'bg-red-600 text-white shadow-sm shadow-red-600/20'
                                                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                                )}
                                            >
                                                <span className="flex items-center gap-2.5 min-w-0">
                                                    <Icon
                                                        className={cn(
                                                            'h-4 w-4 shrink-0',
                                                            isActive
                                                                ? 'text-white'
                                                                : 'text-slate-500 group-hover:text-slate-900'
                                                        )}
                                                    />
                                                    <span className="truncate">{item.label}</span>
                                                </span>
                                                {item.badge > 0 && (
                                                    <span
                                                        className={cn(
                                                            'shrink-0 rounded-full px-1.5 py-0.5 text-[10px] font-bold',
                                                            isActive
                                                                ? 'bg-white/25 text-white'
                                                                : 'bg-red-50 text-red-600'
                                                        )}
                                                    >
                                                        {item.badge}
                                                    </span>
                                                )}
                                            </Link>
                                        );
                                    })}
                                </div>
                            ))}
                        </nav>
                    </div>

                    <div className="shrink-0 border-t border-slate-200 p-3">
                        <button
                            type="button"
                            onClick={handleLogout}
                            className="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50/60 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                        >
                            <LogOut className="h-4 w-4" />
                            Keluar Sistem
                        </button>
                    </div>
                </div>
            </aside>

            {/* Main area */}
            <div className="flex min-h-screen flex-col lg:pl-60">
                <header className="sticky top-0 z-30 border-b border-slate-200/80 bg-white/85 px-4 py-3 backdrop-blur-md sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex min-w-0 items-center gap-3">
                            <button
                                type="button"
                                onClick={() => setSidebarOpen(true)}
                                className="rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                            >
                                <Menu className="h-5 w-5" />
                            </button>
                            <div className="min-w-0">
                                <h1 className="truncate text-base sm:text-lg font-bold tracking-tight text-slate-900">
                                    {title}
                                </h1>
                            </div>
                        </div>

                        <div className="flex items-center gap-2">
                            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-xs">
                                <User className="h-4 w-4" />
                            </div>
                            <span className="text-xs font-bold uppercase tracking-wider text-slate-800">
                                {auth.role === 'master_admin' ? 'Master Admin' : (auth.role === 'cashier' ? 'Kasir' : 'Admin')}
                            </span>
                        </div>
                    </div>
                </header>

                <main className="flex-1 p-4 sm:p-6 lg:p-8">
                    <FlashBanner />
                    {children}
                </main>
            </div>
        </div>
    );
}

