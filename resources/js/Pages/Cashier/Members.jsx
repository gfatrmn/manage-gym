import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import {
    Calendar,
    CheckCircle2,
    Clock,
    CreditCard,
    Filter,
    QrCode,
    Search,
    User,
    UserCheck,
    UserPlus,
    XCircle,
} from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    Input,
    Pagination,
} from '../../Components/UI';

export default function Members({ members, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || 'all');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/cashier/members', { q: search, status: statusFilter }, { preserveState: true });
    };

    const handleStatusFilter = (st) => {
        setStatusFilter(st);
        router.get('/cashier/members', { q: search, status: st }, { preserveState: true });
    };

    return (
        <AppLayout
            title="Data Member & Status"
            subtitle="Pencarian data member, masa aktif, dan proses perpanjangan cepat di meja kasir"
            active="members"
            panel="cashier"
        >
            <Head title="Data Member & Status" />

            {/* Header / Filter */}
            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <form onSubmit={handleSearch} className="flex flex-1 items-center gap-2 max-w-md">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            placeholder="Cari nama, ID barcode, HP, atau email..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="pl-9"
                        />
                    </div>
                    <Button type="submit" variant="secondary" size="md">
                        Cari
                    </Button>
                </form>

                <div className="flex flex-wrap items-center gap-2">
                    <div className="inline-flex rounded-xl border border-slate-200 bg-white p-1 dark:border-slate-800 dark:bg-slate-900">
                        {[
                            { key: 'all', label: 'Semua' },
                            { key: 'active', label: 'Aktif' },
                            { key: 'expired', label: 'Expired' },
                        ].map((item) => (
                            <button
                                key={item.key}
                                type="button"
                                onClick={() => handleStatusFilter(item.key)}
                                className={`rounded-lg px-3 py-1.5 text-xs font-semibold transition ${
                                    statusFilter === item.key
                                        ? 'bg-rose-600 text-white shadow-xs'
                                        : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                                }`}
                            >
                                {item.label}
                            </button>
                        ))}
                    </div>

                    <Link
                        href="/cashier/transactions/register-member"
                        className="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700"
                    >
                        <UserPlus className="h-4 w-4" />
                        Daftar Baru
                    </Link>
                </div>
            </div>

            {/* Members Table */}
            <Card className="overflow-hidden p-0">
                {members.data && members.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                            <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-400">
                                <tr>
                                    <th className="px-5 py-3.5 font-bold">Data Member</th>
                                    <th className="px-5 py-3.5 font-bold">Kontak</th>
                                    <th className="px-5 py-3.5 font-bold">Paket</th>
                                    <th className="px-5 py-3.5 font-bold">Masa Aktif & Kedaluwarsa</th>
                                    <th className="px-5 py-3.5 font-bold">Status</th>
                                    <th className="px-5 py-3.5 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {members.data.map((m) => (
                                    <tr key={m.id} className="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                        <td className="px-5 py-3.5">
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                                    {m.profile_initials || m.full_name?.charAt(0) || 'M'}
                                                </div>
                                                <div>
                                                    <p className="font-bold text-slate-900 dark:text-white">
                                                        {m.full_name}
                                                    </p>
                                                    <p className="font-mono text-xs text-rose-600 dark:text-rose-400 font-semibold">
                                                        {m.checkin_code}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5 text-xs">
                                            <p className="text-slate-800 dark:text-slate-200">{m.phone}</p>
                                            <p className="text-slate-400">{m.email || '-'}</p>
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <Badge variant="blue">{m.membership_plan}</Badge>
                                        </td>
                                        <td className="px-5 py-3.5 text-xs">
                                            <div className="flex items-center gap-1.5 font-medium text-slate-800 dark:text-slate-200">
                                                <Calendar className="h-3.5 w-3.5 text-slate-400" />
                                                <span>Berakhir: {m.expires_at_label}</span>
                                            </div>
                                            <p className="mt-0.5 text-[11px] text-slate-500">
                                                {m.remaining_days > 0 ? (
                                                    <span className="font-bold text-emerald-600 dark:text-emerald-400">
                                                        Tersisa {m.remaining_days} hari
                                                    </span>
                                                ) : (
                                                    <span className="font-bold text-rose-600 dark:text-rose-400">
                                                        Sudah berakhir
                                                    </span>
                                                )}
                                            </p>
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <Badge variant={m.remaining_days > 0 ? 'green' : 'rose'}>
                                                {m.remaining_days > 0 ? 'Aktif' : 'Expired'}
                                            </Badge>
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <div className="flex items-center justify-end gap-2">
                                                <Link
                                                    href={`/cashier/member-payments?member_id=${m.id}`}
                                                    className="inline-flex items-center gap-1 rounded-lg bg-rose-600 px-2.5 py-1 text-xs font-bold text-white shadow-xs hover:bg-rose-700"
                                                >
                                                    <CreditCard className="h-3.5 w-3.5" />
                                                    Perpanjang
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="p-8">
                        <EmptyState
                            title="Tidak ada member ditemukan"
                            description="Gunakan kata kunci lain atau daftarkan member baru."
                        />
                    </div>
                )}
            </Card>

            <div className="mt-6">
                <Pagination links={members.links} />
            </div>
        </AppLayout>
    );
}
