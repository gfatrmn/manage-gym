import React, { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import {
    Clock,
    DollarSign,
    Edit3,
    Layers,
    PackagePlus,
    Power,
    Search,
    ShieldAlert,
    Sparkles,
    Tag,
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
    Textarea,
} from '../../Components/UI';
import { formatCurrency } from '../../lib/format';

export default function Packages({ packages, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || 'all');
    const [showModal, setShowModal] = useState(false);
    const [editingPackage, setEditingPackage] = useState(null);

    const form = useForm({
        name: '',
        duration_days: 30,
        price: 150000,
        description: '',
        facilities: 'Akses gym full, loker, shower',
        is_active: true,
    });

    const openCreateModal = () => {
        setEditingPackage(null);
        form.reset();
        form.setData({
            name: '',
            duration_days: 30,
            price: 150000,
            description: '',
            facilities: 'Akses gym full, loker, shower',
            is_active: true,
        });
        setShowModal(true);
    };

    const openEditModal = (pkg) => {
        setEditingPackage(pkg);
        form.setData({
            name: pkg.name,
            duration_days: pkg.duration_days,
            price: pkg.price,
            description: pkg.description || '',
            facilities: pkg.facilities || '',
            is_active: Boolean(pkg.is_active),
        });
        setShowModal(true);
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/packages', { q: search, status: statusFilter }, { preserveState: true });
    };

    const handleFilterStatus = (newStatus) => {
        setStatusFilter(newStatus);
        router.get('/admin/packages', { q: search, status: newStatus }, { preserveState: true });
    };

    const handleToggleStatus = (pkg) => {
        if (confirm(`Ubah status aktif paket "${pkg.name}"?`)) {
            router.patch(`/admin/packages/${pkg.id}/toggle-status`, {}, { preserveScroll: true });
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editingPackage) {
            form.put(`/admin/packages/${editingPackage.id}`, {
                onSuccess: () => {
                    setShowModal(false);
                },
            });
        } else {
            form.post('/admin/packages', {
                onSuccess: () => {
                    setShowModal(false);
                },
            });
        }
    };

    return (
        <AppLayout
            title="Paket Membership"
            subtitle="Kelola paket membership gym, durasi, harga, dan fasilitas"
            active="packages"
            panel="admin"
        >
            <Head title="Paket Membership" />

            {/* Header Actions */}
            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <form onSubmit={handleSearch} className="flex flex-1 items-center gap-2 max-w-md">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            type="text"
                            placeholder="Cari nama paket atau fasilitas..."
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
                        {['all', 'active', 'inactive'].map((st) => (
                            <button
                                key={st}
                                type="button"
                                onClick={() => handleFilterStatus(st)}
                                className={`rounded-lg px-3 py-1.5 text-xs font-semibold capitalize transition ${
                                    statusFilter === st
                                        ? 'bg-rose-600 text-white shadow-xs'
                                        : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                                }`}
                            >
                                {st === 'all' ? 'Semua' : st === 'active' ? 'Aktif' : 'Nonaktif'}
                            </button>
                        ))}
                    </div>

                    <Button onClick={openCreateModal} className="gap-2">
                        <PackagePlus className="h-4 w-4" />
                        Tambah Paket
                    </Button>
                </div>
            </div>

            {/* Packages Grid */}
            {packages.data && packages.data.length > 0 ? (
                <div className="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    {packages.data.map((pkg) => (
                        <Card
                            key={pkg.id}
                            className={`relative flex flex-col justify-between border transition hover:shadow-md ${
                                pkg.is_active
                                    ? 'border-slate-200 dark:border-slate-800'
                                    : 'border-slate-200/50 bg-slate-50/50 opacity-75 dark:border-slate-800/50 dark:bg-slate-950/40'
                            }`}
                        >
                            <div>
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <Badge variant={pkg.is_active ? 'green' : 'slate'}>
                                            {pkg.is_active ? 'Aktif' : 'Nonaktif'}
                                        </Badge>
                                        <h3 className="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                                            {pkg.name}
                                        </h3>
                                    </div>
                                    <span className="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400">
                                        <Sparkles className="h-5 w-5" />
                                    </span>
                                </div>

                                <div className="mt-4 flex items-baseline gap-1">
                                    <span className="text-2xl font-extrabold text-slate-900 dark:text-white">
                                        {formatCurrency(pkg.price)}
                                    </span>
                                    <span className="text-xs font-medium text-slate-500 dark:text-slate-400">
                                        / {pkg.duration_days} hari
                                    </span>
                                </div>

                                {pkg.description && (
                                    <p className="mt-2.5 text-xs text-slate-600 dark:text-slate-400 line-clamp-2">
                                        {pkg.description}
                                    </p>
                                )}

                                <div className="mt-4 border-t border-slate-100 pt-3 dark:border-slate-800">
                                    <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                        Fasilitas
                                    </p>
                                    <p className="mt-1 text-xs text-slate-700 dark:text-slate-300">
                                        {pkg.facilities || 'Akses Gym Standar'}
                                    </p>
                                </div>

                                <div className="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                    <span className="inline-flex items-center gap-1">
                                        <Clock className="h-3.5 w-3.5" />
                                        Durasi: {pkg.duration_days} Hari
                                    </span>
                                    <span className="inline-flex items-center gap-1 font-semibold text-rose-600 dark:text-rose-400">
                                        <Layers className="h-3.5 w-3.5" />
                                        {pkg.memberships_count ?? 0} Member Terdaftar
                                    </span>
                                </div>
                            </div>

                            <div className="mt-6 flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-800">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => handleToggleStatus(pkg)}
                                    className={pkg.is_active ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'}
                                >
                                    <Power className="mr-1 h-3.5 w-3.5" />
                                    {pkg.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                </Button>

                                <Button
                                    variant="secondary"
                                    size="sm"
                                    onClick={() => openEditModal(pkg)}
                                    className="gap-1.5"
                                >
                                    <Edit3 className="h-3.5 w-3.5" />
                                    Edit Paket
                                </Button>
                            </div>
                        </Card>
                    ))}
                </div>
            ) : (
                <EmptyState
                    title="Tidak ada paket membership"
                    description="Belum ada data paket membership yang cocok dengan kriteria pencarian."
                    action={
                        <Button onClick={openCreateModal} className="gap-2">
                            <PackagePlus className="h-4 w-4" />
                            Tambah Paket Baru
                        </Button>
                    }
                />
            )}

            {/* Pagination */}
            <div className="mt-6">
                <Pagination links={packages.links} />
            </div>

            {/* Create / Edit Modal */}
            <Modal
                show={showModal}
                onClose={() => setShowModal(false)}
                title={editingPackage ? 'Edit Paket Membership' : 'Tambah Paket Membership'}
                maxWidth="md"
            >
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="name" required>
                            Nama Paket
                        </InputLabel>
                        <Input
                            id="name"
                            value={form.data.name}
                            onChange={(e) => form.setData('name', e.target.value)}
                            placeholder="Contoh: Paket 1 Bulan, Paket Pelajar, dll."
                            error={form.errors.name}
                            required
                        />
                        <FieldError message={form.errors.name} />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="duration_days" required>
                                Durasi (Hari)
                            </InputLabel>
                            <Input
                                id="duration_days"
                                type="number"
                                min="1"
                                value={form.data.duration_days}
                                onChange={(e) => form.setData('duration_days', parseInt(e.target.value) || 1)}
                                error={form.errors.duration_days}
                                required
                            />
                            <FieldError message={form.errors.duration_days} />
                        </div>

                        <div>
                            <InputLabel htmlFor="price" required>
                                Harga (Rp)
                            </InputLabel>
                            <Input
                                id="price"
                                type="number"
                                min="0"
                                step="1000"
                                value={form.data.price}
                                onChange={(e) => form.setData('price', parseInt(e.target.value) || 0)}
                                error={form.errors.price}
                                required
                            />
                            <FieldError message={form.errors.price} />
                        </div>
                    </div>

                    <div>
                        <InputLabel htmlFor="description">Deskripsi Singkat</InputLabel>
                        <Textarea
                            id="description"
                            rows={2}
                            value={form.data.description}
                            onChange={(e) => form.setData('description', e.target.value)}
                            placeholder="Penjelasan keunggulan paket..."
                            error={form.errors.description}
                        />
                        <FieldError message={form.errors.description} />
                    </div>

                    <div>
                        <InputLabel htmlFor="facilities">Fasilitas (Dipisahkan koma)</InputLabel>
                        <Input
                            id="facilities"
                            value={form.data.facilities}
                            onChange={(e) => form.setData('facilities', e.target.value)}
                            placeholder="Loker, shower, trainer konsultasi, free wifi"
                            error={form.errors.facilities}
                        />
                        <FieldError message={form.errors.facilities} />
                    </div>

                    <div className="flex items-center gap-2 pt-2">
                        <input
                            type="checkbox"
                            id="is_active"
                            checked={form.data.is_active}
                            onChange={(e) => form.setData('is_active', e.target.checked)}
                            className="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500 dark:border-slate-700 dark:bg-slate-900"
                        />
                        <label htmlFor="is_active" className="text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                            Paket Aktif & Dapat Dipilih Saat Pendaftaran
                        </label>
                    </div>

                    <div className="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setShowModal(false)}
                            disabled={form.processing}
                        >
                            Batal
                        </Button>
                        <Button type="submit" disabled={form.processing}>
                            {form.processing ? 'Menyimpan...' : editingPackage ? 'Simpan Perubahan' : 'Buat Paket'}
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
