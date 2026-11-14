import React from 'react';
import { CalendarDays, CheckCircle2, Flame, History as HistoryIcon } from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import { Badge, Card, EmptyState, Pagination, StatCard } from '../../Components/UI';

export default function History({
    checkins,
    totalCheckins = 0,
    thisMonthCheckins = 0,
    thisWeekCheckins = 0,
    trainingChart = [],
    trainingConsistency = 0,
    trainingAssessment = '-',
    activeTrainingDays = 0,
    trackedTrainingDays = 0,
}) {
    const rows = checkins?.data || [];

    return (
        <MemberLayout title="Riwayat Check-in" active="history">
            <div className="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard title="Total Sesi" value={totalCheckins} icon={HistoryIcon} accent="rose" />
                <StatCard title="Bulan Ini" value={thisMonthCheckins} icon={CalendarDays} accent="emerald" />
                <StatCard title="Minggu Ini" value={thisWeekCheckins} icon={CheckCircle2} accent="sky" />
                <StatCard
                    title="Konsistensi"
                    value={`${trainingConsistency}%`}
                    subtitle={`${trainingAssessment} (${activeTrainingDays}/${trackedTrainingDays} hari)`}
                    icon={Flame}
                    accent="rose"
                />
            </div>

            {/* 30-day activity strip */}
            {trainingChart.length > 0 && (
                <Card className="mb-6">
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-sm font-bold text-slate-900 dark:text-white">
                            Peta Kehadiran Harian ({trackedTrainingDays} Hari Terakhir)
                        </h2>
                        <Badge variant={trainingConsistency >= 50 ? 'success' : 'warning'}>
                            {trainingAssessment}
                        </Badge>
                    </div>
                    <div className="grid grid-cols-10 gap-1.5 sm:grid-cols-15">
                        {trainingChart.map((day, idx) => (
                            <div
                                key={idx}
                                title={`${day.label}: ${day.value} check-in`}
                                className={`flex h-9 flex-col items-center justify-center rounded-lg text-[10px] font-bold ${
                                    day.value > 0
                                        ? 'bg-emerald-500 text-white'
                                        : 'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500'
                                }`}
                            >
                                <span>{day.label.split(' ')[0]}</span>
                            </div>
                        ))}
                    </div>
                </Card>
            )}

            <Card>
                <h2 className="mb-4 text-base font-bold text-slate-900 dark:text-white">
                    Daftar Kehadiran Terverifikasi
                </h2>

                {rows.length === 0 ? (
                    <EmptyState
                        icon={HistoryIcon}
                        title="Belum ada riwayat check-in"
                        description="Lakukan check-in melalui QR di kasir untuk mulai mencatat riwayat latihan Anda."
                    />
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                        <th className="pb-3 pr-4">Waktu Check-in</th>
                                        <th className="pb-3 pr-4">Metode</th>
                                        <th className="pb-3">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                    {rows.map((row) => (
                                        <tr key={row.id}>
                                            <td className="py-3 pr-4 font-medium text-slate-900 dark:text-white">
                                                {row.checked_in_at}
                                            </td>
                                            <td className="py-3 pr-4">
                                                <Badge variant="info">{row.checkin_method}</Badge>
                                            </td>
                                            <td className="py-3 text-slate-500 dark:text-slate-400">
                                                {row.notes || '-'}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={checkins} />
                    </>
                )}
            </Card>
        </MemberLayout>
    );
}

