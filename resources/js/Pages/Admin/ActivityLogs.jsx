import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Activity, Clock, Filter, Globe, RotateCcw, Search, Shield, User } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, EmptyState, Input, Pagination, Select } from '../../Components/UI';
import { formatDateTime } from '../../lib/format';

export default function ActivityLogs({ logs, actionTypes = [], filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [action, setAction] = useState(filters.action || 'all');

    const handleFilter = (e) => {
        e?.preventDefault();
        router.get('/admin/activity-logs', { q: search, action }, { preserveState: true });
    };

    const handleReset = () => {
        setSearch('');
        setAction('all');
        router.get('/admin/activity-logs');
    };

    const formatActionBadge = (act) => {
        if (act.includes('create') || act.includes('store')) {
            return <Badge variant="green">{act}</Badge>;
        }
        if (act.includes('update') || act.includes('toggle')) {
            return <Badge variant="blue">{act}</Badge>;
        }
        if (act.includes('delete') || act.includes('destroy') || act.includes('cancel')) {
            return <Badge variant="rose">{act}</Badge>;
        }
        return <Badge variant="slate">{act}</Badge>;
    };

    return (
        <AppLayout
            title="Activity Log (Audit Trail)"
            subtitle="Rekaman seluruh riwayat aktivitas, aksi admin, kasir, dan perubahan data sistem"
            active="activity-logs"
            panel="admin"
        >
            <Head title="Activity Log" />

            {/* Filter Bar */}
            <Card className="mb-6 p-4">
                <form onSubmit={handleFilter} className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <Input
                                placeholder="Cari aktivitas, deskripsi, atau IP..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pl-9"
                            />
                        </div>

                        <div className="w-full sm:w-60">
                            <Select value={action} onChange={(e) => setAction(e.target.value)}>
                                <option value="all">Semua Tipe Aksi</option>
                                {actionTypes.map((act) => (
                                    <option key={act} value={act}>
                                        {act}
                                    </option>
                                ))}
                            </Select>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <Button type="button" variant="ghost" size="sm" onClick={handleReset}>
                            <RotateCcw className="mr-1 h-3.5 w-3.5" />
                            Reset
                        </Button>
                        <Button type="submit" size="sm">
                            <Filter className="mr-1 h-3.5 w-3.5" />
                            Filter
                        </Button>
                    </div>
                </form>
            </Card>

            {/* Logs Table */}
            <Card className="overflow-hidden p-0">
                {logs.data && logs.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                            <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-400">
                                <tr>
                                    <th className="px-5 py-3.5 font-bold">Waktu</th>
                                    <th className="px-5 py-3.5 font-bold">Pengguna</th>
                                    <th className="px-5 py-3.5 font-bold">Aksi</th>
                                    <th className="px-5 py-3.5 font-bold">Deskripsi Aktivitas</th>
                                    <th className="px-5 py-3.5 font-bold">IP Address</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {logs.data.map((log) => (
                                    <tr key={log.id} className="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                        <td className="px-5 py-3.5 text-xs whitespace-nowrap">
                                            {formatDateTime(log.created_at)}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <div className="flex items-center gap-2">
                                                <div className="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    {log.user?.name ? log.user.name.charAt(0) : 'S'}
                                                </div>
                                                <div>
                                                    <p className="text-xs font-semibold text-slate-900 dark:text-white">
                                                        {log.user?.name || 'Sistem Otomatis'}
                                                    </p>
                                                    <p className="text-[10px] text-slate-400 capitalize">
                                                        {log.user?.role || 'system'}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5 whitespace-nowrap">
                                            {formatActionBadge(log.action)}
                                        </td>
                                        <td className="px-5 py-3.5 text-xs text-slate-800 dark:text-slate-200 font-medium">
                                            {log.description}
                                        </td>
                                        <td className="px-5 py-3.5 font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                            {log.ip_address || '-'}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="p-8">
                        <EmptyState
                            title="Tidak ada log aktivitas"
                            description="Belum ada riwayat aktivitas yang tercatat sesuai filter."
                        />
                    </div>
                )}
            </Card>

            <div className="mt-6">
                <Pagination links={logs.links} />
            </div>
        </AppLayout>
    );
}
