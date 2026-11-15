import React, { useState } from 'react';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import {
    Calendar,
    CheckCircle2,
    Clock,
    CreditCard,
    DollarSign,
    KeyRound,
    Plus,
    QrCode,
    Search,
    Sparkles,
    User,
    UserCheck,
    UserPlus,
    Users,
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
import { cn, formatCurrency } from '../../lib/format';

export default function Members({
    members,
    packages = [],
    totalMembers = 0,
    activeMembers = 0,
    expiredMembers = 0,
    filters = {},
}) {
    const { flash = {} } = usePage().props;

    // Search and status filter
    const [search, setSearch] = useState(filters.q || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || 'all');

    // Modals state
    const [showRegisterModal, setShowRegisterModal] = useState(false);
    const [showRenewModal, setShowRenewModal] = useState(false);
    const [selectedMemberForRenew, setSelectedMemberForRenew] = useState(null);

    // Default package
    const defaultPkg = packages.length > 0 ? packages[0] : null;

    // Register Form
    const registerForm = useForm({
        full_name: '',
        phone: '',
        email: '',
        gender: 'male',
        birth_date: '',
        address: '',
        username: '',
        password: '',
        package_id: defaultPkg ? String(defaultPkg.id) : '',
        duration: defaultPkg ? Math.max(1, Math.round(defaultPkg.duration_days / 30)) : 1,
        discount: 0,
        payment_amount: defaultPkg ? defaultPkg.price : 90000,
        paid_amount: defaultPkg ? defaultPkg.price : 100000,
        payment_method: 'cash',
        notes: '',
        return_to: 'members',
    });

    // Renew Form
    const renewForm = useForm({
        gym_member_id: '',
        package_id: defaultPkg ? String(defaultPkg.id) : '',
        amount: defaultPkg ? defaultPkg.price : 90000,
        discount: 0,
        paid_amount: defaultPkg ? defaultPkg.price : 100000,
        payment_method: 'cash',
        notes: '',
        return_to: 'members',
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/cashier/members', { q: search, status: statusFilter }, { preserveState: true });
    };

    const handleStatusFilter = (st) => {
        setStatusFilter(st);
        router.get('/cashier/members', { q: search, status: st }, { preserveState: true });
    };

    // Open renewal modal for specific member
    const openRenewModal = (member) => {
        setSelectedMemberForRenew(member);
        const pkg = defaultPkg;
        const initialPrice = pkg ? pkg.price : 90000;
        renewForm.setData({
            gym_member_id: member.id,
            package_id: pkg ? String(pkg.id) : '',
            amount: initialPrice,
            discount: 0,
            paid_amount: initialPrice,
            payment_method: 'cash',
            notes: '',
            return_to: 'members',
        });
        setShowRenewModal(true);
    };

    // Handle package change in Register Modal
    const handleRegisterPackageChange = (pkgId) => {
        const pkg = packages.find((p) => String(p.id) === String(pkgId));
        if (pkg) {
            const finalPrice = Math.max(0, pkg.price - (Number(registerForm.data.discount) || 0));
            registerForm.setData((prev) => ({
                ...prev,
                package_id: String(pkg.id),
                duration: Math.max(1, Math.round(pkg.duration_days / 30)),
                payment_amount: finalPrice,
                paid_amount: prev.payment_method === 'cash' ? finalPrice : prev.paid_amount,
            }));
        }
    };

    // Handle discount change in Register Modal
    const handleRegisterDiscountChange = (disc) => {
        const d = Number(disc) || 0;
        const currentPkg = packages.find((p) => String(p.id) === String(registerForm.data.package_id)) || defaultPkg;
        const basePrice = currentPkg ? currentPkg.price : 90000;
        const finalPrice = Math.max(0, basePrice - d);
        registerForm.setData((prev) => ({
            ...prev,
            discount: d,
            payment_amount: finalPrice,
            paid_amount: prev.payment_method === 'cash' && prev.paid_amount < finalPrice ? finalPrice : prev.paid_amount,
        }));
    };

    // Submit Register Member
    const submitRegister = (e) => {
        e.preventDefault();
        registerForm.post('/cashier/transactions/register-member', {
            onSuccess: () => {
                setShowRegisterModal(false);
                registerForm.reset();
            },
        });
    };

    // Handle package change in Renew Modal
    const handleRenewPackageChange = (pkgId) => {
        const pkg = packages.find((p) => String(p.id) === String(pkgId));
        if (pkg) {
            const finalPrice = Math.max(0, pkg.price - (Number(renewForm.data.discount) || 0));
            renewForm.setData((prev) => ({
                ...prev,
                package_id: String(pkg.id),
                amount: finalPrice,
                paid_amount: prev.payment_method === 'cash' ? finalPrice : prev.paid_amount,
            }));
        }
    };

    // Handle discount change in Renew Modal
    const handleRenewDiscountChange = (disc) => {
        const d = Number(disc) || 0;
        const currentPkg = packages.find((p) => String(p.id) === String(renewForm.data.package_id)) || defaultPkg;
        const basePrice = currentPkg ? currentPkg.price : 90000;
        const finalPrice = Math.max(0, basePrice - d);
        renewForm.setData((prev) => ({
            ...prev,
            discount: d,
            amount: finalPrice,
            paid_amount: prev.payment_method === 'cash' ? finalPrice : prev.paid_amount,
        }));
    };

    // Submit Renew Member
    const submitRenew = (e) => {
        e.preventDefault();
        renewForm.post('/cashier/member-payments', {
            onSuccess: () => {
                setShowRenewModal(false);
                setSelectedMemberForRenew(null);
                renewForm.reset();
            },
        });
    };

    const registerChange =
        registerForm.data.payment_method === 'cash'
            ? Math.max(0, Number(registerForm.data.paid_amount || 0) - Number(registerForm.data.payment_amount || 0))
            : 0;

    const renewChange =
        renewForm.data.payment_method === 'cash'
            ? Math.max(0, Number(renewForm.data.paid_amount || 0) - Number(renewForm.data.amount || 0))
            : 0;

    const creds = flash.member_credentials;

    return (
        <AppLayout
            title="Kelola Data & Layanan Member"
            subtitle="Pusat data member terpadu: cek status aktif, pendaftaran member baru, dan perpanjangan paket"
            panel="cashier"
            active="members"
        >
            <Head title="Data & Kelola Member - Arena Gym" />

            <div className="space-y-5">
                {/* Credentials Alert after registration */}
                {creds && (
                    <Card className="border-emerald-200 bg-emerald-50/70 p-4">
                        <div className="flex items-start justify-between gap-3">
                            <div className="flex items-start gap-3">
                                <CheckCircle2 className="mt-0.5 h-5 w-5 text-emerald-600 shrink-0" />
                                <div className="text-xs">
                                    <p className="font-bold text-emerald-900 text-sm">
                                        Pendaftaran Member Baru Berhasil!
                                    </p>
                                    <p className="text-emerald-700 mt-0.5">
                                        Akun login untuk <span className="font-bold">{creds.name}</span> siap diberikan:
                                    </p>
                                    <div className="mt-2 flex flex-wrap gap-2 text-xs">
                                        <span className="rounded-lg bg-white px-2.5 py-1 font-mono font-semibold text-slate-800 border border-emerald-200">
                                            User/Email: {creds.username || creds.email}
                                        </span>
                                        <span className="rounded-lg bg-white px-2.5 py-1 font-mono font-semibold text-slate-800 border border-emerald-200">
                                            Password: {creds.password}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Card>
                )}

                {/* 1. COMPACT SUMMARY CARDS (TOTAL, AKTIF, EXPIRED) */}
                <div className="grid grid-cols-1 gap-3.5 sm:grid-cols-3">
                    <div className="flex items-center justify-between rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-2xs">
                        <div>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Total Member Terdaftar
                            </p>
                            <p className="mt-1 text-xl font-extrabold text-slate-900">
                                {totalMembers}
                                <span className="ml-1 text-xs font-normal text-slate-500">Orang</span>
                            </p>
                            <p className="mt-0.5 text-[11px] text-slate-500">Seluruh basis member gym</p>
                        </div>
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                            <Users className="h-5 w-5" />
                        </div>
                    </div>

                    <div className="flex items-center justify-between rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-2xs">
                        <div>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Member Aktif
                            </p>
                            <p className="mt-1 text-xl font-extrabold text-slate-900">
                                {activeMembers}
                                <span className="ml-1 text-xs font-normal text-slate-500">Orang</span>
                            </p>
                            <p className="mt-0.5 text-[11px] font-semibold text-emerald-600">Bisa check-in gym</p>
                        </div>
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <UserCheck className="h-5 w-5" />
                        </div>
                    </div>

                    <div className="flex items-center justify-between rounded-xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-2xs">
                        <div>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Member Expired
                            </p>
                            <p className="mt-1 text-xl font-extrabold text-slate-900">
                                {expiredMembers}
                                <span className="ml-1 text-xs font-normal text-slate-500">Orang</span>
                            </p>
                            <p className="mt-0.5 text-[11px] font-semibold text-red-600">Perlu perpanjangan</p>
                        </div>
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                            <Clock className="h-5 w-5" />
                        </div>
                    </div>
                </div>

                {/* 2. CONTROLS BAR: SEARCH, FILTERS, AND "DAFTAR MEMBER BARU" BUTTON */}
                <div className="flex flex-col gap-3 rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs sm:flex-row sm:items-center sm:justify-between">
                    <form onSubmit={handleSearch} className="flex flex-1 items-center gap-2 max-w-md">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input
                                type="text"
                                placeholder="Cari nama, kode AGM, email, HP..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full rounded-xl border border-slate-200 bg-slate-50/70 pl-9 pr-3 py-2 text-xs font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-red-500"
                            />
                        </div>
                        <Button type="submit" variant="secondary" size="sm">
                            Cari
                        </Button>
                    </form>

                    <div className="flex flex-wrap items-center gap-2">
                        {/* Status Pills */}
                        <div className="inline-flex rounded-xl bg-slate-100 p-1">
                            {[
                                { key: 'all', label: 'Semua' },
                                { key: 'active', label: 'Aktif' },
                                { key: 'expired', label: 'Expired' },
                            ].map((item) => (
                                <button
                                    key={item.key}
                                    type="button"
                                    onClick={() => handleStatusFilter(item.key)}
                                    className={cn(
                                        'rounded-lg px-3 py-1 text-xs font-bold transition',
                                        statusFilter === item.key
                                            ? 'bg-white text-red-600 shadow-2xs'
                                            : 'text-slate-600 hover:text-slate-900'
                                    )}
                                >
                                    {item.label}
                                </button>
                            ))}
                        </div>

                        {/* Button Daftar Member Baru (Unifying Pendaftaran Member) */}
                        <Button
                            type="button"
                            variant="primary"
                            size="sm"
                            onClick={() => setShowRegisterModal(true)}
                            className="gap-1.5"
                        >
                            <UserPlus className="h-4 w-4" />
                            Daftar Member Baru
                        </Button>
                    </div>
                </div>

                {/* 3. TABLE OF MEMBERS */}
                <div className="rounded-2xl border border-slate-200/90 bg-white shadow-2xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-100 bg-slate-50/60 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    <th className="py-3 px-4">Data Member</th>
                                    <th className="py-3 px-4">Kontak</th>
                                    <th className="py-3 px-4">Paket</th>
                                    <th className="py-3 px-4">Masa Aktif & Kedaluwarsa</th>
                                    <th className="py-3 px-4">Status</th>
                                    <th className="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-xs">
                                {members.data && members.data.length > 0 ? (
                                    members.data.map((m) => (
                                        <tr key={m.id} className="hover:bg-slate-50/80 transition">
                                            {/* Data Member */}
                                            <td className="py-3.5 px-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-600 text-white font-bold text-xs shadow-xs">
                                                        {m.profile_initials || m.full_name?.charAt(0) || 'M'}
                                                    </div>
                                                    <div>
                                                        <p className="font-bold text-slate-900">
                                                            {m.full_name}
                                                        </p>
                                                        <p className="font-mono text-[11px] font-bold text-red-600">
                                                            {m.checkin_code}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Kontak */}
                                            <td className="py-3.5 px-4 text-xs">
                                                <p className="font-semibold text-slate-800">{m.phone}</p>
                                                <p className="text-slate-400 text-[11px]">{m.email || '-'}</p>
                                            </td>

                                            {/* Paket */}
                                            <td className="py-3.5 px-4">
                                                <span className="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800">
                                                    {m.membership_plan}
                                                </span>
                                            </td>

                                            {/* Masa Aktif */}
                                            <td className="py-3.5 px-4 text-xs">
                                                <div className="flex items-center gap-1.5 font-medium text-slate-800">
                                                    <Calendar className="h-3.5 w-3.5 text-slate-400" />
                                                    <span>Berakhir: {m.expires_at_label}</span>
                                                </div>
                                                <p className="mt-0.5 text-[11px]">
                                                    {m.remaining_days > 0 ? (
                                                        <span className="font-bold text-emerald-600">
                                                            Tersisa {m.remaining_days} hari
                                                        </span>
                                                    ) : (
                                                        <span className="font-bold text-red-600">
                                                            Sudah berakhir
                                                        </span>
                                                    )}
                                                </p>
                                            </td>

                                            {/* Status */}
                                            <td className="py-3.5 px-4">
                                                {m.remaining_days > 0 ? (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200/60 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">
                                                        <CheckCircle2 className="h-3 w-3" />
                                                        Aktif
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-red-50 border border-red-200/60 px-2.5 py-0.5 text-[11px] font-bold text-red-600">
                                                        <Clock className="h-3 w-3" />
                                                        Expired
                                                    </span>
                                                )}
                                            </td>

                                            {/* Aksi Perpanjangan Terpadu */}
                                            <td className="py-3.5 px-4 text-right">
                                                <Button
                                                    variant="primary"
                                                    size="xs"
                                                    onClick={() => openRenewModal(m)}
                                                    className="gap-1 shadow-2xs"
                                                >
                                                    <CreditCard className="h-3.5 w-3.5" />
                                                    Perpanjang
                                                </Button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan={6} className="py-12 text-center text-slate-400">
                                            Tidak ada data member yang sesuai filter saat ini.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {members.links && members.links.length > 3 && (
                        <div className="p-4 border-t border-slate-100">
                            <Pagination links={members.links} />
                        </div>
                    )}
                </div>
            </div>

            {/* MODAL 1: REGISTRASI MEMBER BARU (FUNGSI PENDAFTARAN MEMBER) */}
            <Modal
                open={showRegisterModal}
                onClose={() => setShowRegisterModal(false)}
                title="Daftar Member Baru"
                subtitle="Formulir pendaftaran keanggotaan baru, paket aktivasi, dan pembuatan akun login"
                maxWidth="max-w-2xl"
            >
                <form onSubmit={submitRegister} className="space-y-4">
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel required>Nama Lengkap</InputLabel>
                            <Input
                                placeholder="Cth: Budi Santoso"
                                value={registerForm.data.full_name}
                                onChange={(e) => registerForm.setData('full_name', e.target.value)}
                                error={registerForm.errors.full_name}
                                required
                            />
                        </div>

                        <div>
                            <InputLabel required>Nomor WhatsApp / HP</InputLabel>
                            <Input
                                placeholder="Cth: 08123456789"
                                value={registerForm.data.phone}
                                onChange={(e) => registerForm.setData('phone', e.target.value)}
                                error={registerForm.errors.phone}
                                required
                            />
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel>Email (Opsional)</InputLabel>
                            <Input
                                type="email"
                                placeholder="Cth: budi@gmail.com"
                                value={registerForm.data.email}
                                onChange={(e) => registerForm.setData('email', e.target.value)}
                                error={registerForm.errors.email}
                            />
                        </div>

                        <div>
                            <InputLabel required>Pilihan Paket Membership</InputLabel>
                            <Select
                                value={registerForm.data.package_id}
                                onChange={(e) => handleRegisterPackageChange(e.target.value)}
                                error={registerForm.errors.package_id}
                                required
                            >
                                {packages.map((pkg) => (
                                    <option key={pkg.id} value={pkg.id}>
                                        {pkg.name} — {formatCurrency(pkg.price)} ({pkg.duration_days} hari)
                                    </option>
                                ))}
                            </Select>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <InputLabel required>Total Biaya (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={registerForm.data.payment_amount}
                                onChange={(e) => registerForm.setData('payment_amount', Number(e.target.value))}
                                error={registerForm.errors.payment_amount}
                                required
                            />
                        </div>

                        <div>
                            <InputLabel>Diskon / Potongan (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={registerForm.data.discount}
                                onChange={(e) => handleRegisterDiscountChange(e.target.value)}
                                error={registerForm.errors.discount}
                            />
                        </div>

                        <div>
                            <InputLabel required>Metode Pembayaran</InputLabel>
                            <Select
                                value={registerForm.data.payment_method}
                                onChange={(e) => registerForm.setData('payment_method', e.target.value)}
                                required
                            >
                                <option value="cash">Tunai (CASH)</option>
                                <option value="qris">QRIS Standar</option>
                            </Select>
                        </div>
                    </div>

                    {registerForm.data.payment_method === 'cash' && (
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 rounded-xl bg-slate-50 p-3 border border-slate-200">
                            <div>
                                <InputLabel required>Uang Diterima dari Member</InputLabel>
                                <Input
                                    type="number"
                                    value={registerForm.data.paid_amount}
                                    onChange={(e) => registerForm.setData('paid_amount', Number(e.target.value))}
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel>Kembalian</InputLabel>
                                <p className="mt-2 text-base font-extrabold text-emerald-600">
                                    {formatCurrency(registerChange)}
                                </p>
                            </div>
                        </div>
                    )}

                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel>Username Login (Opsional)</InputLabel>
                            <Input
                                placeholder="Kosongkan jika ingin dibuatkan otomatis"
                                value={registerForm.data.username}
                                onChange={(e) => registerForm.setData('username', e.target.value)}
                                error={registerForm.errors.username}
                            />
                        </div>

                        <div>
                            <InputLabel>Password Login (Opsional)</InputLabel>
                            <Input
                                type="password"
                                placeholder="Default: member123"
                                value={registerForm.data.password}
                                onChange={(e) => registerForm.setData('password', e.target.value)}
                                error={registerForm.errors.password}
                            />
                        </div>
                    </div>

                    <div>
                        <InputLabel>Alamat Lengkap / Catatan</InputLabel>
                        <Textarea
                            placeholder="Catatan tambahan (opsional)..."
                            rows={2}
                            value={registerForm.data.notes}
                            onChange={(e) => registerForm.setData('notes', e.target.value)}
                        />
                    </div>

                    <div className="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => setShowRegisterModal(false)}
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            variant="primary"
                            disabled={registerForm.processing}
                        >
                            <CheckCircle2 className="h-4 w-4" />
                            {registerForm.processing ? 'Menyimpan...' : 'Daftarkan & Bayar'}
                        </Button>
                    </div>
                </form>
            </Modal>

            {/* MODAL 2: PERPANJANGAN MEMBER (FUNGSI PERPANJANG MEMBER) */}
            <Modal
                open={showRenewModal}
                onClose={() => {
                    setShowRenewModal(false);
                    setSelectedMemberForRenew(null);
                }}
                title="Perpanjangan Membership"
                subtitle="Perpanjang durasi keanggotaan member dan catat bukti pembayaran"
                maxWidth="max-w-lg"
            >
                {selectedMemberForRenew && (
                    <form onSubmit={submitRenew} className="space-y-4">
                        {/* Member Summary Header */}
                        <div className="rounded-xl border border-red-200 bg-red-50/50 p-3.5">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-xs font-bold text-slate-900">
                                        {selectedMemberForRenew.full_name}
                                    </p>
                                    <p className="font-mono text-[11px] text-red-600 font-semibold">
                                        ID Barcode: {selectedMemberForRenew.checkin_code}
                                    </p>
                                </div>
                                <span className="rounded-md bg-white px-2 py-0.5 text-xs font-semibold text-slate-700 border border-red-200">
                                    {selectedMemberForRenew.membership_plan}
                                </span>
                            </div>
                            <div className="mt-2 flex items-center justify-between text-[11px] text-slate-500 border-t border-red-100 pt-1.5">
                                <span>Kedaluwarsa Saat Ini:</span>
                                <span className="font-semibold text-slate-800">
                                    {selectedMemberForRenew.expires_at_label}
                                </span>
                            </div>
                        </div>

                        <div>
                            <InputLabel required>Pilihan Paket Perpanjangan</InputLabel>
                            <Select
                                value={renewForm.data.package_id}
                                onChange={(e) => handleRenewPackageChange(e.target.value)}
                                error={renewForm.errors.package_id}
                                required
                            >
                                {packages.map((pkg) => (
                                    <option key={pkg.id} value={pkg.id}>
                                        {pkg.name} — {formatCurrency(pkg.price)} (+{pkg.duration_days} hari)
                                    </option>
                                ))}
                            </Select>
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <InputLabel required>Nominal Bayar (Rp)</InputLabel>
                                <Input
                                    type="number"
                                    value={renewForm.data.amount}
                                    onChange={(e) => renewForm.setData('amount', Number(e.target.value))}
                                    error={renewForm.errors.amount}
                                    required
                                />
                            </div>

                            <div>
                                <InputLabel>Diskon (Rp)</InputLabel>
                                <Input
                                    type="number"
                                    value={renewForm.data.discount}
                                    onChange={(e) => handleRenewDiscountChange(e.target.value)}
                                    error={renewForm.errors.discount}
                                />
                            </div>
                        </div>

                        <div>
                            <InputLabel required>Metode Pembayaran</InputLabel>
                            <Select
                                value={renewForm.data.payment_method}
                                onChange={(e) => renewForm.setData('payment_method', e.target.value)}
                                required
                            >
                                <option value="cash">Tunai (CASH)</option>
                                <option value="qris">QRIS Standar</option>
                            </Select>
                        </div>

                        {renewForm.data.payment_method === 'cash' && (
                            <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 rounded-xl bg-slate-50 p-3 border border-slate-200">
                                <div>
                                    <InputLabel required>Uang Diterima</InputLabel>
                                    <Input
                                        type="number"
                                        value={renewForm.data.paid_amount}
                                        onChange={(e) => renewForm.setData('paid_amount', Number(e.target.value))}
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel>Kembalian</InputLabel>
                                    <p className="mt-2 text-base font-extrabold text-emerald-600">
                                        {formatCurrency(renewChange)}
                                    </p>
                                </div>
                            </div>
                        )}

                        <div>
                            <InputLabel>Catatan (Opsional)</InputLabel>
                            <Textarea
                                placeholder="Catatan perpanjangan member..."
                                rows={2}
                                value={renewForm.data.notes}
                                onChange={(e) => renewForm.setData('notes', e.target.value)}
                            />
                        </div>

                        <div className="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <Button
                                type="button"
                                variant="secondary"
                                onClick={() => {
                                    setShowRenewModal(false);
                                    setSelectedMemberForRenew(null);
                                }}
                            >
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                variant="primary"
                                disabled={renewForm.processing}
                            >
                                <CheckCircle2 className="h-4 w-4" />
                                {renewForm.processing ? 'Memproses...' : 'Konfirmasi Perpanjangan'}
                            </Button>
                        </div>
                    </form>
                )}
            </Modal>
        </AppLayout>
    );
}
