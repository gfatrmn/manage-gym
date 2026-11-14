import React, { useEffect, useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { Calendar, CheckCircle2, Clock, CreditCard, Search, Sparkles } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    FieldError,
    Input,
    InputLabel,
    Pagination,
    Select,
    Textarea,
} from '../../Components/UI';
import { formatCurrency, formatDate, formatRupiah } from '../../lib/format';

export default function MemberPayments({
    members,
    memberSearch = '',
    memberPayments,
    packages = [],
}) {
    const [search, setSearch] = useState(memberSearch || '');
    const [selectedMember, setSelectedMember] = useState(null);

    const urlParams = new URLSearchParams(window.location.search);
    const initialMemberId = urlParams.get('member_id');
    const initialPkgId = urlParams.get('package_id') || (packages.length > 0 ? String(packages[0].id) : '');

    const selectedPkg = packages.find((p) => String(p.id) === String(initialPkgId)) || packages[0];
    const initialAmount = selectedPkg ? selectedPkg.price : 90000;

    const form = useForm({
        gym_member_id: initialMemberId || '',
        package_id: initialPkgId || '',
        amount: initialAmount,
        discount: 0,
        paid_amount: initialAmount,
        payment_method: 'cash',
        notes: '',
    });

    useEffect(() => {
        if (initialMemberId && members?.data) {
            const found = members.data.find((m) => String(m.id) === String(initialMemberId));
            if (found) {
                setSelectedMember(found);
            }
        }
    }, [initialMemberId, members]);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/cashier/member-payments', { member_q: search }, { preserveState: true });
    };

    const selectMember = (m) => {
        setSelectedMember(m);
        form.setData('gym_member_id', m.id);
    };

    const handlePackageChange = (pkgId) => {
        const pkg = packages.find((p) => String(p.id) === String(pkgId));
        if (pkg) {
            const finalPrice = Math.max(0, pkg.price - (Number(form.data.discount) || 0));
            form.setData((prev) => ({
                ...prev,
                package_id: pkg.id,
                amount: finalPrice,
                paid_amount: prev.payment_method === 'cash' ? finalPrice : prev.paid_amount,
            }));
        } else {
            form.setData((prev) => ({
                ...prev,
                package_id: '',
                amount: 90000,
                paid_amount: prev.payment_method === 'cash' ? 90000 : prev.paid_amount,
            }));
        }
    };

    const handleDiscountChange = (disc) => {
        const d = Number(disc) || 0;
        const basePrice = selectedPkg ? selectedPkg.price : 90000;
        const finalPrice = Math.max(0, basePrice - d);
        form.setData((prev) => ({
            ...prev,
            discount: d,
            amount: finalPrice,
            paid_amount: prev.payment_method === 'cash' ? finalPrice : prev.paid_amount,
        }));
    };

    const submit = (e) => {
        e.preventDefault();
        form.post('/cashier/member-payments', {
            onSuccess: () => {
                setSelectedMember(null);
                form.reset('gym_member_id', 'notes');
            },
        });
    };

    const currentTotal = Number(form.data.amount || 90000);
    const change =
        form.data.payment_method === 'cash'
            ? Math.max(0, Number(form.data.paid_amount || 0) - currentTotal)
            : 0;

    const memberRows = members?.data || [];
    const paymentRows = memberPayments?.data || [];

    // Calculate preview renewal expiry (BR-011)
    const calculatePreviewExpiry = () => {
        if (!selectedMember) return null;
        const pkg = packages.find((p) => String(p.id) === String(form.data.package_id)) || { duration_days: 30 };
        const durationDays = pkg.duration_days;
        
        // If member is still active, starts after current expires_at
        return `+${durationDays} hari (Masa aktif tidak terpotong)`;
    };

    return (
        <AppLayout
            title="Perpanjangan & Pembayaran Member"
            subtitle="Pilih paket membership dan proses pembayaran perpanjangan masa aktif via Cash atau QRIS"
            panel="cashier"
            active="member-payments"
        >
            <div className="grid gap-6 lg:grid-cols-3">
                {/* Member selector */}
                <Card className="lg:col-span-2">
                    <div className="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <h2 className="text-base font-bold text-slate-900 dark:text-white">
                            1. Pilih Member Yang Akan Diperpanjang
                        </h2>
                        <form onSubmit={handleSearch} className="flex gap-2">
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari nama, HP, email, kode..."
                                className="w-56"
                            />
                            <Button type="submit" variant="secondary" size="sm">
                                <Search className="h-4 w-4" />
                            </Button>
                        </form>
                    </div>

                    <div className="space-y-2">
                        {memberRows.map((m) => {
                            const isSelected = Number(form.data.gym_member_id) === m.id;
                            return (
                                <div
                                    key={m.id}
                                    onClick={() => selectMember(m)}
                                    className={`flex cursor-pointer flex-wrap items-center justify-between gap-3 rounded-xl border p-3.5 transition ${
                                        isSelected
                                            ? 'border-rose-500 bg-rose-500/10'
                                            : 'border-slate-200 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/60'
                                    }`}
                                >
                                    <div>
                                        <p className="font-bold text-slate-900 dark:text-white">
                                            {m.full_name}
                                        </p>
                                        <p className="text-xs text-slate-500">
                                            {m.checkin_code || '-'} · {m.phone || '-'} · Masa Berlaku:{' '}
                                            <span className="font-semibold text-slate-700 dark:text-slate-300">
                                                {m.expires_at_label}
                                            </span>
                                        </p>
                                    </div>
                                    <Badge
                                        variant={
                                            m.lifecycle?.badge_tone === 'danger'
                                                ? 'danger'
                                                : m.lifecycle?.badge_tone === 'warning'
                                                  ? 'warning'
                                                  : 'success'
                                        }
                                    >
                                        {m.lifecycle?.label || m.status}
                                    </Badge>
                                </div>
                            );
                        })}
                    </div>
                    <Pagination paginator={members} />
                </Card>

                {/* Payment Form */}
                <Card>
                    <h2 className="mb-4 flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                        <CreditCard className="h-4 w-4 text-rose-500" />
                        2. Detail Paket & Kasir
                    </h2>

                    {selectedMember ? (
                        <div className="mb-4 rounded-xl bg-slate-100 p-3.5 text-xs dark:bg-slate-800">
                            <p className="text-slate-500">Member Dipilih:</p>
                            <p className="text-sm font-bold text-slate-900 dark:text-white">
                                {selectedMember.full_name}
                            </p>
                            <p className="mt-1 text-slate-500">
                                Status: <span className="font-semibold">{selectedMember.lifecycle?.label}</span> (s/d {selectedMember.expires_at_label})
                            </p>
                            <div className="mt-2 rounded-lg bg-emerald-500/10 p-2 text-emerald-700 dark:text-emerald-300 font-medium">
                                <Clock className="inline mr-1 h-3.5 w-3.5" />
                                {calculatePreviewExpiry()}
                            </div>
                        </div>
                    ) : (
                        <p className="mb-4 rounded-xl border border-dashed border-slate-300 p-3 text-xs text-slate-500 dark:border-slate-700">
                            Pilih salah satu member di sebelah kiri untuk memproses perpanjangan paket.
                        </p>
                    )}

                    <form onSubmit={submit} className="space-y-4">
                        <FieldError message={form.errors.gym_member_id} />

                        {packages.length > 0 && (
                            <div>
                                <InputLabel required>Pilihan Paket Membership</InputLabel>
                                <Select
                                    value={form.data.package_id}
                                    onChange={(e) => handlePackageChange(e.target.value)}
                                >
                                    {packages.map((pkg) => (
                                        <option key={pkg.id} value={pkg.id}>
                                            {pkg.name} ({pkg.duration_days} Hari - {formatCurrency(pkg.price)})
                                        </option>
                                    ))}
                                </Select>
                            </div>
                        )}

                        <div>
                            <InputLabel>Diskon / Potongan Kasir (Rp)</InputLabel>
                            <Input
                                type="number"
                                min="0"
                                step="1000"
                                value={form.data.discount}
                                onChange={(e) => handleDiscountChange(e.target.value)}
                            />
                        </div>

                        <div>
                            <InputLabel required>Total Tagihan (Rp)</InputLabel>
                            <div className="rounded-xl bg-slate-100 px-3.5 py-2.5 text-base font-extrabold text-slate-900 dark:bg-slate-800 dark:text-white">
                                {formatRupiah(currentTotal)}
                            </div>
                        </div>

                        <div>
                            <InputLabel required>Metode Pembayaran</InputLabel>
                            <Select
                                value={form.data.payment_method}
                                onChange={(e) => form.setData('payment_method', e.target.value)}
                            >
                                <option value="cash">Cash (Tunai Langsung)</option>
                                <option value="qris">QRIS (Menunggu Verifikasi)</option>
                            </Select>
                        </div>

                        {form.data.payment_method === 'cash' && (
                            <div>
                                <InputLabel required>Uang Diterima dari Member (Rp)</InputLabel>
                                <Input
                                    type="number"
                                    value={form.data.paid_amount}
                                    onChange={(e) => form.setData('paid_amount', parseInt(e.target.value) || 0)}
                                    error={form.errors.paid_amount}
                                    required
                                />
                                <FieldError message={form.errors.paid_amount} />
                                <p className="mt-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    Kembalian Kasir: {formatRupiah(change)}
                                </p>
                            </div>
                        )}

                        <div>
                            <InputLabel>Catatan (Opsional)</InputLabel>
                            <Textarea
                                rows={2}
                                value={form.data.notes}
                                onChange={(e) => form.setData('notes', e.target.value)}
                                placeholder="Catatan perpanjangan..."
                            />
                        </div>

                        <Button
                            type="submit"
                            disabled={form.processing || !form.data.gym_member_id}
                            className="w-full gap-2"
                            size="lg"
                        >
                            <CreditCard className="h-4 w-4" />
                            Proses Perpanjangan
                        </Button>
                    </form>
                </Card>
            </div>
        </AppLayout>
    );
}
