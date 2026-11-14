import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Building2, Check, Clock, Dumbbell, FileText, Globe, Mail, MapPin, Phone, Save, Settings as SettingsIcon } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Button, Card, FieldError, Input, InputLabel, Select, Textarea } from '../../Components/UI';

export default function Settings({ settings = {} }) {
    const form = useForm({
        gym_name: settings.gym_name || 'Arena Gym & Fitness',
        gym_phone: settings.gym_phone || '0812-3456-7890',
        gym_address: settings.gym_address || '',
        gym_email: settings.gym_email || '',
        invoice_prefix: settings.invoice_prefix || 'INV',
        timezone: settings.timezone || 'Asia/Jakarta',
        invoice_footer: settings.invoice_footer || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post('/admin/settings');
    };

    return (
        <AppLayout
            title="Profil & Pengaturan Gym"
            subtitle="Konfigurasi identitas gym, format struk/invoice, dan zona waktu operasional"
            active="settings"
            panel="admin"
        >
            <Head title="Pengaturan Gym" />

            <div className="mx-auto max-w-4xl">
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Profil Gym */}
                    <Card className="p-6">
                        <div className="flex items-center gap-3 border-b border-slate-100 pb-4 dark:border-slate-800">
                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400">
                                <Building2 className="h-5 w-5" />
                            </div>
                            <div>
                                <h3 className="text-base font-bold text-slate-900 dark:text-white">
                                    Profil & Identitas Usaha Gym
                                </h3>
                                <p className="text-xs text-slate-500">
                                    Informasi ini dicetak pada kop invoice, struk, dan portal aplikasi member
                                </p>
                            </div>
                        </div>

                        <div className="mt-5 space-y-4">
                            <div>
                                <InputLabel htmlFor="gym_name" required>
                                    Nama Gym / Fitness Center
                                </InputLabel>
                                <Input
                                    id="gym_name"
                                    value={form.data.gym_name}
                                    onChange={(e) => form.setData('gym_name', e.target.value)}
                                    placeholder="Arena Gym & Fitness"
                                    error={form.errors.gym_name}
                                    required
                                />
                                <FieldError message={form.errors.gym_name} />
                            </div>

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel htmlFor="gym_phone">
                                        Nomor Telepon / WhatsApp CS
                                    </InputLabel>
                                    <Input
                                        id="gym_phone"
                                        value={form.data.gym_phone}
                                        onChange={(e) => form.setData('gym_phone', e.target.value)}
                                        placeholder="0812-3456-7890"
                                        error={form.errors.gym_phone}
                                    />
                                    <FieldError message={form.errors.gym_phone} />
                                </div>

                                <div>
                                    <InputLabel htmlFor="gym_email">
                                        Email Resmi Gym
                                    </InputLabel>
                                    <Input
                                        id="gym_email"
                                        type="email"
                                        value={form.data.gym_email}
                                        onChange={(e) => form.setData('gym_email', e.target.value)}
                                        placeholder="contact@arenagym.local"
                                        error={form.errors.gym_email}
                                    />
                                    <FieldError message={form.errors.gym_email} />
                                </div>
                            </div>

                            <div>
                                <InputLabel htmlFor="gym_address">
                                    Alamat Lengkap Lokasi Gym
                                </InputLabel>
                                <Textarea
                                    id="gym_address"
                                    rows={2}
                                    value={form.data.gym_address}
                                    onChange={(e) => form.setData('gym_address', e.target.value)}
                                    placeholder="Jl. Arena Olahraga No. 10, Jakarta Selatan"
                                    error={form.errors.gym_address}
                                />
                                <FieldError message={form.errors.gym_address} />
                            </div>
                        </div>
                    </Card>

                    {/* Konfigurasi Invoice & Sistem */}
                    <Card className="p-6">
                        <div className="flex items-center gap-3 border-b border-slate-100 pb-4 dark:border-slate-800">
                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500/10 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400">
                                <FileText className="h-5 w-5" />
                            </div>
                            <div>
                                <h3 className="text-base font-bold text-slate-900 dark:text-white">
                                    Pengaturan Invoice & Struk Kasir
                                </h3>
                                <p className="text-xs text-slate-500">
                                    Prefix penomoran invoice, pesan catatan di footer struk, dan zona waktu
                                </p>
                            </div>
                        </div>

                        <div className="mt-5 space-y-4">
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel htmlFor="invoice_prefix" required>
                                        Prefix Invoice
                                    </InputLabel>
                                    <Input
                                        id="invoice_prefix"
                                        value={form.data.invoice_prefix}
                                        onChange={(e) => form.setData('invoice_prefix', e.target.value)}
                                        placeholder="INV"
                                        error={form.errors.invoice_prefix}
                                        required
                                    />
                                    <p className="mt-1 text-[11px] text-slate-400">Format: INV-YYYYMMDD-XXXX</p>
                                    <FieldError message={form.errors.invoice_prefix} />
                                </div>

                                <div>
                                    <InputLabel htmlFor="timezone" required>
                                        Zona Waktu Sistem
                                    </InputLabel>
                                    <Select
                                        id="timezone"
                                        value={form.data.timezone}
                                        onChange={(e) => form.setData('timezone', e.target.value)}
                                        error={form.errors.timezone}
                                        required
                                    >
                                        <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                        <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                        <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                    </Select>
                                    <FieldError message={form.errors.timezone} />
                                </div>
                            </div>

                            <div>
                                <InputLabel htmlFor="invoice_footer">
                                    Pesan Footer di Invoice / Struk
                                </InputLabel>
                                <Textarea
                                    id="invoice_footer"
                                    rows={2}
                                    value={form.data.invoice_footer}
                                    onChange={(e) => form.setData('invoice_footer', e.target.value)}
                                    placeholder="Terima kasih telah berlatih di Arena Gym. Harap simpan struk ini sebagai bukti pembayaran."
                                    error={form.errors.invoice_footer}
                                />
                                <FieldError message={form.errors.invoice_footer} />
                            </div>
                        </div>
                    </Card>

                    <div className="flex justify-end">
                        <Button type="submit" size="lg" disabled={form.processing} className="gap-2">
                            <Save className="h-5 w-5" />
                            {form.processing ? 'Menyimpan...' : 'Simpan Semua Pengaturan'}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
