import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Clock, CreditCard, Dumbbell, Sparkles, UserPlus } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, EmptyState } from '../../Components/UI';
import { formatCurrency } from '../../lib/format';

export default function Packages({ packages = [] }) {
    return (
        <AppLayout
            title="Katalog Paket Membership"
            subtitle="Daftar paket membership aktif yang tersedia untuk registrasi dan perpanjangan member"
            active="packages"
            panel="cashier"
        >
            <Head title="Katalog Paket Membership" />

            {packages.length > 0 ? (
                <div className="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    {packages.map((pkg) => (
                        <Card
                            key={pkg.id}
                            className="flex flex-col justify-between border border-slate-200 p-6 transition hover:shadow-md dark:border-slate-800"
                        >
                            <div>
                                <div className="flex items-start justify-between">
                                    <div>
                                        <Badge variant="green">Tersedia</Badge>
                                        <h3 className="mt-2 text-xl font-bold text-slate-900 dark:text-white">
                                            {pkg.name}
                                        </h3>
                                    </div>
                                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400">
                                        <Sparkles className="h-6 w-6" />
                                    </div>
                                </div>

                                <div className="mt-4 flex items-baseline gap-1">
                                    <span className="text-3xl font-extrabold text-slate-900 dark:text-white">
                                        {formatCurrency(pkg.price)}
                                    </span>
                                    <span className="text-xs font-semibold text-slate-500">
                                        / {pkg.duration_days} Hari
                                    </span>
                                </div>

                                {pkg.description && (
                                    <p className="mt-3 text-xs text-slate-600 dark:text-slate-400">
                                        {pkg.description}
                                    </p>
                                )}

                                <div className="mt-5 rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800/60">
                                    <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                        Fasilitas Yang Didapat
                                    </p>
                                    <p className="mt-1 text-xs font-medium text-slate-700 dark:text-slate-300">
                                        {pkg.facilities || 'Akses gym full & locker'}
                                    </p>
                                </div>
                            </div>

                            <div className="mt-6 flex flex-col gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                                <Link
                                    href={`/cashier/transactions/register-member?package_id=${pkg.id}`}
                                    className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm shadow-rose-500/20 transition hover:bg-rose-700"
                                >
                                    <UserPlus className="h-4 w-4" />
                                    Pendaftaran Member Baru
                                </Link>

                                <Link
                                    href={`/cashier/member-payments?package_id=${pkg.id}`}
                                    className="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                >
                                    <CreditCard className="h-4 w-4" />
                                    Perpanjang Member
                                </Link>
                            </div>
                        </Card>
                    ))}
                </div>
            ) : (
                <EmptyState
                    title="Belum ada paket aktif"
                    description="Hubungi Administrator untuk mengaktifkan paket membership gym."
                />
            )}
        </AppLayout>
    );
}
