import React from 'react';
import { Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft, Copy, Mail } from 'lucide-react';
import GuestLayout from '../../Layouts/GuestLayout';
import { Button, Card, FieldError, Input, InputLabel } from '../../Components/UI';

export default function ForgotPassword() {
    const { flash = {} } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <GuestLayout
            title="Lupa Password"
            subtitle="Masukkan email terdaftar untuk membuat tautan reset password"
        >
            <Card>
                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="email" required>
                            Email Terdaftar
                        </InputLabel>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="nama@email.com"
                            error={errors.email}
                            autoFocus
                        />
                        <FieldError message={errors.email} />
                    </div>

                    <Button type="submit" disabled={processing} className="w-full">
                        <Mail className="h-4 w-4" />
                        {processing ? 'Memproses...' : 'Buat Tautan Reset Password'}
                    </Button>
                </form>

                {flash.reset_link && (
                    <div className="mt-4 rounded-xl border border-sky-500/30 bg-sky-500/10 p-3 text-xs text-sky-900 dark:text-sky-200">
                        <p className="font-bold">Tautan Reset Langsung (Mode Lokal):</p>
                        <a
                            href={flash.reset_link}
                            className="mt-1 inline-flex items-center gap-1 break-all font-mono underline"
                        >
                            {flash.reset_link}
                            <Copy className="h-3.5 w-3.5 shrink-0" />
                        </a>
                    </div>
                )}

                <div className="mt-5 border-t border-slate-200 pt-4 text-center dark:border-slate-800">
                    <Link
                        href="/login"
                        className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-rose-600 dark:text-slate-300"
                    >
                        <ArrowLeft className="h-3.5 w-3.5" />
                        Kembali ke Halaman Login
                    </Link>
                </div>
            </Card>
        </GuestLayout>
    );
}

