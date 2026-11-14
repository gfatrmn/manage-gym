import React from 'react';
import { QRCodeSVG } from 'qrcode.react';
import { ShieldCheck, Sparkles } from 'lucide-react';
import MemberLayout from '../../Layouts/MemberLayout';
import { Badge, Card } from '../../Components/UI';
import { formatDate } from '../../lib/format';

export default function Barcode({ user, member, checkinCode, membershipStatus, sessionId }) {
    const qrPayload = checkinCode || `AG-${member?.id || user?.id || 'MEMBER'}`;

    return (
        <MemberLayout title="QR Check-in Member" active="barcode">
            <div className="mx-auto max-w-md">
                <Card className="text-center">
                    <div className="mb-3 flex items-center justify-center gap-2">
                        <Badge variant="success">
                            <ShieldCheck className="h-3.5 w-3.5" />
                            Kartu Digital Aktif
                        </Badge>
                        <Badge variant="neutral">{membershipStatus}</Badge>
                    </div>

                    <h1 className="text-xl font-extrabold text-slate-900 dark:text-white">
                        {member?.name || user?.name}
                    </h1>
                    <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Tunjukkan QR Code ini kepada kasir saat memasuki area gym
                    </p>

                    <div className="my-6 inline-flex flex-col items-center rounded-3xl border-2 border-slate-200 bg-white p-6 shadow-inner dark:border-slate-700">
                        <QRCodeSVG value={qrPayload} size={210} level="M" includeMargin />
                        <p className="mt-3 font-mono text-base font-extrabold tracking-widest text-slate-900">
                            {qrPayload}
                        </p>
                    </div>

                    <div className="grid grid-cols-2 gap-3 rounded-2xl bg-slate-100 p-3 text-left text-xs dark:bg-slate-800/80">
                        <div>
                            <p className="text-slate-500 dark:text-slate-400">Masa Aktif Hingga</p>
                            <p className="mt-0.5 font-bold text-slate-900 dark:text-white">
                                {member?.expires_at ? formatDate(member.expires_at) : '-'}
                            </p>
                        </div>
                        <div>
                            <p className="text-slate-500 dark:text-slate-400">Sesi Tampil</p>
                            <p className="mt-0.5 font-mono font-bold text-slate-900 dark:text-white">
                                {sessionId}
                            </p>
                        </div>
                    </div>

                    <p className="mt-4 inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <Sparkles className="h-3.5 w-3.5 text-amber-500" />
                        Naikkan kecerahan layar agar pemindaian kasir lebih cepat
                    </p>
                </Card>
            </div>
        </MemberLayout>
    );
}

