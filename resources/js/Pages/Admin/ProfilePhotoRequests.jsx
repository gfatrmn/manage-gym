import React from 'react';
import { router } from '@inertiajs/react';
import { Camera, Check, X } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import { Badge, Button, Card, EmptyState, Pagination } from '../../Components/UI';

export default function ProfilePhotoRequests({ requests }) {
    const rows = requests?.data || [];

    return (
        <AppLayout
            title="Verifikasi Foto Profil"
            subtitle="Setujui atau tolak permintaan pergantian foto profil member yang melebihi kuota"
            panel="admin"
            active="profile-photo-requests"
        >
            {rows.length === 0 ? (
                <EmptyState
                    icon={Camera}
                    title="Belum ada permintaan ganti foto"
                    description="Permintaan foto profil yang menunggu moderasi admin akan muncul di sini."
                />
            ) : (
                <>
                    <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        {rows.map((item) => (
                            <Card key={item.id}>
                                <div className="mb-3 flex items-start justify-between gap-2">
                                    <div>
                                        <h3 className="font-bold text-slate-900 dark:text-white">
                                            {item.member_name}
                                        </h3>
                                        <p className="text-xs text-slate-500">{item.member_email}</p>
                                        <p className="text-[11px] text-slate-400">Diajukan: {item.created_at}</p>
                                    </div>
                                    <Badge
                                        variant={
                                            item.status === 'approved'
                                                ? 'success'
                                                : item.status === 'rejected'
                                                  ? 'danger'
                                                  : 'warning'
                                        }
                                    >
                                        {item.status}
                                    </Badge>
                                </div>

                                <div className="grid grid-cols-2 gap-3 rounded-2xl bg-slate-100 p-3 dark:bg-slate-800/70">
                                    <div className="text-center">
                                        <p className="mb-1.5 text-[11px] font-semibold text-slate-500">Foto Lama</p>
                                        {item.member_current_photo ? (
                                            <img
                                                src={item.member_current_photo}
                                                alt="Lama"
                                                className="mx-auto h-24 w-24 rounded-xl object-cover"
                                            />
                                        ) : (
                                            <div className="mx-auto flex h-24 w-24 items-center justify-center rounded-xl bg-slate-200 text-xs text-slate-500 dark:bg-slate-700">
                                                Kosong
                                            </div>
                                        )}
                                    </div>
                                    <div className="text-center">
                                        <p className="mb-1.5 text-[11px] font-semibold text-rose-500">Foto Pengajuan</p>
                                        <img
                                            src={item.requested_photo_url}
                                            alt="Baru"
                                            className="mx-auto h-24 w-24 rounded-xl border-2 border-rose-500/40 object-cover"
                                        />
                                    </div>
                                </div>

                                {item.status === 'pending' ? (
                                    <div className="mt-4 grid grid-cols-2 gap-2">
                                        <Button
                                            variant="success"
                                            size="sm"
                                            onClick={() =>
                                                router.post(`/admin/profile-photo-requests/${item.id}/approve`)
                                            }
                                        >
                                            <Check className="h-4 w-4" />
                                            Setujui
                                        </Button>
                                        <Button
                                            variant="danger"
                                            size="sm"
                                            onClick={() =>
                                                router.post(`/admin/profile-photo-requests/${item.id}/reject`)
                                            }
                                        >
                                            <X className="h-4 w-4" />
                                            Tolak
                                        </Button>
                                    </div>
                                ) : (
                                    <p className="mt-3 text-center text-xs text-slate-400">
                                        Direview {item.reviewed_at || '-'} oleh {item.reviewed_by || 'Admin'}
                                    </p>
                                )}
                            </Card>
                        ))}
                    </div>
                    <Pagination paginator={requests} />
                </>
            )}
        </AppLayout>
    );
}

