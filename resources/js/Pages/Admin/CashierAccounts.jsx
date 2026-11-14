import React from 'react';
import { router, useForm } from '@inertiajs/react';
import { Trash2, UserCog, UserPlus } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, EmptyState, FieldError, Input, InputLabel } from '../../Components/UI';

export default function CashierAccounts({ cashiers = [] }) {
    const form = useForm({
        name: '',
        login: '',
        password: '',
    });

    const submit = (e) => {
        e.preventDefault();
        form.post('/admin/cashier-accounts', {
            onSuccess: () => form.reset(),
        });
    };

    return (
        <AppLayout
            title="Manajemen Akun Kasir"
            subtitle="Tambah atau hapus akun kasir untuk operasional POS & meja front-office"
            panel="admin"
            active="cashier-accounts"
        >
            <div className="grid gap-6 lg:grid-cols-3">
                <Card>
                    <h2 className="mb-4 flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                        <UserPlus className="h-4 w-4 text-rose-500" />
                        Tambah Akun Kasir Baru
                    </h2>
                    <form onSubmit={submit} className="space-y-4">
                        <div>
                            <InputLabel required>Nama Kasir</InputLabel>
                            <Input
                                value={form.data.name}
                                onChange={(e) => form.setData('name', e.target.value)}
                                placeholder="Contoh: Kasir Malam"
                                error={form.errors.name}
                            />
                            <FieldError message={form.errors.name} />
                        </div>
                        <div>
                            <InputLabel required>Username Login</InputLabel>
                            <Input
                                value={form.data.login}
                                onChange={(e) => form.setData('login', e.target.value)}
                                placeholder="Contoh: kasir2"
                                error={form.errors.login}
                            />
                            <FieldError message={form.errors.login} />
                        </div>
                        <div>
                            <InputLabel required>Password Awal</InputLabel>
                            <Input
                                type="password"
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                placeholder="Minimal 6 karakter"
                                error={form.errors.password}
                            />
                            <FieldError message={form.errors.password} />
                        </div>
                        <Button type="submit" disabled={form.processing} className="w-full">
                            Buat Akun Kasir
                        </Button>
                    </form>
                </Card>

                <Card className="lg:col-span-2">
                    <h2 className="mb-4 text-base font-bold text-slate-900 dark:text-white">
                        Daftar Akun Kasir ({cashiers.length})
                    </h2>
                    {cashiers.length === 0 ? (
                        <EmptyState icon={UserCog} title="Belum ada akun kasir" />
                    ) : (
                        <div className="divide-y divide-slate-100 dark:divide-slate-800">
                            {cashiers.map((c) => (
                                <div key={c.id} className="flex items-center justify-between gap-4 py-3.5">
                                    <div>
                                        <p className="font-bold text-slate-900 dark:text-white">{c.name}</p>
                                        <p className="text-xs text-slate-500">
                                            Username: <span className="font-mono font-semibold">{c.login}</span> · Dibuat{' '}
                                            {c.created_at}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Badge variant="info">Cashier</Badge>
                                        <Button
                                            variant="ghost"
                                            size="xs"
                                            onClick={() => {
                                                if (confirm(`Hapus akun kasir "${c.name}"?`)) {
                                                    router.delete(`/admin/cashier-accounts/${c.id}`);
                                                }
                                            }}
                                        >
                                            <Trash2 className="h-4 w-4 text-rose-600" />
                                        </Button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>
            </div>
        </AppLayout>
    );
}

