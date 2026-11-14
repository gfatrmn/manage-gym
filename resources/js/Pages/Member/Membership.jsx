import React from 'react';
import { Calendar, CheckCircle2, CreditCard, Receipt } from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import { Badge, Card, EmptyState, StatCard } from '../../Components/UI';

export default function Membership({
    member,
    membershipStatus = 'member',
    remainingDays = null,
    totalDays = 30,
    progressPercent = 0,
    joinedAtFormatted = '-',
    expiresAtFormatted = '-',
    paymentHistory = [],
}) {
    return (
        <MemberLayout title="Info Membership" active="membership">
            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard
                    title="Paket / Status"
                    value={membershipStatus}
                    subtitle={`Status: ${member?.status || 'active'}`}
                    icon={CreditCard}
                    accent="rose"
                />
                <StatCard
                    title="Tanggal Mulai"
                    value={joinedAtFormatted}
                    subtitle={`Berakhir: ${expiresAtFormatted}`}
                    icon={Calendar}
                    accent="sky"
                />
                <StatCard
                    title="Sisa Waktu"
                    value={remainingDays !== null ? `${remainingDays} Hari` : '-'}
                    subtitle={`Siklus ${totalDays} hari`}
                    icon={CheckCircle2}
                    accent={remainingDays !== null && remainingDays <= 7 ? 'rose' : 'emerald'}
                />
            </div>

            <Card className="mb-6">
                <div className="mb-2 flex items-center justify-between text-xs font-semibold">
                    <span className="text-slate-600">
                        Progres Siklus Keanggotaan
                    </span>
                    <span className="text-slate-900">{progressPercent}% terpakai</span>
                </div>
                <div className="h-3 w-full overflow-hidden rounded-full bg-slate-200">
                    <div
                        style={{ width: `${progressPercent}%` }}
                        className="h-full rounded-full bg-red-600 transition-all"
                    />
                </div>
                <div className="mt-2 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Mulai: {joinedAtFormatted}</span>
                    <span>Berakhir: {expiresAtFormatted}</span>
                </div>
            </Card>

            <Card>
                <h2 className="mb-4 text-base font-bold text-slate-900 dark:text-white">
                    Riwayat Pembayaran Membership
                </h2>
                {paymentHistory.length === 0 ? (
                    <EmptyState
                        icon={Receipt}
                        title="Belum ada riwayat pembayaran"
                        description="Transaksi pendaftaran atau perpanjangan membership Anda di kasir akan tercatat di sini."
                    />
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                    <th className="pb-3 pr-4">Invoice</th>
                                    <th className="pb-3 pr-4">Tanggal</th>
                                    <th className="pb-3 pr-4">Jenis</th>
                                    <th className="pb-3 pr-4">Metode</th>
                                    <th className="pb-3 pr-4">Nominal</th>
                                    <th className="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {paymentHistory.map((trx) => (
                                    <tr key={trx.id}>
                                        <td className="py-3 pr-4 font-mono text-xs font-semibold text-slate-900 dark:text-white">
                                            {trx.invoice}
                                        </td>
                                        <td className="py-3 pr-4 text-slate-600 dark:text-slate-300">
                                            {trx.transaction_at}
                                        </td>
                                        <td className="py-3 pr-4 capitalize">{trx.transaction_type}</td>
                                        <td className="py-3 pr-4">{trx.payment_method}</td>
                                        <td className="py-3 pr-4 font-semibold">{trx.amount_formatted}</td>
                                        <td className="py-3">
                                            <Badge variant={trx.payment_status === 'paid' ? 'success' : 'warning'}>
                                                {trx.payment_status}
                                            </Badge>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </Card>
        </MemberLayout>
    );
}

