import React, { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import {
    AlertOctagon,
    Calendar,
    CheckCircle2,
    Clock,
    CreditCard,
    Filter,
    PauseCircle,
    PlayCircle,
    Search,
    ShieldAlert,
    User,
    XCircle,
} from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    FieldError,
    Input,
    InputLabel,
    Modal,
    Pagination,
    Select,
    Textarea,
} from '../../Components/UI';
import { formatCurrency, formatDate } from '../../lib/format';

export default function Memberships({ memberships, stats = {}, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || 'all');
    const [selectedMembership, setSelectedMembership] = useState(null);
    const [showStatusModal, setShowStatusModal] = useState(false);

    const statusForm = useForm({
        status: 'active',
        status_reason: '',
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/memberships', { q: search, status: statusFilter }, { preserveState: true });
    };

    const handleFilterStatus = (newStatus) => {
        setStatusFilter(newStatus);
        router.get('/admin/memberships', { q: search, status: newStatus }, { preserveState: true });
    };

    const openStatusModal = (item) => {
        setSelectedMembership(item);
        statusForm.setData({
            status: item.status,
            status_reason: item.status_reason || '',
        });
        setShowStatusModal(true);
    };

    const handleStatusSubmit = (e) => {
        e.preventDefault();
        if (!selectedMembership) return;

        statusForm.patch(`/admin/memberships/${selectedMembership.id}/status`, {
            onSuccess: () => {
                setShowStatusModal(false);
            },
        });
    };

    const getStatusBadge = (status) => {
        switch (status) {
            case 'active':
                return <Badge variant="green">Aktif</Badge>;
            case 'expired':
                return <Badge variant="rose">Kedaluwarsa</Badge>;
            case 'suspended':
                return <Badge variant="warning">Ditangguhkan</Badge>;
            case 'cancelled':
                return <Badge variant="slate">Dibatalkan</Badge>;
            case 'pending':
                return <Badge variant="blue">Menunggu Pembayaran</Badge>;
            default:
                return <Badge variant="slate">{status}</Badge>;
        }
    };

    return (
        <AppLayout
            title="Status Membership"
            subtitle="Monitoring dan manajemen status langganan seluruh member gym"
            active="memberships"
            panel="admin"
        >
            <Head title="Status Membership" />

            {/* Stat Cards */}
            <div className="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <Card className="flex items-center gap-3 p-4">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <CreditCard className="h-5 w-5" />
                    </div>
                    <div>
                        <p className="text-xs font-medium text-slate-500 dark:text-slate-400">Total Langganan</p>
                        <p className="text-xl font-bold text-slate-900 dark:text-white">{stats.total ?? 0}</p>
                    </div>
                </Card>

                <Card className="flex items-center gap-3 p-4">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">
                        <CheckCircle2 className="h-5 w-5" />
                    </div>
                    <div>
                        <p className="text-xs font-medium text-slate-500 dark:text-slate-400">Aktif</p>
                        <p className="text-xl font-bold text-emerald-600 dark:text-emerald-400">{stats.active ?? 0}</p>
                    </div>
                </Card>

                <Card className="flex items-center gap-3 p-4">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400">
                        <AlertOctagon className="h-5 w-5" />
                    </div>
                    <div>
                        <p className="text-xs font-medium text-slate-500 dark:text-slate-400">Kedaluwarsa</p>
                        <p className="text-xl font-bold text-rose-600 dark:text-rose-400">{stats.expired ?? 0}</p>
                    </div>
                </Card>

                <Card className="flex items-center gap-3 p-4">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                        <PauseCircle className="h-5 w-5" />
                    </div>
                    <div>
                        <p className="text-xs font-medium text-slate-500">Ditangguhkan</p>
                        <p className="text-xl font-bold text-slate-900">{stats.suspended ?? 0}</p>
                    </div>
                </Card>
            </div>

            {/* Filters */}
            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <form onSubmit={handleSearch} className="flex flex-1 items-center gap-2 max-w-md">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            type="text"
                            placeholder="Cari nama member, kode, atau no HP..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="pl-9"
                        />
                    </div>
                    <Button type="submit" variant="secondary" size="md">
                        Cari
                    </Button>
                </form>

                <div className="flex flex-wrap items-center gap-1 overflow-x-auto rounded-xl border border-slate-200 bg-white p-1 dark:border-slate-800 dark:bg-slate-900">
                    {[
                        { key: 'all', label: 'Semua' },
                        { key: 'active', label: 'Aktif' },
                        { key: 'expired', label: 'Expired' },
                        { key: 'suspended', label: 'Ditangguhkan' },
                        { key: 'cancelled', label: 'Dibatalkan' },
                    ].map((tab) => (
                        <button
                            key={tab.key}
                            type="button"
                            onClick={() => handleFilterStatus(tab.key)}
                            className={`rounded-lg px-3 py-1.5 text-xs font-semibold transition ${
                                statusFilter === tab.key
                                    ? 'bg-rose-600 text-white shadow-xs'
                                    : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                            }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>
            </div>

            {/* Memberships Table */}
            <Card className="overflow-hidden p-0">
                {memberships.data && memberships.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                            <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-400">
                                <tr>
                                    <th className="px-5 py-3.5 font-bold">Member</th>
                                    <th className="px-5 py-3.5 font-bold">Paket</th>
                                    <th className="px-5 py-3.5 font-bold">Periode Aktif</th>
                                    <th className="px-5 py-3.5 font-bold">Biaya Paket</th>
                                    <th className="px-5 py-3.5 font-bold">Status</th>
                                    <th className="px-5 py-3.5 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {memberships.data.map((item) => (
                                    <tr key={item.id} className="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                        <td className="px-5 py-3.5">
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    {item.member?.full_name?.charAt(0) || 'M'}
                                                </div>
                                                <div>
                                                    <p className="font-semibold text-slate-900 dark:text-white">
                                                        {item.member?.full_name || 'Member dihapus'}
                                                    </p>
                                                    <p className="text-xs text-slate-500">
                                                        {item.member?.checkin_code || '-'} · {item.member?.phone || '-'}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <p className="font-medium text-slate-900 dark:text-white">
                                                {item.package?.name || 'Paket Kustom'}
                                            </p>
                                            <p className="text-xs text-slate-500">
                                                {item.package?.duration_days ? `${item.package.duration_days} Hari` : '-'}
                                            </p>
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <div className="flex items-center gap-1.5 text-xs">
                                                <Calendar className="h-3.5 w-3.5 text-slate-400" />
                                                <span>{formatDate(item.start_date)} - {formatDate(item.end_date)}</span>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5 font-semibold text-slate-900 dark:text-white">
                                            {formatCurrency(item.price)}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            {getStatusBadge(item.status)}
                                            {item.status_reason && (
                                                <p className="mt-1 text-[11px] text-slate-500 italic max-w-xs truncate" title={item.status_reason}>
                                                    Ket: {item.status_reason}
                                                </p>
                                            )}
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                onClick={() => openStatusModal(item)}
                                                className="text-xs"
                                            >
                                                Ubah Status
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="p-8">
                        <EmptyState
                            title="Tidak ada membership ditemukan"
                            description="Belum ada data riwayat langganan membership yang sesuai."
                        />
                    </div>
                )}
            </Card>

            {/* Pagination */}
            <div className="mt-6">
                <Pagination links={memberships.links} />
            </div>

            {/* Status Change Modal */}
            <Modal
                show={showStatusModal}
                onClose={() => setShowStatusModal(false)}
                title="Perbarui Status Membership"
                maxWidth="md"
            >
                <form onSubmit={handleStatusSubmit} className="space-y-4">
                    {selectedMembership && (
                        <div className="rounded-xl bg-slate-50 p-3.5 text-xs text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                            <p className="font-bold text-slate-900 dark:text-white">
                                {selectedMembership.member?.full_name} ({selectedMembership.member?.checkin_code})
                            </p>
                            <p className="mt-1">
                                Paket: {selectedMembership.package?.name} · Periode: {formatDate(selectedMembership.start_date)} s/d {formatDate(selectedMembership.end_date)}
                            </p>
                        </div>
                    )}

                    <div>
                        <InputLabel htmlFor="status" required>
                            Pilih Status Baru
                        </InputLabel>
                        <Select
                            id="status"
                            value={statusForm.data.status}
                            onChange={(e) => statusForm.setData('status', e.target.value)}
                            error={statusForm.errors.status}
                            required
                        >
                            <option value="active">Aktif (Aktifkan Membership)</option>
                            <option value="suspended">Ditangguhkan / Suspended</option>
                            <option value="cancelled">Dibatalkan / Cancelled</option>
                            <option value="expired">Kedaluwarsa / Expired</option>
                        </Select>
                        <FieldError message={statusForm.errors.status} />
                    </div>

                    <div>
                        <InputLabel htmlFor="status_reason" required>
                            Alasan Perubahan Status (Wajib diisi)
                        </InputLabel>
                        <Textarea
                            id="status_reason"
                            rows={3}
                            placeholder="Contoh: Member mengajukan penundaan latihan cuti luar kota, salah input paket, dll."
                            value={statusForm.data.status_reason}
                            onChange={(e) => statusForm.setData('status_reason', e.target.value)}
                            error={statusForm.errors.status_reason}
                            required
                        />
                        <FieldError message={statusForm.errors.status_reason} />
                    </div>

                    <div className="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setShowStatusModal(false)}
                            disabled={statusForm.processing}
                        >
                            Batal
                        </Button>
                        <Button type="submit" disabled={statusForm.processing}>
                            {statusForm.processing ? 'Menyimpan...' : 'Perbarui Status'}
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
