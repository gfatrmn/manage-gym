import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { DollarSign, Filter, Ticket, UserCheck } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    FieldError,
    Input,
    InputLabel,
    Modal,
    Pagination,
    Select,
    StatCard,
} from '../../Components/UI';

export default function Checkins({
    todayCheckinsCount = 0,
    todayDailyPassCount = 0,
    todayDailyPassRevenue = 'Rp 0',
    allLogs,
    memberOptions = [],
    paymentMethods = ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'],
    filters = {},
}) {
    const [filterState, setFilterState] = useState({
        date_from: filters.date_from || '',
        date_to: filters.date_to || '',
        type: filters.type || '',
    });
    const [showCheckinModal, setShowCheckinModal] = useState(false);
    const [showDailyPassModal, setShowDailyPassModal] = useState(false);

    const checkinForm = useForm({
        gym_member_id: memberOptions[0]?.id || '',
        notes: '',
    });

    const dailyPassForm = useForm({
        name: '',
        phone: '',
        price: 30000,
        payment_method: paymentMethods[0] || 'Cash',
    });

    const applyFilter = (e) => {
        e.preventDefault();
        router.get('/admin/checkins', filterState, { preserveState: true });
    };

    const rows = allLogs?.data || [];

    return (
        <AppLayout
            title="Absensi & Check-in"
            subtitle="Pantau kunjungan member aktif serta pengunjung daily pass harian"
            panel="admin"
            active="checkins"
        >
            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard title="Check-in Member Hari Ini" value={todayCheckinsCount} icon={UserCheck} accent="rose" />
                <StatCard title="Daily Pass Hari Ini" value={todayDailyPassCount} icon={Ticket} accent="sky" />
                <StatCard title="Pendapatan Daily Pass" value={todayDailyPassRevenue} icon={DollarSign} accent="emerald" />
            </div>

            <Card>
                <div className="mb-5 flex flex-col justify-between gap-3 lg:flex-row lg:items-end">
                    <form onSubmit={applyFilter} className="flex flex-wrap items-end gap-2">
                        <div>
                            <InputLabel>Dari Tanggal</InputLabel>
                            <Input
                                type="date"
                                value={filterState.date_from}
                                onChange={(e) => setFilterState({ ...filterState, date_from: e.target.value })}
                            />
                        </div>
                        <div>
                            <InputLabel>Sampai Tanggal</InputLabel>
                            <Input
                                type="date"
                                value={filterState.date_to}
                                onChange={(e) => setFilterState({ ...filterState, date_to: e.target.value })}
                            />
                        </div>
                        <div>
                            <InputLabel>Tipe</InputLabel>
                            <Select
                                value={filterState.type}
                                onChange={(e) => setFilterState({ ...filterState, type: e.target.value })}
                            >
                                <option value="">Semua Tipe</option>
                                <option value="member">Member Check-in</option>
                                <option value="daily_pass">Daily Pass</option>
                            </Select>
                        </div>
                        <Button type="submit" variant="secondary">
                            <Filter className="h-4 w-4" />
                            Filter
                        </Button>
                    </form>

                    <div className="flex flex-wrap gap-2">
                        <Button size="sm" onClick={() => setShowCheckinModal(true)}>
                            + Check-in Member
                        </Button>
                        <Button size="sm" variant="secondary" onClick={() => setShowDailyPassModal(true)}>
                            + Daily Pass
                        </Button>
                    </div>
                </div>

                {rows.length === 0 ? (
                    <EmptyState icon={UserCheck} title="Belum ada catatan kunjungan" />
                ) : (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                        <th className="pb-3 pr-4">Pengunjung</th>
                                        <th className="pb-3 pr-4">Tipe</th>
                                        <th className="pb-3 pr-4">Keterangan</th>
                                        <th className="pb-3 pr-4">Pembayaran</th>
                                        <th className="pb-3">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                    {rows.map((item) => (
                                        <tr key={item.id}>
                                            <td className="py-3 pr-4">
                                                <p className="font-semibold text-slate-900 dark:text-white">{item.nama}</p>
                                                <p className="text-xs text-slate-500">{item.sub || '-'}</p>
                                            </td>
                                            <td className="py-3 pr-4">
                                                <Badge variant={item.type === 'member' ? 'success' : 'info'}>
                                                    {item.type === 'member' ? 'Member' : 'Daily Pass'}
                                                </Badge>
                                            </td>
                                            <td className="py-3 pr-4 text-xs text-slate-600 dark:text-slate-300">
                                                {item.info}
                                            </td>
                                            <td className="py-3 pr-4 text-xs font-semibold">
                                                {item.amount_formatted
                                                    ? `${item.amount_formatted} (${item.payment_method})`
                                                    : '-'}
                                            </td>
                                            <td className="py-3 text-xs text-slate-500 dark:text-slate-400">
                                                {item.waktu}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={allLogs} />
                    </>
                )}
            </Card>

            <Modal open={showCheckinModal} onClose={() => setShowCheckinModal(false)} title="Check-in Member">
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        checkinForm.post('/admin/checkins', {
                            onSuccess: () => {
                                checkinForm.reset('notes');
                                setShowCheckinModal(false);
                            },
                        });
                    }}
                    className="space-y-4"
                >
                    <div>
                        <InputLabel required>Member Aktif</InputLabel>
                        <Select
                            value={checkinForm.data.gym_member_id}
                            onChange={(e) => checkinForm.setData('gym_member_id', e.target.value)}
                        >
                            <option value="">-- Pilih Member --</option>
                            {memberOptions.map((m) => (
                                <option key={m.id} value={m.id}>
                                    {m.full_name} ({m.checkin_code})
                                </option>
                            ))}
                        </Select>
                        <FieldError message={checkinForm.errors.gym_member_id} />
                    </div>
                    <div>
                        <InputLabel>Catatan</InputLabel>
                        <Input
                            value={checkinForm.data.notes}
                            onChange={(e) => checkinForm.setData('notes', e.target.value)}
                        />
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowCheckinModal(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={checkinForm.processing}>
                            Simpan
                        </Button>
                    </div>
                </form>
            </Modal>

            <Modal open={showDailyPassModal} onClose={() => setShowDailyPassModal(false)} title="Catat Daily Pass">
                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        dailyPassForm.post('/admin/checkins/daily-pass', {
                            onSuccess: () => {
                                dailyPassForm.reset('name', 'phone');
                                setShowDailyPassModal(false);
                            },
                        });
                    }}
                    className="space-y-4"
                >
                    <div>
                        <InputLabel required>Nama Tamu</InputLabel>
                        <Input
                            value={dailyPassForm.data.name}
                            onChange={(e) => dailyPassForm.setData('name', e.target.value)}
                        />
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel>No. HP</InputLabel>
                            <Input
                                value={dailyPassForm.data.phone}
                                onChange={(e) => dailyPassForm.setData('phone', e.target.value)}
                            />
                        </div>
                        <div>
                            <InputLabel required>Tarif (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={dailyPassForm.data.price}
                                onChange={(e) => dailyPassForm.setData('price', e.target.value)}
                            />
                        </div>
                    </div>
                    <div>
                        <InputLabel required>Metode Bayar</InputLabel>
                        <Select
                            value={dailyPassForm.data.payment_method}
                            onChange={(e) => dailyPassForm.setData('payment_method', e.target.value)}
                        >
                            {paymentMethods.map((pm) => (
                                <option key={pm} value={pm}>
                                    {pm}
                                </option>
                            ))}
                        </Select>
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowDailyPassModal(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={dailyPassForm.processing}>
                            Simpan
                        </Button>
                    </div>
                </form>
            </Modal>
        </AppLayout>
    );
}

