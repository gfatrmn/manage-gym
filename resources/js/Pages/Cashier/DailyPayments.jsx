import React from 'react';
import { useForm } from '@inertiajs/react';
import { Ticket } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, EmptyState, FieldError, Input, InputLabel, Select, Textarea } from '../../Components/UI';
import { formatRupiah } from '../../lib/format';

export default function DailyPayments({ dailyPayments = [] }) {
    const form = useForm({
        customer_name: '',
        amount: 30000,
        paid_amount: 50000,
        payment_method: 'cash',
        notes: '',
    });

    const submit = (e) => {
        e.preventDefault();
        form.post('/cashier/daily-payments', {
            onSuccess: () => form.reset('customer_name', 'notes'),
        });
    };

    const change =
        form.data.payment_method === 'cash'
            ? Math.max(0, Number(form.data.paid_amount || 0) - Number(form.data.amount || 0))
            : 0;

    return (
        <AppLayout
            title="Pembayaran Daily Pass"
            subtitle="Catat transaksi pengunjung harian (non-member)"
            panel="cashier"
            active="daily-payments"
        >
            <div className="grid gap-6 lg:grid-cols-3">
                <Card>
                    <h2 className="mb-4 flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                        <Ticket className="h-4 w-4 text-rose-500" />
                        Input Daily Pass
                    </h2>
                    <form onSubmit={submit} className="space-y-4">
                        <div>
                            <InputLabel required>Nama Pengunjung</InputLabel>
                            <Input
                                value={form.data.customer_name}
                                onChange={(e) => form.setData('customer_name', e.target.value)}
                                placeholder="Nama lengkap tamu"
                                error={form.errors.customer_name}
                            />
                            <FieldError message={form.errors.customer_name} />
                        </div>
                        <div className="grid gap-3 sm:grid-cols-2">
                            <div>
                                <InputLabel required>Tarif (Rp)</InputLabel>
                                <Input
                                    type="number"
                                    value={form.data.amount}
                                    onChange={(e) => form.setData('amount', e.target.value)}
                                />
                            </div>
                            <div>
                                <InputLabel required>Metode</InputLabel>
                                <Select
                                    value={form.data.payment_method}
                                    onChange={(e) => form.setData('payment_method', e.target.value)}
                                >
                                    <option value="cash">Cash</option>
                                    <option value="qris">QRIS</option>
                                </Select>
                            </div>
                        </div>
                        {form.data.payment_method === 'cash' && (
                            <div>
                                <InputLabel required>Uang Diterima (Rp)</InputLabel>
                                <Input
                                    type="number"
                                    value={form.data.paid_amount}
                                    onChange={(e) => form.setData('paid_amount', e.target.value)}
                                    error={form.errors.paid_amount}
                                />
                                <FieldError message={form.errors.paid_amount} />
                                <p className="mt-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    Kembalian: {formatRupiah(change)}
                                </p>
                            </div>
                        )}
                        <div>
                            <InputLabel>Catatan</InputLabel>
                            <Textarea
                                rows={2}
                                value={form.data.notes}
                                onChange={(e) => form.setData('notes', e.target.value)}
                            />
                        </div>
                        <Button type="submit" disabled={form.processing} className="w-full">
                            Proses Daily Pass
                        </Button>
                    </form>
                </Card>

                <Card className="lg:col-span-2">
                    <h2 className="mb-4 text-base font-bold text-slate-900 dark:text-white">
                        Transaksi Daily Pass (24 Jam Terakhir)
                    </h2>
                    {dailyPayments.length === 0 ? (
                        <EmptyState icon={Ticket} title="Belum ada transaksi daily pass hari ini" />
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead>
                                    <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                        <th className="pb-3 pr-4">Invoice</th>
                                        <th className="pb-3 pr-4">Nama</th>
                                        <th className="pb-3 pr-4">Nominal</th>
                                        <th className="pb-3 pr-4">Metode</th>
                                        <th className="pb-3 pr-4">Status</th>
                                        <th className="pb-3">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                    {dailyPayments.map((trx) => (
                                        <tr key={trx.id}>
                                            <td className="py-3 pr-4 font-mono text-xs font-bold">{trx.invoice}</td>
                                            <td className="py-3 pr-4 font-semibold">{trx.customer_name}</td>
                                            <td className="py-3 pr-4 font-semibold">{trx.amount_formatted}</td>
                                            <td className="py-3 pr-4">{trx.payment_method}</td>
                                            <td className="py-3 pr-4">
                                                <Badge variant={trx.payment_status === 'verified' ? 'success' : 'warning'}>
                                                    {trx.payment_status}
                                                </Badge>
                                            </td>
                                            <td className="py-3 text-xs text-slate-500">{trx.transaction_at}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </Card>
            </div>
        </AppLayout>
    );
}

