import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Check, Clock, CreditCard, Dumbbell, Sparkles, Zap } from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import { Badge, Button, Card, EmptyState } from '../../Components/UI';
import { formatCurrency } from '../../lib/format';

export default function Packages({ packages = [], member }) {
    return (
        <MemberLayout title="Katalog Paket Membership" active="packages">
            <Head title="Paket Membership Arena Gym" />

            <div className="mb-6 text-center sm:text-left">
                <h1 className="text-2xl font-black text-slate-900 dark:text-white">
                    Pilihan Paket Membership
                </h1>
                <p className="mt-1 text-sm text-slate-500">
                    Pilih paket yang paling sesuai dengan target kebugaran Anda. Perpanjangan dan aktivasi dapat dilakukan langsung di meja kasir.
                </p>
            </div>

            {packages.length > 0 ? (
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {packages.map((pkg) => {
                        const isCurrentPlan = member?.membership_plan === pkg.name;
                        return (
                            <Card
                                key={pkg.id}
                                className={`relative flex flex-col justify-between border transition hover:shadow-lg ${
                                    isCurrentPlan
                                        ? 'border-rose-500 ring-2 ring-rose-500/20 dark:border-rose-500'
                                        : 'border-slate-200 dark:border-slate-800'
                                }`}
                            >
                                {isCurrentPlan && (
                                    <div className="absolute -top-3 right-4 rounded-full bg-rose-600 px-3 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-white shadow-sm">
                                        Paket Anda Saat Ini
                                    </div>
                                )}

                                <div>
                                    <div className="flex items-center justify-between">
                                        <h3 className="text-lg font-bold text-slate-900 dark:text-white">
                                            {pkg.name}
                                        </h3>
                                        <span className="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400">
                                            <Sparkles className="h-4 w-4" />
                                        </span>
                                    </div>

                                    <div className="mt-4 flex items-baseline gap-1">
                                        <span className="text-3xl font-black text-slate-900 dark:text-white">
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

                                    <div className="mt-5 border-t border-slate-100 pt-4 dark:border-slate-800">
                                        <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                            Fasilitas Termasuk
                                        </p>
                                        <div className="mt-2 space-y-1.5">
                                            {(pkg.facilities || 'Akses gym full, loker, shower')
                                                .split(',')
                                                .map((fac, idx) => (
                                                    <div
                                                        key={idx}
                                                        className="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300"
                                                    >
                                                        <Check className="h-3.5 w-3.5 text-emerald-500 shrink-0" />
                                                        <span>{fac.trim()}</span>
                                                    </div>
                                                ))}
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-6 border-t border-slate-100 pt-4 dark:border-slate-800">
                                    <Link
                                        href="/member/membership"
                                        className="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                    >
                                        <Clock className="h-4 w-4" />
                                        Lihat Status Membership
                                    </Link>
                                </div>
                            </Card>
                        );
                    })}
                </div>
            ) : (
                <EmptyState
                    title="Paket belum tersedia"
                    description="Katalog paket membership sedang diperbarui oleh manajemen gym."
                />
            )}

            {/* Inquire Info Banner */}
            <div className="mt-8 rounded-2xl bg-gradient-to-br from-rose-50 to-orange-50 p-6 border border-rose-200/60 dark:from-rose-950/20 dark:to-orange-950/20 dark:border-rose-900/40">
                <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-600 text-white shadow-md">
                            <Zap className="h-6 w-6" />
                        </div>
                        <div>
                            <h4 className="text-base font-bold text-slate-900 dark:text-white">
                                Mau Perpanjang atau Upgrade Paket?
                            </h4>
                            <p className="text-xs text-slate-600 dark:text-slate-400">
                                Tunjukkan QR Code kartu member Anda di meja kasir Arena Gym untuk melakukan pembayaran perpanjangan paket dengan Cash, QRIS, atau Transfer.
                            </p>
                        </div>
                    </div>

                    <Link
                        href="/member/barcode"
                        className="inline-flex shrink-0 items-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-rose-700"
                    >
                        Buka Kartu QR Saya
                    </Link>
                </div>
            </div>
        </MemberLayout>
    );
}
