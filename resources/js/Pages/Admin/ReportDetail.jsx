import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { ArrowLeft, Download, Filter } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, Input, InputLabel, Select } from '../../Components/UI';

function ReportTable({ title, columns = [], rows = [] }) {
    return (
        <Card className="mb-6">
            <h3 className="mb-4 text-base font-bold text-slate-900 dark:text-white">{title}</h3>
            {rows.length === 0 ? (
                <p className="py-6 text-center text-xs text-slate-500">Tidak ada data pada periode ini.</p>
            ) : (
                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm">
                        <thead>
                            <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                {columns.map((col, idx) => (
                                    <th key={idx} className="pb-3 pr-4">
                                        {col}
                                    </th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                            {rows.map((row, rIdx) => (
                                <tr key={rIdx}>
                                    {row.map((cell, cIdx) => (
                                        <td key={cIdx} className="py-2.5 pr-4 text-xs sm:text-sm">
                                            {cell}
                                        </td>
                                    ))}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </Card>
    );
}

export default function ReportDetail({
    selectedReport = {},
    detailFilterType = 'month',
    detailFilterMonth = '',
    detailFrom = '',
    detailTo = '',
    detailFilterLabel = '',
}) {
    const [filterType, setFilterType] = useState(detailFilterType);
    const [month, setMonth] = useState(detailFilterMonth);
    const [from, setFrom] = useState(detailFrom);
    const [to, setTo] = useState(detailTo);

    const applyFilter = (e) => {
        e.preventDefault();
        router.get(
            `/admin/reports/${selectedReport.slug}`,
            {
                detail_filter: filterType,
                detail_month: month,
                detail_from: from,
                detail_to: to,
            },
            { preserveState: true }
        );
    };

    const exportHref = `/admin/reports/${selectedReport.slug}?export=1&detail_filter=${filterType}&detail_month=${month}&detail_from=${from}&detail_to=${to}`;
    const reportSections = selectedReport.reportSections || {};
    const sectionEntries = Object.entries(reportSections);

    return (
        <AppLayout
            title={selectedReport.title || 'Detail Laporan'}
            subtitle={`${selectedReport.group || 'Laporan'} · Periode ${detailFilterLabel}`}
            panel="admin"
            active="reports"
        >
            <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
                <Link
                    href="/admin/reports"
                    className="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-rose-600 dark:text-slate-300"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Kembali ke Daftar Laporan
                </Link>
                <a
                    href={exportHref}
                    className="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-500"
                >
                    <Download className="h-4 w-4" />
                    Download Excel (.xls)
                </a>
            </div>

            <Card className="mb-6">
                <div className="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <Badge variant="info">{selectedReport.group}</Badge>
                        <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">
                            {selectedReport.summary}
                        </p>
                        <div className="mt-2 flex flex-wrap gap-3 text-xs text-slate-500">
                            <span>Periode: <strong>{detailFilterLabel}</strong></span>
                            <span>Volume: <strong>{selectedReport.count_label}</strong></span>
                            <span>Highlight: <strong className="text-rose-600 dark:text-rose-400">{selectedReport.highlight}</strong></span>
                        </div>
                    </div>

                    <form onSubmit={applyFilter} className="flex flex-wrap items-end gap-2">
                        <div>
                            <InputLabel>Mode Periode</InputLabel>
                            <Select value={filterType} onChange={(e) => setFilterType(e.target.value)}>
                                <option value="month">Per Bulan</option>
                                <option value="range">Rentang Tanggal</option>
                            </Select>
                        </div>
                        {filterType === 'month' ? (
                            <div>
                                <InputLabel>Bulan</InputLabel>
                                <Input type="month" value={month} onChange={(e) => setMonth(e.target.value)} />
                            </div>
                        ) : (
                            <>
                                <div>
                                    <InputLabel>Dari</InputLabel>
                                    <Input type="date" value={from} onChange={(e) => setFrom(e.target.value)} />
                                </div>
                                <div>
                                    <InputLabel>Sampai</InputLabel>
                                    <Input type="date" value={to} onChange={(e) => setTo(e.target.value)} />
                                </div>
                            </>
                        )}
                        <Button type="submit" variant="secondary">
                            <Filter className="h-4 w-4" />
                            Terapkan
                        </Button>
                    </form>
                </div>
            </Card>

            {sectionEntries.length > 0 ? (
                sectionEntries.map(([sectionTitle, sectionData]) => (
                    <ReportTable
                        key={sectionTitle}
                        title={sectionTitle}
                        columns={sectionData.columns}
                        rows={sectionData.rows}
                    />
                ))
            ) : (
                <ReportTable
                    title="Rincian Data"
                    columns={selectedReport.columns}
                    rows={selectedReport.rows}
                />
            )}

            {selectedReport.monthly_stat_rows?.length > 0 && (
                <ReportTable
                    title="Rekap Bulanan Tahun Berjalan"
                    columns={selectedReport.monthly_stat_columns}
                    rows={selectedReport.monthly_stat_rows}
                />
            )}
        </AppLayout>
    );
}

