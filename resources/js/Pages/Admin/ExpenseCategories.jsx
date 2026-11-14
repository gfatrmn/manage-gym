import React, { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { Edit3, FolderPlus, Layers, Search, Trash2 } from 'lucide-react';
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

export default function ExpenseCategories({ categories, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [showModal, setShowModal] = useState(false);
    const [editingCategory, setEditingCategory] = useState(null);

    const form = useForm({
        name: '',
        description: '',
    });

    const openCreateModal = () => {
        setEditingCategory(null);
        form.reset();
        setShowModal(true);
    };

    const openEditModal = (cat) => {
        setEditingCategory(cat);
        form.setData({
            name: cat.name,
            description: cat.description || '',
        });
        setShowModal(true);
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/expense-categories', { q: search }, { preserveState: true });
    };

    const handleDelete = (cat) => {
        if (confirm(`Apakah Anda yakin ingin menghapus kategori "${cat.name}"?`)) {
            router.delete(`/admin/expense-categories/${cat.id}`);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editingCategory) {
            form.put(`/admin/expense-categories/${editingCategory.id}`, {
                onSuccess: () => setShowModal(false),
            });
        } else {
            form.post('/admin/expense-categories', {
                onSuccess: () => setShowModal(false),
            });
        }
    };

    return (
        <AppLayout
            title="Kategori Pengeluaran"
            subtitle="Master data kelompok biaya operasional gym (gaji, utilitas, maintenance, dll)"
            active="expense-categories"
            panel="admin"
        >
            <Head title="Kategori Pengeluaran" />

            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <form onSubmit={handleSearch} className="flex flex-1 items-center gap-2 max-w-md">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            placeholder="Cari nama atau deskripsi kategori..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="pl-9"
                        />
                    </div>
                    <Button type="submit" variant="secondary" size="md">
                        Cari
                    </Button>
                </form>

                <Button onClick={openCreateModal} className="gap-2">
                    <FolderPlus className="h-4 w-4" />
                    Tambah Kategori
                </Button>
            </div>

            {/* Categories Table */}
            <Card className="overflow-hidden p-0">
                {categories.data && categories.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                            <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-400">
                                <tr>
                                    <th className="px-5 py-3.5 font-bold">Nama Kategori</th>
                                    <th className="px-5 py-3.5 font-bold">Deskripsi</th>
                                    <th className="px-5 py-3.5 font-bold">Total Transaksi Biaya</th>
                                    <th className="px-5 py-3.5 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {categories.data.map((cat) => (
                                    <tr key={cat.id} className="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                        <td className="px-5 py-3.5">
                                            <p className="font-bold text-slate-900 dark:text-white">
                                                {cat.name}
                                            </p>
                                        </td>
                                        <td className="px-5 py-3.5 text-xs text-slate-500">
                                            {cat.description || '-'}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <Badge variant="slate">
                                                {cat.expenses_count ?? 0} Pengeluaran
                                            </Badge>
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <div className="flex items-center justify-end gap-2">
                                                <Button
                                                    variant="secondary"
                                                    size="sm"
                                                    onClick={() => openEditModal(cat)}
                                                >
                                                    <Edit3 className="h-3.5 w-3.5" />
                                                </Button>
                                                <Button
                                                    variant="danger"
                                                    size="sm"
                                                    onClick={() => handleDelete(cat)}
                                                >
                                                    <Trash2 className="h-3.5 w-3.5" />
                                                </Button>
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
                            title="Tidak ada kategori pengeluaran"
                            description="Belum ada data kategori pengeluaran yang terdaftar."
                            action={
                                <Button onClick={openCreateModal} className="gap-2">
                                    <FolderPlus className="h-4 w-4" />
                                    Tambah Kategori Baru
                                </Button>
                            }
                        />
                    </div>
                )}
            </Card>

            <div className="mt-6">
                <Pagination links={categories.links} />
            </div>

            {/* Modal Form */}
            <Modal
                show={showModal}
                onClose={() => setShowModal(false)}
                title={editingCategory ? 'Edit Kategori Pengeluaran' : 'Tambah Kategori Pengeluaran'}
                maxWidth="md"
            >
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="name" required>
                            Nama Kategori
                        </InputLabel>
                        <Input
                            id="name"
                            value={form.data.name}
                            onChange={(e) => form.setData('name', e.target.value)}
                            placeholder="Contoh: Utilitas Listrik & Air, Gaji Staff, Maintenance Alat"
                            error={form.errors.name}
                            required
                        />
                        <FieldError message={form.errors.name} />
                    </div>

                    <div>
                        <InputLabel htmlFor="description">Deskripsi</InputLabel>
                        <Textarea
                            id="description"
                            rows={3}
                            value={form.data.description}
                            onChange={(e) => form.setData('description', e.target.value)}
                            placeholder="Keterangan lingkup kategori ini..."
                            error={form.errors.description}
                        />
                        <FieldError message={form.errors.description} />
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
                            {form.processing ? 'Menyimpan...' : editingCategory ? 'Simpan Perubahan' : 'Buat Kategori'}
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
