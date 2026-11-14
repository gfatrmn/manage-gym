import React from 'react';
import { router } from '@inertiajs/react';
import { CheckCheck, MessageSquare, Trash2 } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, EmptyState, Pagination } from '../../Components/UI';

export default function Feedbacks({ feedbacks, unreadCount = 0 }) {
    const rows = feedbacks?.data || [];

    return (
        <AppLayout
            title="Kritik & Saran Member"
            subtitle={`Tinjau masukan dari member (${unreadCount} belum dibaca)`}
            panel="admin"
            active="feedbacks"
        >
            {rows.length === 0 ? (
                <EmptyState icon={MessageSquare} title="Belum ada kritik & saran" />
            ) : (
                <>
                    <div className="space-y-4">
                        {rows.map((item) => (
                            <Card
                                key={item.id}
                                className={!item.is_read ? 'border-rose-500/40 ring-1 ring-rose-500/20' : ''}
                            >
                                <div className="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <div className="flex flex-wrap items-center gap-2">
                                            <h3 className="font-bold text-slate-900 dark:text-white">
                                                {item.subject}
                                            </h3>
                                            <Badge variant={item.is_read ? 'neutral' : 'danger'}>
                                                {item.is_read ? 'Sudah Dibaca' : 'Baru'}
                                            </Badge>
                                        </div>
                                        <p className="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                            Dari: <strong>{item.name}</strong> ({item.email}) · {item.created_at}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        {!item.is_read && (
                                            <Button
                                                variant="secondary"
                                                size="xs"
                                                onClick={() =>
                                                    router.post(`/admin/feedbacks/${item.id}/read`)
                                                }
                                            >
                                                <CheckCheck className="h-3.5 w-3.5" />
                                                Tandai Dibaca
                                            </Button>
                                        )}
                                        <Button
                                            variant="ghost"
                                            size="xs"
                                            onClick={() => {
                                                if (confirm('Hapus pesan kritik & saran ini?')) {
                                                    router.delete(`/admin/feedbacks/${item.id}`);
                                                }
                                            }}
                                        >
                                            <Trash2 className="h-4 w-4 text-rose-600" />
                                        </Button>
                                    </div>
                                </div>
                                <p className="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                                    {item.message}
                                </p>
                            </Card>
                        ))}
                    </div>
                    <Pagination paginator={feedbacks} />
                </>
            )}
        </AppLayout>
    );
}

