import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import axios from 'axios';
import { CheckCircle2, QrCode, Search, UserCheck, XCircle } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, FieldError, Input, InputLabel, Select } from '../../Components/UI';
import { formatDateTime } from '../../lib/format';

export default function Checkins({
    cashierCheckinMembers = [],
    cashierTodayCheckins = [],
    cashierPendingCheckins = [],
}) {
    const [scanCode, setScanCode] = useState('');
    const [lookupResult, setLookupResult] = useState(null);
    const [lookupError, setLookupError] = useState('');
    const [lookingUp, setLookingUp] = useState(false);

    const form = useForm({
        gym_member_id: cashierCheckinMembers[0]?.id || '',
        actor: 'cashier',
        notes: '',
    });

    const handleLookup = async (e) => {
        e.preventDefault();
        if (!scanCode.trim()) return;
        setLookingUp(true);
        setLookupError('');
        setLookupResult(null);

        try {
            const res = await axios.get('/cashier/checkins/lookup-member', {
                params: { checkin_code: scanCode.trim() },
            });
            setLookupResult(res.data.member);
        } catch (err) {
            setLookupError(err.response?.data?.message || 'Member tidak ditemukan.');
        } finally {
            setLookingUp(false);
        }
    };

    const confirmScanCheckin = () => {
        if (!lookupResult) return;
        router.post(
            '/cashier/checkins',
            {
                gym_member_id: lookupResult.id,
                actor: 'qr_member',
                notes: `Scan kode ${lookupResult.checkin_code}`,
            },
            {
                onSuccess: () => {
                    setScanCode('');
                    setLookupResult(null);
                },
            }
        );
    };

    const submitManual = (e) => {
        e.preventDefault();
        form.post('/cashier/checkins', {
            onSuccess: () => form.reset('notes'),
        });
    };

    return (
        <AppLayout
            title="Check-in Member & Scan QR"
            subtitle="Validasi kode QR member, verifikasi antrean mandiri, atau catat check-in manual"
            panel="cashier"
            active="checkins"
        >
            <div className="mb-6 grid gap-6 lg:grid-cols-2">
                {/* Lookup by QR Check-in Code */}
                <Card>
                    <h2 className="mb-3 flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                        <QrCode className="h-4 w-4 text-rose-500" />
                        Scan / Cek Kode QR Member
                    </h2>
                    <form onSubmit={handleLookup} className="flex gap-2">
                        <Input
                            value={scanCode}
                            onChange={(e) => setScanCode(e.target.value)}
                            placeholder="Scan atau ketik kode member (cth: AG-XXXXXX)..."
                            autoFocus
                        />
                        <Button type="submit" disabled={lookingUp}>
                            <Search className="h-4 w-4" />
                            Cek
                        </Button>
                    </form>

                    {lookupError && (
                        <p className="mt-3 rounded-xl bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400">
                            {lookupError}
                        </p>
                    )}

                    {lookupResult && (
                        <div className="mt-4 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-emerald-500/40 bg-emerald-500/10 p-4">
                            <div className="flex items-center gap-3">
                                {lookupResult.profile_photo_url ? (
                                    <img
                                        src={lookupResult.profile_photo_url}
                                        alt={lookupResult.full_name}
                                        className="h-12 w-12 rounded-xl object-cover"
                                    />
                                ) : (
                                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white">
                                        {lookupResult.profile_initials}
                                    </div>
                                )}
                                <div>
                                    <p className="font-bold text-slate-900 dark:text-white">
                                        {lookupResult.full_name}
                                    </p>
                                    <p className="text-xs text-slate-600 dark:text-slate-300">
                                        Kode: {lookupResult.checkin_code} · Aktif s/d {lookupResult.expires_at}
                                    </p>
                                </div>
                            </div>
                            <Button variant="success" size="sm" onClick={confirmScanCheckin}>
                                <CheckCircle2 className="h-4 w-4" />
                                Proses Check-in QR
                            </Button>
                        </div>
                    )}
                </Card>

                {/* Manual Cashier Checkin */}
                <Card>
                    <h2 className="mb-3 flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                        <UserCheck className="h-4 w-4 text-sky-500" />
                        Check-in Manual oleh Kasir
                    </h2>
                    <form onSubmit={submitManual} className="space-y-4">
                        <div>
                            <InputLabel required>Pilih Member Aktif</InputLabel>
                            <Select
                                value={form.data.gym_member_id}
                                onChange={(e) => form.setData('gym_member_id', e.target.value)}
                            >
                                <option value="">-- Pilih Member --</option>
                                {cashierCheckinMembers.map((m) => (
                                    <option key={m.id} value={m.id}>
                                        {m.full_name} ({m.checkin_code})
                                    </option>
                                ))}
                            </Select>
                            <FieldError message={form.errors.gym_member_id} />
                        </div>
                        <div>
                            <InputLabel>Catatan (Opsional)</InputLabel>
                            <Input
                                value={form.data.notes}
                                onChange={(e) => form.setData('notes', e.target.value)}
                                placeholder="Catatan check-in..."
                            />
                        </div>
                        <Button type="submit" disabled={form.processing}>
                            Catat Check-in Sekarang
                        </Button>
                    </form>
                </Card>
            </div>

            {/* Pending + Today Checkins */}
            <div className="grid gap-6 lg:grid-cols-2">
                <Card>
                    <h3 className="mb-4 text-base font-bold text-slate-900 dark:text-white">
                        Menunggu Verifikasi Kasir ({cashierPendingCheckins.length})
                    </h3>
                    {cashierPendingCheckins.length === 0 ? (
                        <p className="py-6 text-center text-xs text-slate-500">
                            Tidak ada antrean verifikasi.
                        </p>
                    ) : (
                        <div className="space-y-3">
                            {cashierPendingCheckins.map((item) => (
                                <div
                                    key={item.id}
                                    className="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 p-3 dark:border-slate-800"
                                >
                                    <div>
                                        <p className="font-bold text-slate-900 dark:text-white">
                                            {item.member?.full_name || item.submitted_name}
                                        </p>
                                        <p className="text-xs text-slate-500">
                                            {item.submitted_phone || item.member?.phone || '-'} ·{' '}
                                            {formatDateTime(item.checked_in_at)}
                                        </p>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button
                                            variant="success"
                                            size="xs"
                                            onClick={() => router.post(`/cashier/checkins/${item.id}/verify`)}
                                        >
                                            <CheckCircle2 className="h-3.5 w-3.5" />
                                            Setujui
                                        </Button>
                                        <Button
                                            variant="danger"
                                            size="xs"
                                            onClick={() => router.post(`/cashier/checkins/${item.id}/reject`)}
                                        >
                                            <XCircle className="h-3.5 w-3.5" />
                                            Tolak
                                        </Button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>

                <Card>
                    <h3 className="mb-4 text-base font-bold text-slate-900 dark:text-white">
                        Log Check-in Hari Ini ({cashierTodayCheckins.length})
                    </h3>
                    {cashierTodayCheckins.length === 0 ? (
                        <p className="py-6 text-center text-xs text-slate-500">
                            Belum ada check-in hari ini.
                        </p>
                    ) : (
                        <div className="divide-y divide-slate-100 dark:divide-slate-800">
                            {cashierTodayCheckins.map((item) => (
                                <div key={item.id} className="flex items-center justify-between py-2.5 text-sm">
                                    <div>
                                        <p className="font-semibold text-slate-900 dark:text-white">
                                            {item.member?.full_name || item.submitted_name || '-'}
                                        </p>
                                        <p className="text-xs text-slate-500">
                                            {formatDateTime(item.checked_in_at)} · {item.checkin_method}
                                        </p>
                                    </div>
                                    <Badge
                                        variant={
                                            item.verification_status === 'verified'
                                                ? 'success'
                                                : item.verification_status === 'rejected'
                                                  ? 'danger'
                                                  : 'warning'
                                        }
                                    >
                                        {item.verification_status}
                                    </Badge>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>
            </div>
        </AppLayout>
    );
}

