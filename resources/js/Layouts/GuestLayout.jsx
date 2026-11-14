import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Dumbbell } from 'lucide-react';
import { FlashBanner } from '../Components/UI';

export default function GuestLayout({ title, subtitle, children, wide = false }) {
    return (
        <div className="relative min-h-screen overflow-hidden bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
            <Head title={title} />

            {/* Decorative ambient gradient */}
            <div className="pointer-events-none fixed inset-0 overflow-hidden">
                <div className="absolute -left-24 -top-24 h-96 w-96 rounded-full bg-red-500/10 blur-3xl" />
                <div className="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-red-500/5 blur-3xl" />
            </div>

            <div className="relative z-10 flex min-h-screen flex-col justify-between px-4 py-6 sm:px-6 lg:px-8">
                <header className="mx-auto flex w-full max-w-6xl items-center justify-between">
                    <Link href="/login" className="flex items-center gap-3">
                        <div className="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl bg-red-600 text-white shadow-md shadow-red-600/25">
                            <Dumbbell className="h-5 w-5" />
                        </div>
                        <div>
                            <p className="text-sm font-bold tracking-tight text-slate-900">
                                ARENA GYM & FITNESS
                            </p>
                            <p className="text-[11px] font-medium text-slate-500">
                                Management & Membership System
                            </p>
                        </div>
                    </Link>

                    <div className="flex items-center gap-2">
                    </div>
                </header>

                <main className={`mx-auto my-8 w-full ${wide ? 'max-w-4xl' : 'max-w-md'}`}>
                    {subtitle && (
                        <div className="mb-6 text-center">
                            <h1 className="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                                {title}
                            </h1>
                            <p className="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                                {subtitle}
                            </p>
                        </div>
                    )}
                    <FlashBanner />
                    {children}
                </main>

                <footer className="mx-auto w-full max-w-6xl text-center text-xs text-slate-500 dark:text-slate-400">
                    © {new Date().getFullYear()} Arena Gym & Fitness Center · Built with Laravel, Inertia & React
                </footer>
            </div>
        </div>
    );
}

