import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { DollarSign, Plus, Search, Ticket, Trash2 } from 'lucide-react';
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
    StatCard,
} from '../../Components/UI';

export default function DailyPasses({
    dailyPasses,
    search = '',
    todayCount = 0,
    todayRevenue = 'Rp 0',
}) {
    const [query, setQuery] = useState(search || '');
    const [showCreate, setShowCreate] = useState(false);

    const form = useForm({
        full_name: '',
        phone: '',
        payment_method: 'cash',
        payment_amount: 30000,
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/daily-passes', { q: query }, { preserveState: true });
    };

    const submitCreate = (e) => {
        e.preventDefault();
        form.post('/admin/daily-passes', {
            onSuccess: () => {
                form.reset('full_name', 'phone');
                setShowCreate(false);
            },
        });
    };

    const rows = dailyPasses?.data || [];

    return (
        <AppLayout
            title="Tamu Harian (Daily Pass)"
            subtitle="Pencatatan pengunjung non-member harian Arena Gym"
            panel="admin"
            active="daily-passes"
        >
            <div className="mb-6 grid gap-4 sm:grid-cols-2">
                <StatCard title="Kunjungan Hari Ini" value={todayCount} icon={Ticket} accent="rose" />
                <StatCard title="Pendapatan Daily Pass Hari Ini" value={todayRevenue} icon={DollarSign} accent="emerald" />
            </div>

            <Card>
                <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <form onSubmit={handleSearch} className="flex items-center gap-2">
                        <Input
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                            placeholder="Cari nama atau nomor HP tamu..."
                            className="w-64"
                        />
                        <Button type="submit" variant="secondary" size="sm">
                            <Search className="h-4 w-4" />
                            Cari
                        </Button>
                    </form>

                    <Button size="sm" onClick={() => setShowCreate(true)}>
                        <Plus className="h-4 w-4" />
                        Tambah Tamu Harian
                    </Button>
                </div>

                {rows.length === 0 ? (
                    <EmptyState icon={Ticket} title="Belum ada data tamu harian" />
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                        <th className="pb-3 pr-4">Nama Tamu</th>
                                        <th className="pb-3 pr-4">No. HP</th>
                                        <th className="pb-3 pr-4">Metode</th>
                                        <th className="pb-3 pr-4">Nominal</th>
                                        <th className="pb-3 pr-4">Waktu Kunjungan</th>
                                        <th className="pb-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                    {rows.map((item) => (
                                        <tr key={item.id}>
                                            <td className="py-3 pr-4 font-semibold text-slate-900 dark:text-white">
                                                {item.full_name}
                                            </td>
                                            <td className="py-3 pr-4 text-slate-500 dark:text-slate-400">
                                                {item.phone}
                                            </td>
                                            <td className="py-3 pr-4">
                                                <Badge variant="info">{item.payment_method}</Badge>
                                            </td>
                                            <td className="py-3 pr-4 font-semibold">
                                                {item.payment_amount_formatted}
                                            </td>
                                            <td className="py-3 pr-4 text-xs text-slate-500 dark:text-slate-400">
                                                {item.visit_at}
                                            </td>
                                            <td className="py-3 text-right">
                                                <Button
                                                    variant="ghost"
                                                    size="xs"
                                                    onClick={() => {
                                                        if (confirm(`Hapus data "${item.full_name}"?`)) {
                                                            router.delete(`/admin/daily-passes/${item.id}`);
                                                        }
                                                    }}
                                                >
                                                    <Trash2 className="h-4 w-4 text-rose-600" />
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={dailyPasses} />
                    </>
                )}
            </Card>

            <Modal
                open={showCreate}
                onClose={() => setShowCreate(false)}
                title="Tambah Kunjungan Daily Pass"
            >
                <form onSubmit={submitCreate} className="space-y-4">
                    <div>
                        <InputLabel required>Nama Tamu</InputLabel>
                        <Input
                            value={form.data.full_name}
                            onChange={(e) => form.setData('full_name', e.target.value)}
                            error={form.errors.full_name}
                        />
                        <FieldError message={form.errors.full_name} />
                    </div>
                    <div>
                        <InputLabel>No. HP (Opsional)</InputLabel>
                        <Input
                            value={form.data.phone}
                            onChange={(e) => form.setData('phone', e.target.value)}
                        />
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel required>Nominal (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={form.data.payment_amount}
                                onChange={(e) => form.setData('payment_amount', e.target.value)}
                            />
                        </div>
                        <div>
                            <InputLabel required>Metode Bayar</InputLabel>
                            <Select
                                value={form.data.payment_method}
                                onChange={(e) => form.setData('payment_method', e.target.value)}
                            >
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer</option>
                            </Select>
                        </div>
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowCreate(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={form.processing}>
                            Simpan
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}

