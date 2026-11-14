import React, { useState } from 'react';
import { router, useForm, usePage } from '@inertiajs/react';
import { Archive, BellRing, ExternalLink, Megaphone, RotateCcw, Send, Trash2 } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    FieldError,
    Input,
    InputLabel,
    Textarea,
} from '../../Components/UI';

export default function Announcements({
    announcements = [],
    archivedAnnouncements = [],
    expiringMembers = [],
    whatsAppChannelUrl = '',
}) {
    const { flash = {} } = usePage().props;
    const [tab, setTab] = useState('active');

    const form = useForm({
        title: '',
        body: '',
    });

    const submitPublish = (e) => {
        e.preventDefault();
        form.post('/admin/announcements/publish', {
            onSuccess: () => form.reset(),
        });
    };

    const waShare = flash.whatsapp_channel_share;

    return (
        <AppLayout
            title="Pengumuman & Reminder WA"
            subtitle="Publikasikan info ke portal member, saluran WhatsApp, dan kirim pengingat masa aktif"
            panel="admin"
            active="announcements"
        >
            {waShare && (
                <Card className="mb-6 border-emerald-500/40 bg-emerald-500/10">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p className="text-sm font-bold text-emerald-900 dark:text-emerald-200">
                                Bagikan Pengumuman "{waShare.title}" ke WhatsApp
                            </p>
                            <p className="mt-1 text-xs text-emerald-700 dark:text-emerald-300">
                                Pengumuman sudah tersimpan. Klik tombol untuk langsung meneruskan ke WhatsApp.
                            </p>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            {waShare.share_url && (
                                <a
                                    href={waShare.share_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-emerald-500"
                                >
                                    Bagikan via WhatsApp
                                    <ExternalLink className="h-3.5 w-3.5" />
                                </a>
                            )}
                            {waShare.url && (
                                <a
                                    href={waShare.url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center gap-1.5 rounded-xl border border-emerald-600/30 bg-white px-3.5 py-2 text-xs font-bold text-emerald-700 dark:bg-slate-900 dark:text-emerald-300"
                                >
                                    Buka Saluran WA
                                </a>
                            )}
                        </div>
                    </div>
                </Card>
            )}

            <div className="grid gap-6 lg:grid-cols-3">
                {/* Publish form + Expiring reminders */}
                <div className="space-y-6">
                    <Card>
                        <h2 className="mb-4 flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                            <Megaphone className="h-4 w-4 text-rose-500" />
                            Buat Pengumuman Baru
                        </h2>
                        <form onSubmit={submitPublish} className="space-y-4">
                            <div>
                                <InputLabel required>Judul Pengumuman</InputLabel>
                                <Input
                                    value={form.data.title}
                                    onChange={(e) => form.setData('title', e.target.value)}
                                    placeholder="Contoh: Jadwal Libur Nasional"
                                    error={form.errors.title}
                                />
                                <FieldError message={form.errors.title} />
                            </div>
                            <div>
                                <InputLabel required>Isi Pengumuman</InputLabel>
                                <Textarea
                                    rows={4}
                                    value={form.data.body}
                                    onChange={(e) => form.setData('body', e.target.value)}
                                    placeholder="Tuliskan isi pengumuman secara jelas..."
                                    error={form.errors.body}
                                />
                                <FieldError message={form.errors.body} />
                            </div>
                            <Button type="submit" disabled={form.processing} className="w-full">
                                <Send className="h-4 w-4" />
                                Publikasikan Sekarang
                            </Button>
                        </form>

                        {whatsAppChannelUrl && (
                            <div className="mt-4 border-t border-slate-200 pt-3 dark:border-slate-800">
                                <a
                                    href={whatsAppChannelUrl}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-500/20 dark:text-emerald-300"
                                >
                                    Saluran WhatsApp Resmi
                                    <ExternalLink className="h-3.5 w-3.5" />
                                </a>
                            </div>
                        )}
                    </Card>

                    <Card>
                        <h2 className="mb-3 flex items-center gap-2 text-base font-bold text-slate-900">
                            <BellRing className="h-4 w-4 text-red-600" />
                            Pengingat Jatuh Tempo (≤7 Hari)
                        </h2>
                        {expiringMembers.length === 0 ? (
                            <p className="text-xs text-slate-500">Tidak ada member yang akan kedaluwarsa.</p>
                        ) : (
                            <div className="space-y-3">
                                {expiringMembers.map((m) => (
                                    <div
                                        key={m.id}
                                        className="rounded-xl border border-slate-200 p-3 dark:border-slate-800"
                                    >
                                        <div className="flex items-start justify-between gap-2">
                                            <div>
                                                <p className="text-sm font-bold text-slate-900 dark:text-white">
                                                    {m.full_name}
                                                </p>
                                                <p className="text-xs text-slate-500">
                                                    Berakhir: {m.expires_at} ({m.days_left} hari)
                                                </p>
                                                <p className="text-[11px] text-slate-400">
                                                    Reminder: {m.last_reminder}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="mt-2.5 flex flex-wrap gap-2">
                                            <Button
                                                size="xs"
                                                onClick={() =>
                                                    router.post('/admin/announcements/reminders', {
                                                        gym_member_id: m.id,
                                                    })
                                                }
                                            >
                                                Kirim ke Portal
                                            </Button>
                                            {m.whatsapp_url && (
                                                <a
                                                    href={m.whatsapp_url}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500"
                                                >
                                                    WhatsApp
                                                    <ExternalLink className="h-3 w-3" />
                                                </a>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </Card>
                </div>

                {/* Announcements list */}
                <div className="lg:col-span-2">
                    <Card>
                        <div className="mb-4 flex items-center justify-between border-b border-slate-200 pb-3 dark:border-slate-800">
                            <div className="flex gap-2">
                                <button
                                    type="button"
                                    onClick={() => setTab('active')}
                                    className={`rounded-xl px-3.5 py-1.5 text-xs font-bold ${
                                        tab === 'active'
                                            ? 'bg-rose-600 text-white'
                                            : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'
                                    }`}
                                >
                                    Aktif ({announcements.length})
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setTab('archived')}
                                    className={`rounded-xl px-3.5 py-1.5 text-xs font-bold ${
                                        tab === 'archived'
                                            ? 'bg-rose-600 text-white'
                                            : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'
                                    }`}
                                >
                                    Arsip ({archivedAnnouncements.length})
                                </button>
                            </div>
                        </div>

                        {tab === 'active' ? (
                            announcements.length === 0 ? (
                                <EmptyState icon={Megaphone} title="Belum ada pengumuman aktif" />
                            ) : (
                                <div className="space-y-3">
                                    {announcements.map((item) => (
                                        <div
                                            key={item.id}
                                            className="rounded-2xl border border-slate-200 p-4 dark:border-slate-800"
                                        >
                                            <div className="flex flex-wrap items-start justify-between gap-2">
                                                <div>
                                                    <div className="flex items-center gap-2">
                                                        <h3 className="font-bold text-slate-900 dark:text-white">
                                                            {item.title}
                                                        </h3>
                                                        {item.is_targeted && (
                                                            <Badge variant="info">Pesan Khusus Member</Badge>
                                                        )}
                                                    </div>
                                                    <p className="text-[11px] text-slate-400">
                                                        Dipublikasikan: {item.publish_at}
                                                    </p>
                                                </div>
                                                <div className="flex gap-1">
                                                    <Button
                                                        variant="secondary"
                                                        size="xs"
                                                        onClick={() =>
                                                            router.post('/admin/announcements/archive', {
                                                                announcement_id: item.id,
                                                            })
                                                        }
                                                    >
                                                        <Archive className="h-3.5 w-3.5" />
                                                        Arsipkan
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="xs"
                                                        onClick={() =>
                                                            router.post('/admin/announcements/delete', {
                                                                announcement_id: item.id,
                                                            })
                                                        }
                                                    >
                                                        <Trash2 className="h-4 w-4 text-rose-600" />
                                                    </Button>
                                                </div>
                                            </div>
                                            <p className="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">
                                                {item.body}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            )
                        ) : archivedAnnouncements.length === 0 ? (
                            <EmptyState icon={Archive} title="Arsip pengumuman kosong" />
                        ) : (
                            <div className="space-y-3">
                                {archivedAnnouncements.map((item) => (
                                    <div
                                        key={item.id}
                                        className="rounded-2xl border border-slate-200 p-4 opacity-85 dark:border-slate-800"
                                    >
                                        <div className="flex flex-wrap items-start justify-between gap-2">
                                            <div>
                                                <h3 className="font-bold text-slate-900 dark:text-white">
                                                    {item.title}
                                                </h3>
                                                <p className="text-[11px] text-slate-400">
                                                    Diarsipkan: {item.archived_at}
                                                </p>
                                            </div>
                                            <div className="flex gap-1">
                                                <Button
                                                    variant="secondary"
                                                    size="xs"
                                                    onClick={() =>
                                                        router.post('/admin/announcements/restore', {
                                                            announcement_id: item.id,
                                                        })
                                                    }
                                                >
                                                    <RotateCcw className="h-3.5 w-3.5" />
                                                    Pulihkan
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="xs"
                                                    onClick={() =>
                                                        router.post('/admin/announcements/delete', {
                                                            announcement_id: item.id,
                                                        })
                                                    }
                                                >
                                                    <Trash2 className="h-4 w-4 text-rose-600" />
                                                </Button>
                                            </div>
                                        </div>
                                        <p className="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">
                                            {item.body}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        )}
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

