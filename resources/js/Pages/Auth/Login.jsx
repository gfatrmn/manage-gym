import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ArrowRight, Lock, LogIn, User } from 'lucide-react';
import GuestLayout from '../../Layouts/GuestLayout';
import { Button, FieldError } from '../../Components/UI';

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
            showHeader={false}
            cardSize="max-w-[360px]"
            backgroundImage="/images/gym-bg.jpg"
        >
            <div className="rounded-2xl border border-white/20 bg-black/40 p-5 shadow-2xl backdrop-blur-xl sm:p-6">
                {/* Brand Logo & System Label */}
                <div className="mb-4 text-center">
                    <img
                        src="/images/arena-fitness-logo.jpg"
                        alt="Arena Fitness"
                        className="mx-auto h-20 w-auto object-contain drop-shadow-lg"
                    />
                    <p className="mt-2 text-[10px] font-bold uppercase tracking-wider text-red-500">
                        Management & Membership System
                    </p>
                </div>

                <form onSubmit={submit} className="space-y-3.5">
                    <div>
                        <label
                            htmlFor="login"
                            className="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-white/90"
                        >
                            Username atau Email <span className="text-red-500">*</span>
                        </label>
                        <div className="relative">
                            <input
                                id="login"
                                type="text"
                                value={data.login}
                                onChange={(e) => setData('login', e.target.value)}
                                placeholder="Masukkan username / email"
                                className="w-full rounded-xl border border-white/20 bg-white/10 pl-9 pr-3.5 py-2 text-xs text-white placeholder-white/40 backdrop-blur-xs transition focus:border-red-500 focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-red-500/20 sm:text-sm"
                                autoFocus
                            />
                            <User className="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-white/60" />
                        </div>
                        <FieldError message={errors.login || errors.email} />
                    </div>

                    <div>
                        <div className="flex items-center justify-between">
                            <label
                                htmlFor="password"
                                className="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-white/90"
                            >
                                Password <span className="text-red-500">*</span>
                            </label>
                            <Link
                                href="/forgot-password"
                                className="mb-1 text-[11px] font-semibold text-red-400 hover:text-red-300 hover:underline"
                            >
                                Lupa password?
                            </Link>
                        </div>
                        <div className="relative">
                            <input
                                id="password"
                                type="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                placeholder="Masukkan password"
                                className="w-full rounded-xl border border-white/20 bg-white/10 pl-9 pr-3.5 py-2 text-xs text-white placeholder-white/40 backdrop-blur-xs transition focus:border-red-500 focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-red-500/20 sm:text-sm"
                            />
                            <Lock className="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-white/60" />
                        </div>
                        <FieldError message={errors.password} />
                    </div>

                    <Button
                        type="submit"
                        disabled={processing}
                        className="mt-2 w-full shadow-lg shadow-red-600/30"
                        size="md"
                    >
                        <LogIn className="h-4 w-4" />
                        {processing ? 'Memverifikasi...' : 'Masuk ke Sistem'}
                        <ArrowRight className="h-4 w-4" />
                    </Button>
                </form>
            </div>
        </GuestLayout>
    );
}
