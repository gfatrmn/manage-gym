import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowRight, LogIn } from 'lucide-react';
import GuestLayout from '../../Layouts/GuestLayout';
import { Button, Card, FieldError, Input, InputLabel } from '../../Components/UI';

export default function Login({ pageTitle }) {
    const { data, setData, post, processing, errors } = useForm({
        login: '',
        password: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <GuestLayout
            title={pageTitle || 'Login Arena Gym'}
            subtitle="Satu portal masuk untuk seluruh akun: Admin, Kasir, dan Member"
        >
            <Card className="backdrop-blur-md">
                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="login" required>
                            Username atau Email
                        </InputLabel>
                        <Input
                            id="login"
                            type="text"
                            value={data.login}
                            onChange={(e) => setData('login', e.target.value)}
                            placeholder="Masukkan username atau email Anda"
                            error={errors.login || errors.email}
                            autoFocus
                        />
                        <FieldError message={errors.login || errors.email} />
                    </div>

                    <div>
                        <div className="flex items-center justify-between">
                            <InputLabel htmlFor="password" required>
                                Password
                            </InputLabel>
                            <Link
                                href="/forgot-password"
                                className="mb-1.5 text-xs font-semibold text-rose-600 hover:underline dark:text-rose-400"
                            >
                                Lupa password?
                            </Link>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="Masukkan password Anda"
                            error={errors.password}
                        />
                        <FieldError message={errors.password} />
                    </div>

                    <Button type="submit" disabled={processing} className="w-full" size="lg">
                        <LogIn className="h-4 w-4" />
                        {processing ? 'Memverifikasi...' : 'Masuk ke Sistem'}
                        <ArrowRight className="h-4 w-4" />
                    </Button>
                </form>
            </Card>
        </GuestLayout>
    );
}
