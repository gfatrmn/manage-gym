import React, { useEffect, useState } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { CheckCircle2, CreditCard, DollarSign, KeyRound, Sparkles, UserPlus } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    FieldError,
    Input,
    InputLabel,
    Select,
    Textarea,
} from '../../Components/UI';
import { formatCurrency, formatRupiah } from '../../lib/format';

export default function RegisterMember({
    registerMemberHistory = [],
    packages = [],
}) {
    const { flash = {} } = usePage().props;

    // Detect URL query param ?package_id=...
    const urlParams = new URLSearchParams(window.location.search);
    const initialPkgId = urlParams.get('package_id') || (packages.length > 0 ? String(packages[0].id) : '');

    const selectedPkg = packages.find((p) => String(p.id) === String(initialPkgId)) || packages[0];

    const form = useForm({
        full_name: '',
        phone: '',
        email: '',
        gender: 'male',
        birth_date: '',
        address: '',
        username: '',
        password: '',
        package_id: initialPkgId || '',
        duration: selectedPkg ? Math.max(1, Math.round(selectedPkg.duration_days / 30)) : 1,
        discount: 0,
        payment_amount: selectedPkg ? selectedPkg.price : 90000,
        paid_amount: selectedPkg ? selectedPkg.price : 100000,
        payment_method: 'cash',
        notes: '',
    });

    const handlePackageChange = (pkgId) => {
        const pkg = packages.find((p) => String(p.id) === String(pkgId));
        if (pkg) {
            const finalPrice = Math.max(0, pkg.price - (Number(form.data.discount) || 0));
            form.setData((prev) => ({
                ...prev,
                package_id: pkg.id,
                duration: Math.max(1, Math.round(pkg.duration_days / 30)),
                payment_amount: finalPrice,
                paid_amount: prev.payment_method === 'cash' ? finalPrice : prev.paid_amount,
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
            payment_amount: finalPrice,
            paid_amount: prev.payment_method === 'cash' && prev.paid_amount < finalPrice ? finalPrice : prev.paid_amount,
        }));
    };

    const submit = (e) => {
        e.preventDefault();
        form.post('/cashier/transactions/register-member', {
            onSuccess: () => {
                form.reset('full_name', 'phone', 'email', 'username', 'password', 'address', 'birth_date', 'notes');
            },
        });
    };

    const change =
        form.data.payment_method === 'cash'
            ? Math.max(0, Number(form.data.paid_amount || 0) - Number(form.data.payment_amount || 0))
            : 0;

    const creds = flash.member_credentials;

    return (
        <AppLayout
            title="Registrasi Member Baru"
            subtitle="Buat keanggotaan baru, data identitas, paket membership, dan cetak bukti pembayaran"
            panel="cashier"
            active="register-member"
        >
            {creds && (
                <Card className="mb-6 border-emerald-500/40 bg-emerald-500/10">
                    <div className="flex items-start gap-3">
                        <CheckCircle2 className="mt-0.5 h-5 w-5 text-emerald-500 shrink-0" />
                        <div className="text-sm">
                            <p className="font-bold text-emerald-900 dark:text-emerald-200">
                                Kredensial Member Siap Diberikan: {creds.name}
                            </p>
                            <p className="mt-0.5 text-xs text-emerald-700 dark:text-emerald-300">
                                Berikan informasi login ini kepada member untuk mengakses Portal Member Arena Gym.
                            </p>
                            <div className="mt-3 grid gap-2 rounded-xl bg-white/80 p-3 font-mono text-xs text-slate-900 dark:bg-slate-900/80 dark:text-white sm:grid-cols-3">
                                <div>
                                    <span className="text-slate-400">Username: </span>
                                    <strong>{creds.username}</strong>
                                </div>
                                <div>
                                    <span className="text-slate-400">Email: </span>
                                    <strong>{creds.email}</strong>
                                </div>
                                <div>
                                    <span className="text-slate-400">Password: </span>
                                    <strong>{creds.password}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>
            )}

            <div className="grid gap-6 lg:grid-cols-3">
                <Card className="lg:col-span-2">
                    <h2 className="mb-4 flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                        <UserPlus className="h-4 w-4 text-rose-500" />
                        Form Pendaftaran Member Baru
                    </h2>

                    <form onSubmit={submit} className="space-y-4">
                        {/* Data Diri */}
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel required>Nama Lengkap</InputLabel>
                                <Input
                                    value={form.data.full_name}
                                    onChange={(e) => form.setData('full_name', e.target.value)}
                                    placeholder="Contoh: Rian Pratama"
                                    error={form.errors.full_name}
                                    required
                                />
                                <FieldError message={form.errors.full_name} />
                            </div>

                            <div>
                                <InputLabel>No. WhatsApp / HP</InputLabel>
                                <Input
                                    value={form.data.phone}
                                    onChange={(e) => form.setData('phone', e.target.value)}
                                    placeholder="08123456789"
                                    error={form.errors.phone}
                                />
                                <FieldError message={form.errors.phone} />
                            </div>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-3">
                            <div>
                                <InputLabel required>Email</InputLabel>
                                <Input
                                    type="email"
                                    value={form.data.email}
                                    onChange={(e) => form.setData('email', e.target.value)}
                                    placeholder="member@email.com"
                                    error={form.errors.email}
                                    required
                                />
                                <FieldError message={form.errors.email} />
                            </div>

                            <div>
                                <InputLabel>Jenis Kelamin</InputLabel>
                                <Select
                                    value={form.data.gender}
                                    onChange={(e) => form.setData('gender', e.target.value)}
                                >
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </Select>
                            </div>

                            <div>
                                <InputLabel>Tanggal Lahir</InputLabel>
                                <Input
                                    type="date"
                                    value={form.data.birth_date}
                                    onChange={(e) => form.setData('birth_date', e.target.value)}
                                />
                            </div>
                        </div>

                        <div>
                            <InputLabel>Alamat Domisili</InputLabel>
                            <Input
                                value={form.data.address}
                                onChange={(e) => form.setData('address', e.target.value)}
                                placeholder="Alamat tinggal member..."
                            />
                        </div>

                        {/* Akun Login */}
                        <div className="border-t border-slate-100 pt-3 dark:border-slate-800">
                            <p className="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                                Akun Login Portal Member
                            </p>
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel required>Username Login</InputLabel>
                                    <Input
                                        value={form.data.username}
                                        onChange={(e) => form.setData('username', e.target.value)}
                                        placeholder="rianpratama"
                                        error={form.errors.username}
                                        required
                                    />
                                    <FieldError message={form.errors.username} />
                                </div>
                                <div>
                                    <InputLabel required>Password Awal (Min 8 karakter)</InputLabel>
                                    <Input
                                        type="password"
                                        value={form.data.password}
                                        onChange={(e) => form.setData('password', e.target.value)}
                                        placeholder="********"
                                        error={form.errors.password}
                                        required
                                    />
                                    <FieldError message={form.errors.password} />
                                </div>
                            </div>
                        </div>

                        {/* Paket & Pembayaran */}
                        <div className="border-t border-slate-100 pt-3 dark:border-slate-800">
                            <p className="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                                Paket Membership & Pembayaran
                            </p>
                            <div className="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <InputLabel required>Pilih Paket</InputLabel>
                                    {packages.length > 0 ? (
                                        <Select
                                            value={form.data.package_id}
                                            onChange={(e) => handlePackageChange(e.target.value)}
                                            required
                                        >
                                            {packages.map((pkg) => (
                                                <option key={pkg.id} value={pkg.id}>
                                                    {pkg.name} ({pkg.duration_days} Hari - {formatCurrency(pkg.price)})
                                                </option>
                                            ))}
                                        </Select>
                                    ) : (
                                        <Select
                                            value={form.data.duration}
                                            onChange={(e) => form.setData('duration', parseInt(e.target.value))}
                                        >
                                            <option value={1}>1 Bulan (Rp 90.000)</option>
                                            <option value={3}>3 Bulan (Rp 270.000)</option>
                                            <option value={6}>6 Bulan (Rp 540.000)</option>
                                            <option value={12}>12 Bulan (Rp 1.080.000)</option>
                                        </Select>
                                    )}
                                </div>

                                <div>
                                    <InputLabel>Diskon / Potongan (Rp)</InputLabel>
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
                                    <Input
                                        type="number"
                                        value={form.data.payment_amount}
                                        onChange={(e) => form.setData('payment_amount', parseInt(e.target.value) || 0)}
                                        required
                                    />
                                </div>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2 mt-4">
                                <div>
                                    <InputLabel required>Metode Pembayaran</InputLabel>
                                    <Select
                                        value={form.data.payment_method}
                                        onChange={(e) => form.setData('payment_method', e.target.value)}
                                    >
                                        <option value="cash">Tunai (Cash)</option>
                                        <option value="qris">QRIS</option>
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
                            </div>
                        </div>

                        <div>
                            <InputLabel>Catatan (Opsional)</InputLabel>
                            <Textarea
                                rows={2}
                                value={form.data.notes}
                                onChange={(e) => form.setData('notes', e.target.value)}
                                placeholder="Catatan tambahan pendaftaran..."
                            />
                        </div>

                        <Button type="submit" disabled={form.processing} size="lg" className="w-full gap-2">
                            <CreditCard className="h-5 w-5" />
                            {form.processing ? 'Memproses Pendaftaran...' : 'Proses Pendaftaran & Pembayaran'}
                        </Button>
                    </form>
                </Card>

                {/* History Aktivasi Terakhir */}
                <div className="space-y-4">
                    <Card>
                        <h3 className="text-sm font-bold text-slate-900 dark:text-white">
                            Aktivasi Member Terakhir
                        </h3>
                        <p className="text-xs text-slate-500 mb-3">10 transaksi pendaftaran terbaru di shift ini</p>
                        <div className="space-y-3">
                            {registerMemberHistory.length > 0 ? (
                                registerMemberHistory.map((item) => (
                                    <div
                                        key={item.id}
                                        className="rounded-xl border border-slate-100 p-3 text-xs dark:border-slate-800"
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="font-mono font-bold text-slate-800 dark:text-slate-200">
                                                {item.invoice}
                                            </span>
                                            <Badge variant={item.payment_status === 'verified' ? 'success' : 'warning'}>
                                                {item.payment_status}
                                            </Badge>
                                        </div>
                                        <p className="mt-1 font-semibold text-slate-900 dark:text-white">
                                            {item.customer_name}
                                        </p>
                                        <div className="mt-1 flex items-center justify-between text-slate-500">
                                            <span>{item.amount_formatted}</span>
                                            <span>{item.transaction_at}</span>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <p className="text-xs text-slate-400">Belum ada riwayat pendaftaran member hari ini.</p>
                            )}
                        </div>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
