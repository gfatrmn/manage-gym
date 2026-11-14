import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import {
    Download,
    Edit3,
    History,
    Search,
    Trash2,
    UserPlus,
    Users,
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
    StatCard,
    Textarea,
} from '../../Components/UI';
import { cn } from '../../lib/format';

export default function Members({
    memberSection = 'active',
    memberSearch = '',
    members,
    totalActiveCount = 0,
    totalExpiredCount = 0,
    totalMembersCount = 0,
    expiringSoonCount = 0,
}) {
    const [search, setSearch] = useState(memberSearch || '');
    const [showCreate, setShowCreate] = useState(false);
    const [editingMember, setEditingMember] = useState(null);
    const [historyMember, setHistoryMember] = useState(null);

    const createForm = useForm({
        full_name: '',
        email: '',
        phone: '',
        joined_at: new Date().toISOString().slice(0, 10),
        payment_method: 'cash',
        notes: '',
        profile_photo: null,
    });

    const editForm = useForm({
        full_name: '',
        email: '',
        phone: '',
        joined_at: '',
        payment_method: 'cash',
        duration: '',
        notes: '',
        profile_photo: null,
    });

    const switchSection = (section) => {
        router.get('/admin/members', { section, q: search }, { preserveState: true });
    };

    const submitSearch = (e) => {
        e.preventDefault();
        router.get('/admin/members', { section: memberSection, q: search }, { preserveState: true });
    };

    const openEdit = (m) => {
        setEditingMember(m);
        editForm.setData({
            full_name: m.full_name || '',
            email: m.email || '',
            phone: m.phone || '',
            joined_at: m.joined_at || '',
            payment_method: m.payment_method || 'cash',
            duration: '',
            notes: m.notes || '',
            profile_photo: null,
        });
    };

    const submitCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/members', {
            forceFormData: true,
            onSuccess: () => {
                createForm.reset();
                setShowCreate(false);
            },
        });
    };

    const submitEdit = (e) => {
        e.preventDefault();
        if (!editingMember) return;
        router.post(
            `/admin/members/${editingMember.id}`,
            {
                _method: 'put',
                ...editForm.data,
            },
            {
                forceFormData: true,
                onSuccess: () => setEditingMember(null),
            }
        );
    };

    const deleteMember = (m) => {
        if (confirm(`Hapus data member "${m.full_name}"?`)) {
            router.delete(`/admin/members/${m.id}`);
        }
    };

    const rows = members?.data || [];

    return (
        <AppLayout
            title="Manajemen Member"
            subtitle="Kelola data member aktif, masa kedaluwarsa, perpanjangan, dan riwayat latihan"
            panel="admin"
            active="members"
        >
            <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard title="Total Member" value={totalMembersCount} icon={Users} accent="rose" />
                <StatCard title="Member Aktif" value={totalActiveCount} icon={Users} accent="emerald" />
                <StatCard title="Jatuh Tempo (≤7 Hari)" value={expiringSoonCount} icon={Users} accent="rose" />
                <StatCard title="Expired" value={totalExpiredCount} icon={Users} accent="sky" />
            </div>

            <Card>
                <div className="mb-5 flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div className="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            onClick={() => switchSection('active')}
                            className={cn(
                                'rounded-xl px-4 py-2 text-xs font-bold transition',
                                memberSection === 'active'
                                    ? 'bg-rose-600 text-white shadow-sm'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300'
                            )}
                        >
                            Member Aktif ({totalActiveCount})
                        </button>
                        <button
                            type="button"
                            onClick={() => switchSection('expired')}
                            className={cn(
                                'rounded-xl px-4 py-2 text-xs font-bold transition',
                                memberSection === 'expired'
                                    ? 'bg-rose-600 text-white shadow-sm'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300'
                            )}
                        >
                            Expired ({totalExpiredCount})
                        </button>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <form onSubmit={submitSearch} className="flex items-center gap-2">
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari nama, email, HP, kode..."
                                className="w-56"
                            />
                            <Button type="submit" variant="secondary" size="sm">
                                <Search className="h-4 w-4" />
                                Cari
                            </Button>
                        </form>
                        <a
                            href="/admin/export/member-data"
                            className="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >
                            <Download className="h-4 w-4" />
                            Export Excel
                        </a>
                        <Button size="sm" onClick={() => setShowCreate(true)}>
                            <UserPlus className="h-4 w-4" />
                            Tambah Member
                        </Button>
                    </div>
                </div>

                {rows.length === 0 ? (
                    <EmptyState
                        icon={Users}
                        title="Tidak ada data member"
                        description="Belum ada member pada kategori atau kata kunci pencarian ini."
                    />
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                        <th className="pb-3 pr-4">Member</th>
                                        <th className="pb-3 pr-4">Kontak</th>
                                        <th className="pb-3 pr-4">Kode QR</th>
                                        <th className="pb-3 pr-4">Masa Aktif</th>
                                        <th className="pb-3 pr-4">Status</th>
                                        <th className="pb-3 pr-4">Aktivitas</th>
                                        <th className="pb-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                    {rows.map((m) => (
                                        <tr key={m.id}>
                                            <td className="py-3.5 pr-4">
                                                <div className="flex items-center gap-3">
                                                    {m.profile_photo_url ? (
                                                        <img
                                                            src={m.profile_photo_url}
                                                            alt={m.full_name}
                                                            className="h-10 w-10 rounded-xl object-cover"
                                                        />
                                                    ) : (
                                                        <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/10 text-xs font-bold text-rose-600 dark:text-rose-400">
                                                            {m.profile_initials}
                                                        </div>
                                                    )}
                                                    <div>
                                                        <p className="font-semibold text-slate-900 dark:text-white">
                                                            {m.full_name}
                                                        </p>
                                                        <p className="text-xs text-slate-500 dark:text-slate-400">
                                                            {m.membership_plan} · {m.payment_method_label}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="py-3.5 pr-4 text-xs">
                                                <p className="text-slate-700 dark:text-slate-200">{m.email}</p>
                                                <p className="text-slate-500 dark:text-slate-400">{m.phone || '-'}</p>
                                            </td>
                                            <td className="py-3.5 pr-4 font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                                                {m.checkin_code}
                                            </td>
                                            <td className="py-3.5 pr-4 text-xs">
                                                <p className="text-slate-500 dark:text-slate-400">Mulai: {m.joined_at_label}</p>
                                                <p className="font-semibold text-slate-800 dark:text-slate-200">
                                                    Habis: {m.expires_at_label}
                                                </p>
                                            </td>
                                            <td className="py-3.5 pr-4">
                                                <Badge
                                                    variant={
                                                        m.lifecycle?.badge_tone === 'danger'
                                                            ? 'danger'
                                                            : m.lifecycle?.badge_tone === 'warning'
                                                              ? 'warning'
                                                              : 'success'
                                                    }
                                                >
                                                    {m.lifecycle?.label || 'Aktif'}
                                                </Badge>
                                            </td>
                                            <td className="py-3.5 pr-4 text-xs text-slate-600 dark:text-slate-300">
                                                <p>{m.checkins_count}x Check-in</p>
                                                <p>{m.product_transactions_count}x Beli Produk</p>
                                            </td>
                                            <td className="py-3.5 text-right">
                                                <div className="inline-flex items-center gap-1">
                                                    <Button
                                                        variant="ghost"
                                                        size="xs"
                                                        onClick={() => setHistoryMember(m)}
                                                        title="Lihat Riwayat"
                                                    >
                                                        <History className="h-4 w-4" />
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="xs"
                                                        onClick={() => openEdit(m)}
                                                        title="Edit / Perpanjang"
                                                    >
                                                        <Edit3 className="h-4 w-4 text-sky-600" />
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="xs"
                                                        onClick={() => deleteMember(m)}
                                                        title="Hapus"
                                                    >
                                                        <Trash2 className="h-4 w-4 text-rose-600" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={members} />
                    </>
                )}
            </Card>

            {/* Modal Tambah Member */}
            <Modal
                open={showCreate}
                onClose={() => setShowCreate(false)}
                title="Tambah Member Baru"
                subtitle="Mendaftarkan member baru sekaligus akun aktivasi portal"
            >
                <form onSubmit={submitCreate} className="space-y-4">
                    <div>
                        <InputLabel required>Nama Lengkap</InputLabel>
                        <Input
                            value={createForm.data.full_name}
                            onChange={(e) => createForm.setData('full_name', e.target.value)}
                            error={createForm.errors.full_name}
                        />
                        <FieldError message={createForm.errors.full_name} />
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel required>Email</InputLabel>
                            <Input
                                type="email"
                                value={createForm.data.email}
                                onChange={(e) => createForm.setData('email', e.target.value)}
                                error={createForm.errors.email}
                            />
                            <FieldError message={createForm.errors.email} />
                        </div>
                        <div>
                            <InputLabel>No. WhatsApp / HP</InputLabel>
                            <Input
                                value={createForm.data.phone}
                                onChange={(e) => createForm.setData('phone', e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel required>Tanggal Bergabung</InputLabel>
                            <Input
                                type="date"
                                value={createForm.data.joined_at}
                                onChange={(e) => createForm.setData('joined_at', e.target.value)}
                            />
                        </div>
                        <div>
                            <InputLabel required>Metode Pembayaran</InputLabel>
                            <Select
                                value={createForm.data.payment_method}
                                onChange={(e) => createForm.setData('payment_method', e.target.value)}
                            >
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer Bank</option>
                            </Select>
                        </div>
                    </div>
                    <div>
                        <InputLabel>Foto Profil (Opsional)</InputLabel>
                        <input
                            type="file"
                            accept="image/*"
                            onChange={(e) => createForm.setData('profile_photo', e.target.files?.[0] || null)}
                            className="block w-full rounded-xl border border-slate-200 p-2 text-xs dark:border-slate-700"
                        />
                    </div>
                    <div>
                        <InputLabel>Catatan</InputLabel>
                        <Textarea
                            rows={2}
                            value={createForm.data.notes}
                            onChange={(e) => createForm.setData('notes', e.target.value)}
                        />
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowCreate(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={createForm.processing}>
                            Simpan Member
                        </Button>
                    </div>
                </form>
            </Modal>

            {/* Modal Edit & Perpanjang Member */}
            <Modal
                open={Boolean(editingMember)}
                onClose={() => setEditingMember(null)}
                title={`Edit / Perpanjang: ${editingMember?.full_name || ''}`}
                subtitle="Kosongkan kolom Perpanjang Durasi jika hanya mengubah biodata"
            >
                <form onSubmit={submitEdit} className="space-y-4">
                    <div>
                        <InputLabel required>Nama Lengkap</InputLabel>
                        <Input
                            value={editForm.data.full_name}
                            onChange={(e) => editForm.setData('full_name', e.target.value)}
                        />
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel>Email</InputLabel>
                            <Input
                                type="email"
                                value={editForm.data.email}
                                onChange={(e) => editForm.setData('email', e.target.value)}
                            />
                        </div>
                        <div>
                            <InputLabel>No. HP</InputLabel>
                            <Input
                                value={editForm.data.phone}
                                onChange={(e) => editForm.setData('phone', e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel>Perpanjang Durasi</InputLabel>
                            <Select
                                value={editForm.data.duration}
                                onChange={(e) => editForm.setData('duration', e.target.value)}
                            >
                                <option value="">-- Tidak Diperpanjang --</option>
                                <option value="1">1 Bulan (Rp 90.000)</option>
                                <option value="3">3 Bulan (Rp 270.000)</option>
                                <option value="6">6 Bulan (Rp 540.000)</option>
                                <option value="12">12 Bulan (Rp 1.080.000)</option>
                            </Select>
                        </div>
                        <div>
                            <InputLabel required>Metode Pembayaran</InputLabel>
                            <Select
                                value={editForm.data.payment_method}
                                onChange={(e) => editForm.setData('payment_method', e.target.value)}
                            >
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer Bank</option>
                            </Select>
                        </div>
                    </div>
                    <div>
                        <InputLabel>Ganti Foto Profil (Opsional)</InputLabel>
                        <input
                            type="file"
                            accept="image/*"
                            onChange={(e) => editForm.setData('profile_photo', e.target.files?.[0] || null)}
                            className="block w-full rounded-xl border border-slate-200 p-2 text-xs dark:border-slate-700"
                        />
                    </div>
                    <div>
                        <InputLabel>Catatan</InputLabel>
                        <Textarea
                            rows={2}
                            value={editForm.data.notes}
                            onChange={(e) => editForm.setData('notes', e.target.value)}
                        />
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setEditingMember(null)}>
                            Batal
                        </Button>
                        <Button type="submit">Simpan Perubahan</Button>
                    </div>
                </form>
            </Modal>

            {/* Modal Riwayat Member */}
            <Modal
                open={Boolean(historyMember)}
                onClose={() => setHistoryMember(null)}
                title={`Riwayat Member: ${historyMember?.full_name || ''}`}
                maxWidth="max-w-2xl"
            >
                {historyMember && (
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <h4 className="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">
                                Log Check-in Terbaru
                            </h4>
                            {historyMember.checkin_logs?.length ? (
                                <div className="space-y-2">
                                    {historyMember.checkin_logs.map((log) => (
                                        <div
                                            key={log.id}
                                            className="rounded-xl bg-slate-100 p-2.5 text-xs dark:bg-slate-800"
                                        >
                                            <p className="font-semibold">{log.checked_in_at}</p>
                                            <p className="text-slate-500">{log.method}</p>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-xs text-slate-400">Belum ada check-in.</p>
                            )}
                        </div>
                        <div>
                            <h4 className="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">
                                Pembelian Produk
                            </h4>
                            {historyMember.product_logs?.length ? (
                                <div className="space-y-2">
                                    {historyMember.product_logs.map((log) => (
                                        <div
                                            key={log.id}
                                            className="rounded-xl bg-slate-100 p-2.5 text-xs dark:bg-slate-800"
                                        >
                                            <p className="font-semibold">
                                                {log.title} ({log.quantity}x)
                                            </p>
                                            <p className="text-slate-500">
                                                {log.amount} · {log.occurred_at}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-xs text-slate-400">Belum ada pembelian produk.</p>
                            )}
                        </div>
                    </div>
                )}
            </Modal>
        </AppLayout>
    );
}

