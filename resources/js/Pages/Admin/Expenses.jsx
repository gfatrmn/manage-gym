import React, { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import {
    Calendar,
    DollarSign,
    Edit3,
    FilePlus,
    Filter,
    FolderKanban,
    RotateCcw,
    Search,
    Trash2,
    Wallet,
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

export default function Expenses({
    expenses,
    categories = [],
    totalAmount = 0,
    filters = {},
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [categoryId, setCategoryId] = useState(filters.category_id || '');
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');

    const [showModal, setShowModal] = useState(false);
    const [editingExpense, setEditingExpense] = useState(null);

    const form = useForm({
        title: '',
        category_id: categories.length > 0 ? categories[0].id : '',
        amount: 50000,
        expense_date: new Date().toISOString().split('T')[0],
        payment_method: 'cash',
        notes: '',
    });

    const openCreateModal = () => {
        setEditingExpense(null);
        form.reset();
        form.setData({
            title: '',
            category_id: categories.length > 0 ? categories[0].id : '',
            amount: 50000,
            expense_date: new Date().toISOString().split('T')[0],
            payment_method: 'cash',
            notes: '',
        });
        setShowModal(true);
    };

    const openEditModal = (item) => {
        setEditingExpense(item);
        form.setData({
            title: item.title,
            category_id: item.category_id || (categories.length > 0 ? categories[0].id : ''),
            amount: item.amount,
            expense_date: item.expense_date ? item.expense_date.split('T')[0] : '',
            payment_method: item.payment_method || 'cash',
            notes: item.notes || '',
        });
        setShowModal(true);
    };

    const handleFilter = (e) => {
        e?.preventDefault();
        router.get(
            '/admin/expenses',
            {
                q: search,
                category_id: categoryId,
                start_date: startDate,
                end_date: endDate,
            },
            { preserveState: true }
        );
    };

    const handleReset = () => {
        setSearch('');
        setCategoryId('');
        setStartDate('');
        setEndDate('');
        router.get('/admin/expenses');
    };

    const handleDelete = (item) => {
        if (confirm(`Hapus catatan pengeluaran "${item.title}"?`)) {
            router.delete(`/admin/expenses/${item.id}`);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editingExpense) {
            form.put(`/admin/expenses/${editingExpense.id}`, {
                onSuccess: () => setShowModal(false),
            });
        } else {
            form.post('/admin/expenses', {
                onSuccess: () => setShowModal(false),
            });
        }
    };

    return (
        <AppLayout
            title="Catat Pengeluaran"
            subtitle="Pencatatan dan monitoring beban biaya operasional gym secara akurat"
            active="expenses"
            panel="admin"
        >
            <Head title="Pengeluaran Operasional" />

            {/* Total Expense Summary */}
            <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Card className="flex items-center gap-4 p-5 sm:col-span-2">
                    <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400">
                        <Wallet className="h-6 w-6" />
                    </div>
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Total Pengeluaran (Sesuai Filter)
                        </p>
                        <p className="text-2xl font-black text-rose-600 dark:text-rose-400">
                            {formatCurrency(totalAmount)}
                        </p>
                    </div>
                </Card>

                <div className="flex items-center justify-end gap-2">
                    <Button onClick={openCreateModal} size="lg" className="w-full gap-2 sm:w-auto">
                        <FilePlus className="h-5 w-5" />
                        Catat Biaya Baru
                    </Button>
                </div>
            </div>

            {/* Filter Card */}
            <Card className="mb-6 p-4">
                <form onSubmit={handleFilter} className="space-y-3">
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <InputLabel htmlFor="search">Cari Deskripsi / Catatan</InputLabel>
                            <Input
                                id="search"
                                placeholder="Contoh: Beli sabun, servis AC..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                        </div>

                        <div>
                            <InputLabel htmlFor="category_id">Kategori Biaya</InputLabel>
                            <Select
                                id="category_id"
                                value={categoryId}
                                onChange={(e) => setCategoryId(e.target.value)}
                            >
                                <option value="">Semua Kategori</option>
                                {categories.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.name}
                                    </option>
                                ))}
                            </Select>
                        </div>

                        <div className="flex flex-col">
                            <InputLabel>Rentang Tanggal</InputLabel>
                            <div className="flex items-center gap-2">
                                <Input
                                    type="date"
                                    value={startDate}
                                    onChange={(e) => setStartDate(e.target.value)}
                                    className="py-1.5 text-xs"
                                />
                                <span className="text-xs text-slate-400">-</span>
                                <Input
                                    type="date"
                                    value={endDate}
                                    onChange={(e) => setEndDate(e.target.value)}
                                    className="py-1.5 text-xs"
                                />
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <Button type="button" variant="ghost" size="sm" onClick={handleReset}>
                            <RotateCcw className="mr-1 h-3.5 w-3.5" />
                            Reset
                        </Button>
                        <Button type="submit" size="sm">
                            <Filter className="mr-1 h-3.5 w-3.5" />
                            Filter Data
                        </Button>
                    </div>
                </form>
            </Card>

            {/* Expenses Table */}
            <Card className="overflow-hidden p-0">
                {expenses.data && expenses.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                            <thead className="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-400">
                                <tr>
                                    <th className="px-5 py-3.5 font-bold">Tanggal</th>
                                    <th className="px-5 py-3.5 font-bold">Keperluan / Judul</th>
                                    <th className="px-5 py-3.5 font-bold">Kategori</th>
                                    <th className="px-5 py-3.5 font-bold">Metode</th>
                                    <th className="px-5 py-3.5 font-bold">Dicatat Oleh</th>
                                    <th className="px-5 py-3.5 font-bold">Nominal</th>
                                    <th className="px-5 py-3.5 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {expenses.data.map((item) => (
                                    <tr key={item.id} className="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                        <td className="px-5 py-3.5 text-xs">
                                            {formatDate(item.expense_date)}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <p className="font-bold text-slate-900 dark:text-white">
                                                {item.title}
                                            </p>
                                            {item.notes && (
                                                <p className="text-xs text-slate-500 line-clamp-1">{item.notes}</p>
                                            )}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <Badge variant="blue">
                                                {item.category?.name || item.category || 'Operasional'}
                                            </Badge>
                                        </td>
                                        <td className="px-5 py-3.5 uppercase text-xs font-semibold">
                                            {item.payment_method || 'cash'}
                                        </td>
                                        <td className="px-5 py-3.5 text-xs text-slate-500">
                                            {item.user?.name || 'Admin'}
                                        </td>
                                        <td className="px-5 py-3.5 font-bold text-rose-600 dark:text-rose-400">
                                            {formatCurrency(item.amount)}
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <div className="flex items-center justify-end gap-1.5">
                                                <Button
                                                    variant="secondary"
                                                    size="sm"
                                                    onClick={() => openEditModal(item)}
                                                >
                                                    <Edit3 className="h-3.5 w-3.5" />
                                                </Button>
                                                <Button
                                                    variant="danger"
                                                    size="sm"
                                                    onClick={() => handleDelete(item)}
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
                            title="Belum ada catatan pengeluaran"
                            description="Catat pengeluaran operasional gym untuk menjaga keakuratan laporan keuangan."
                            action={
                                <Button onClick={openCreateModal} className="gap-2">
                                    <FilePlus className="h-4 w-4" />
                                    Catat Pengeluaran Pertama
                                </Button>
                            }
                        />
                    </div>
                )}
            </Card>

            <div className="mt-6">
                <Pagination links={expenses.links} />
            </div>

            {/* Create/Edit Modal */}
            <Modal
                show={showModal}
                onClose={() => setShowModal(false)}
                title={editingExpense ? 'Edit Pengeluaran' : 'Catat Pengeluaran Operasional'}
                maxWidth="md"
            >
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="title" required>
                            Judul / Keperluan Pengeluaran
                        </InputLabel>
                        <Input
                            id="title"
                            placeholder="Contoh: Tagihan Listrik PLN Bulan Ini, Servis Treadmill..."
                            value={form.data.title}
                            onChange={(e) => form.setData('title', e.target.value)}
                            error={form.errors.title}
                            required
                        />
                        <FieldError message={form.errors.title} />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="category_id">Kategori Biaya</InputLabel>
                            <Select
                                id="category_id"
                                value={form.data.category_id}
                                onChange={(e) => form.setData('category_id', e.target.value)}
                                error={form.errors.category_id}
                            >
                                <option value="">Pilih Kategori...</option>
                                {categories.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.name}
                                    </option>
                                ))}
                            </Select>
                            <FieldError message={form.errors.category_id} />
                        </div>

                        <div>
                            <InputLabel htmlFor="amount" required>
                                Nominal Biaya (Rp)
                            </InputLabel>
                            <Input
                                id="amount"
                                type="number"
                                min="1000"
                                step="1000"
                                value={form.data.amount}
                                onChange={(e) => form.setData('amount', parseInt(e.target.value) || 0)}
                                error={form.errors.amount}
                                required
                            />
                            <FieldError message={form.errors.amount} />
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="expense_date" required>
                                Tanggal Pengeluaran
                            </InputLabel>
                            <Input
                                id="expense_date"
                                type="date"
                                value={form.data.expense_date}
                                onChange={(e) => form.setData('expense_date', e.target.value)}
                                error={form.errors.expense_date}
                                required
                            />
                            <FieldError message={form.errors.expense_date} />
                        </div>

                        <div>
                            <InputLabel htmlFor="payment_method">Metode Bayar</InputLabel>
                            <Select
                                id="payment_method"
                                value={form.data.payment_method}
                                onChange={(e) => form.setData('payment_method', e.target.value)}
                                error={form.errors.payment_method}
                            >
                                <option value="cash">Tunai (Kas Kasir/Operasional)</option>
                                <option value="transfer">Transfer Bank Perusahaan</option>
                                <option value="qris">QRIS / E-Wallet</option>
                            </Select>
                            <FieldError message={form.errors.payment_method} />
                        </div>
                    </div>

                    <div>
                        <InputLabel htmlFor="notes">Keterangan Tambahan / Catatan Bukti</InputLabel>
                        <Textarea
                            id="notes"
                            rows={3}
                            placeholder="Nomor struk, nama vendor, atau rincian tambahan..."
                            value={form.data.notes}
                            onChange={(e) => form.setData('notes', e.target.value)}
                            error={form.errors.notes}
                        />
                        <FieldError message={form.errors.notes} />
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
                            {form.processing ? 'Menyimpan...' : editingExpense ? 'Simpan Perubahan' : 'Catat Pengeluaran'}
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}
