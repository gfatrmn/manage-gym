import React from 'react';
import { Bell, Calendar } from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import { Badge, Card, EmptyState } from '../../Components/UI';

export default function Messages({ announcements = [], membershipStatus = 'member' }) {
    return (
        <MemberLayout title="Pesan & Pengumuman" active="messages">
            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-xl font-bold text-slate-900 dark:text-white">
                        Kotak Masuk Member
                    </h1>
                    <p className="text-xs text-slate-500 dark:text-slate-400">
                        Informasi pengingat perpanjangan & pengumuman khusus untuk Anda
                    </p>
                </div>
                <Badge variant="info">Status: {membershipStatus}</Badge>
            </div>

            {announcements.length === 0 ? (
                <EmptyState
                    icon={Bell}
                    title="Belum ada pesan baru"
                    description="Pengumuman khusus atau pengingat masa aktif dari admin akan muncul di halaman ini."
                />
            ) : (
                <div className="space-y-4">
                    {announcements.map((item) => (
                        <Card key={item.id}>
                            <div className="flex flex-wrap items-start justify-between gap-2">
                                <h2 className="text-base font-bold text-slate-900 dark:text-white">
                                    {item.title}
                                </h2>
                                <span className="inline-flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                                    <Calendar className="h-3.5 w-3.5" />
                                    {item.publish_at || '-'}
                                </span>
                            </div>
                            <p className="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                                {item.body}
                            </p>
                        </Card>
                    ))}
                </div>
            )}
        </MemberLayout>
    );
}

