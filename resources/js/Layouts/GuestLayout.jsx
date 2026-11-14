import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Dumbbell } from 'lucide-react';
import { FlashBanner } from '../Components/UI';

export default function GuestLayout({
    title,
    subtitle,
    children,
    wide = false,
    cardSize = null,
    showHeader = true,
    backgroundImage = null,
}) {
    return (
        <div className="relative min-h-screen overflow-hidden bg-slate-900 text-slate-900">
            <Head title={title} />

            {/* Background Image / Ambient */}
            {backgroundImage ? (
                <div className="pointer-events-none fixed inset-0 z-0 overflow-hidden">
                    <img
                        src={backgroundImage}
                        alt="Gym Background"
                        className="h-full w-full object-cover object-center filter brightness-[0.88] contrast-[1.05]"
                    />
                    <div className="absolute inset-0 bg-black/25 backdrop-blur-[0.5px]" />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20" />
                </div>
            ) : (
                <div className="pointer-events-none fixed inset-0 overflow-hidden bg-slate-50">
                    <div className="absolute -left-24 -top-24 h-96 w-96 rounded-full bg-red-500/10 blur-3xl" />
                    <div className="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-red-500/5 blur-3xl" />
                </div>
            )}

            <div className="relative z-10 flex min-h-screen flex-col justify-between px-4 py-6 sm:px-6 lg:px-8">
                {showHeader ? (
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
                    </header>
                ) : (
                    <div className="h-2" />
                )}

                <main className={`mx-auto my-auto w-full py-4 ${cardSize || (wide ? 'max-w-4xl' : 'max-w-md')}`}>
                    {subtitle && (
                        <div className="mb-6 text-center">
                            <h1 className="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                                {title}
                            </h1>
                            <p className="mt-1.5 text-sm text-slate-300">
                                {subtitle}
                            </p>
                        </div>
                    )}
                    <FlashBanner />
                    {children}
                </main>

                <footer className="mx-auto w-full max-w-6xl text-center text-xs text-white/90 drop-shadow-sm">
                    © {new Date().getFullYear()} Arena Gym & Fitness Center
                </footer>
            </div>
        </div>
    );
}

