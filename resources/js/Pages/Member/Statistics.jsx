import React from 'react';
import { Activity, CalendarCheck, Clock, TrendingUp } from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import { Card, StatCard } from '../../Components/UI';

function SimpleBarChart({ title, data = [], barColor = 'bg-red-600' }) {
    const maxVal = Math.max(...data.map((d) => d.value || 0), 1);

    return (
        <Card>
            <div className="mb-5 flex items-center justify-between">
                <h2 className="text-base font-bold text-slate-900">{title}</h2>
                <span className="text-xs text-slate-500">Sesi Terverifikasi</span>
            </div>
            <div className="flex h-48 items-end gap-3">
                {data.map((item, idx) => {
                    const heightPct = Math.max(6, Math.round(((item.value || 0) / maxVal) * 100));
                    return (
                        <div key={idx} className="flex flex-1 flex-col items-center gap-2">
                            <span className="text-xs font-bold text-slate-700">
                                {item.value}
                            </span>
                            <div className="flex h-32 w-full items-end rounded-xl bg-slate-100 p-1">
                                <div
                                    style={{ height: `${item.value > 0 ? heightPct : 4}%` }}
                                    className={`w-full rounded-lg transition-all ${
                                        item.value > 0 ? barColor : 'bg-slate-300'
                                    }`}
                                />
                            </div>
                            <div className="text-center">
                                <p className="text-[11px] font-semibold text-slate-600">
                                    {item.label}
                                </p>
                                {item.date && (
                                    <p className="text-[10px] text-slate-400">{item.date}</p>
                                )}
                            </div>
                        </div>
                    );
                })}
            </div>
        </Card>
    );
}

export default function Statistics({
    totalCheckins = 0,
    thisMonthCheckins = 0,
    lastCheckinFormatted = null,
    weeklyStats = [],
    monthlyStats = [],
}) {
    return (
        <MemberLayout title="Statistik Latihan" active="statistics">
            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard
                    title="Total Kehadiran"
                    value={totalCheckins}
                    subtitle="Sepanjang masa keanggotaan"
                    icon={Activity}
                    accent="rose"
                />
                <StatCard
                    title="Bulan Ini"
                    value={thisMonthCheckins}
                    subtitle="Kehadiran terverifikasi"
                    icon={TrendingUp}
                    accent="emerald"
                />
                <StatCard
                    title="Check-in Terakhir"
                    value={lastCheckinFormatted || 'Belum Ada'}
                    subtitle="Waktu kedatangan terakhir"
                    icon={Clock}
                    accent="sky"
                />
            </div>

            <div className="grid gap-6 lg:grid-cols-2">
                <SimpleBarChart
                    title="Aktivitas 7 Hari Terakhir"
                    data={weeklyStats}
                    barColor="bg-red-600"
                />
                <SimpleBarChart
                    title="Tren 6 Bulan Terakhir"
                    data={monthlyStats}
                    barColor="bg-slate-700"
                />
            </div>

            <Card className="mt-6">
                <div className="flex items-center gap-3">
                    <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600">
                        <CalendarCheck className="h-5 w-5" />
                    </div>
                    <div>
                        <p className="text-sm font-bold text-slate-900">
                            Tips Konsistensi Arena Gym
                        </p>
                        <p className="text-xs text-slate-500">
                            Targetkan minimal 3–4 sesi latihan per minggu untuk hasil kebugaran optimal dan pastikan check-in diverifikasi kasir.
                        </p>
                    </div>
                </div>
            </Card>
        </MemberLayout>
    );
}

