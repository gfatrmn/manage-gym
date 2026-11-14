import React, { useState } from 'react';
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
                { key: 'daily-passes', label: 'Tamu Harian', href: '/admin/daily-passes', icon: Ticket },
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
                {
                    key: 'register-member',
                    label: 'Pendaftaran Member',
                    href: '/cashier/transactions/register-member',
                    icon: UserPlus,
                },
                { key: 'members', label: 'Data Member', href: '/cashier/members', icon: Users },
                { key: 'packages', label: 'Paket Membership', href: '/cashier/packages', icon: Package },
                {
                    key: 'member-payments',
                    label: 'Perpanjangan Member',
                    href: '/cashier/member-payments',
                    icon: CreditCard,
                },
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

    const menuSections = panel === 'cashier' ? cashierSections : adminSections;

    const handleLogout = () => {
        router.post('/logout');
    };

    const SidebarContent = () => (
        <div className="flex h-full min-h-0 flex-col">
            <div className="flex shrink-0 items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <Link
                    href={panel === 'cashier' ? '/cashier' : '/admin'}
                    className="flex items-center gap-3"
                >
                    <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white shadow-md shadow-red-600/20">
                        <Dumbbell className="h-5 w-5" />
                    </div>
                    <div>
                        <p className="text-sm font-bold tracking-tight text-slate-900">
                            ARENA GYM
                        </p>
                        <p className="text-[11px] font-medium uppercase tracking-wider text-red-600">
                            {panel === 'cashier' ? 'Cashier POS' : 'Admin Console'}
                        </p>
                    </div>
                </Link>
                <button
                    type="button"
                    onClick={() => setSidebarOpen(false)}
                    className="rounded-lg p-1 text-slate-400 hover:bg-slate-100 lg:hidden dark:hover:bg-slate-800"
                >
                    <X className="h-5 w-5" />
                </button>
            </div>

            <div className="flex-1 min-h-0 overflow-y-auto p-3 overscroll-contain">
                <nav className="space-y-4">
                    {menuSections.map((sec, secIdx) => (
                        <div key={secIdx} className="space-y-1">
                            {sec.title && (
                                <p className="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    {sec.title}
                                </p>
                            )}
                            {sec.items.map((item) => {
                                const Icon = item.icon;
                                const isActive = active === item.key;
                                return (
                                    <Link
                                        key={item.key}
                                        href={item.href}
                                        onClick={() => setSidebarOpen(false)}
                                        className={cn(
                                            'flex items-center justify-between gap-3 rounded-xl px-3 py-2 text-xs font-semibold transition',
                                            isActive
                                                ? 'bg-red-600 text-white shadow-sm shadow-red-600/20'
                                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                        )}
                                    >
                                        <span className="flex items-center gap-2.5 truncate">
                                            <Icon className="h-4 w-4 shrink-0" />
                                            <span className="truncate">{item.label}</span>
                                        </span>
                                        {item.badge > 0 && (
                                            <span
                                                className={cn(
                                                    'rounded-full px-2 py-0.5 text-[10px] font-bold',
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

            <div className="shrink-0 border-t border-slate-200 p-4">
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
    );

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
                    'fixed inset-y-0 left-0 z-50 flex h-full flex-col w-68 border-r border-slate-200 bg-white transition-transform duration-200 dark:border-slate-800 dark:bg-slate-900 lg:translate-x-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                )}
            >
                <SidebarContent />
            </aside>

            {/* Main area */}
            <div className="flex min-h-screen flex-col lg:pl-68">
                <header className="sticky top-0 z-30 border-b border-slate-200/80 bg-white/85 px-4 py-3.5 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/85 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex min-w-0 items-center gap-3">
                            <button
                                type="button"
                                onClick={() => setSidebarOpen(true)}
                                className="rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 lg:hidden dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            >
                                <Menu className="h-5 w-5" />
                            </button>
                            <div className="min-w-0">
                                <h1 className="truncate text-lg font-bold tracking-tight text-slate-900 dark:text-white sm:text-xl">
                                    {title}
                                </h1>
                                {subtitle && (
                                    <p className="truncate text-xs text-slate-500 dark:text-slate-400">
                                        {subtitle}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            {auth.role === 'master_admin' && (
                                <Link
                                    href={panel === 'admin' ? '/cashier' : '/admin'}
                                    className="hidden sm:inline-flex"
                                >
                                    <span className="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100 transition">
                                        {panel === 'admin' ? 'Buka Kasir POS' : 'Buka Admin'}
                                    </span>
                                </Link>
                            )}

                            {/* User Role Card with Icon on Top Right */}
                            <div className="flex items-center gap-2.5 rounded-xl border border-red-200/90 bg-red-50/70 px-3 py-1.5 shadow-xs">
                                <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-red-600 text-white shadow-xs">
                                    <User className="h-4 w-4" />
                                </div>
                                <div className="text-left">
                                    <p className="truncate max-w-[120px] sm:max-w-[200px] text-xs font-bold text-slate-900 leading-tight">
                                        {auth.name || 'Staff Arena Gym'}
                                    </p>
                                    <div className="flex items-center gap-1 mt-0.5">
                                        <span className="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wider text-red-700 leading-none">
                                            <Shield className="h-3 w-3 text-red-600" />
                                            {auth.role || panel}
                                        </span>
                                    </div>
                                </div>
                            </div>
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

