@extends('admin.layout')

@section('content')
    <style>
        .reminder-page .topbar-card,
        .reminder-page .reminder-table-card {
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
        }

        .reminder-table-wrap {
            overflow-x: auto;
        }

        .reminder-table {
            min-width: 880px;
        }

        .reminder-table thead th {
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--text-muted);
            border-bottom-color: var(--border);
            background: rgba(255,255,255,.03);
            white-space: nowrap;
        }

        .reminder-table td {
            vertical-align: middle;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .reminder-table tbody tr + tr td {
            border-top-color: var(--border);
        }

        .reminder-table tbody tr.reminder-h7 {
            background: rgba(255, 193, 7, .08);
        }

        .reminder-table tbody tr.reminder-h3 {
            background: rgba(220, 53, 69, .1);
        }

        .reminder-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 74px;
            border-radius: 999px;
            padding: .42rem .75rem;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .reminder-badge.h7 {
            background: rgba(255, 193, 7, .18);
            border: 1px solid rgba(255, 193, 7, .45);
            color: #ffd86b;
        }

        .reminder-badge.h3 {
            background: rgba(220, 53, 69, .2);
            border: 1px solid rgba(220, 53, 69, .5);
            color: #ff9aa6;
        }

        .reminder-filter-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            border-radius: 999px;
            padding: .5rem .85rem;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.04);
            color: var(--text-muted);
            font-size: .85rem;
            font-weight: 700;
        }

        .reminder-dot {
            width: .65rem;
            height: .65rem;
            border-radius: 999px;
        }

        .reminder-dot.h7 { background: #ffc107; }
        .reminder-dot.h3 { background: #dc3545; }

        .reminder-action {
            min-width: 112px;
            font-weight: 800;
        }

        .reminder-empty {
            border: 1px dashed var(--border);
            border-radius: 1rem;
            padding: 2rem 1.25rem;
            text-align: center;
            color: var(--text-muted);
            background: rgba(255,255,255,.02);
        }

        @media (max-width: 575.98px) {
            .reminder-page .topbar-card,
            .reminder-page .reminder-table-card {
                padding: 1rem !important;
            }

            .reminder-table {
                min-width: 760px;
            }
        }
    </style>

    @php
        $h7Count = $expiringMembers->filter(fn ($member) => $member->expires_at && now()->startOfDay()->diffInDays($member->expires_at->copy()->startOfDay(), false) >= 4)->count();
        $h3Count = $expiringMembers->filter(fn ($member) => $member->expires_at && now()->startOfDay()->diffInDays($member->expires_at->copy()->startOfDay(), false) <= 3)->count();
    @endphp

    <div class="reminder-page">
        <div class="topbar-card p-4 p-lg-5 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div>
                    <div class="section-label">Reminder Membership</div>
                    <h1 class="h2 fw-bold mt-2 mb-2">Member yang Perlu Diingatkan</h1>
                    <p class="muted-copy mb-0">Tabel ini menampilkan member yang masa aktif membership-nya tersisa 1 sampai 7 hari sebelum expired.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="reminder-filter-pill"><span class="reminder-dot h7"></span>H-4 s/d H-7: {{ $h7Count }}</span>
                    <span class="reminder-filter-pill"><span class="reminder-dot h3"></span>H-1 s/d H-3: {{ $h3Count }}</span>
                    <span class="reminder-filter-pill">Total: {{ $expiringMembers->count() }}</span>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success rounded-4 mb-4">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger rounded-4 mb-4">{{ $errors->first() }}</div>
        @endif

        @if(session('whatsapp_dispatch'))
            @php
                $dispatch = session('whatsapp_dispatch');
                $recipient = $dispatch['recipients'][0] ?? null;
            @endphp
            @if($recipient)
                <div class="panel-card p-4 mb-4">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h3 class="h5 fw-bold mb-2">
                                @if($recipient['delivery_status'] === 'sent')
                                    <i class="fas fa-check-circle text-success"></i> Pesan Berhasil Dikirim
                                @elseif($recipient['delivery_status'] === 'pending')
                                    <i class="fas fa-info-circle text-warning"></i> Siap Dikirim Manual
                                @else
                                    <i class="fas fa-exclamation-circle text-danger"></i> Gagal Dikirim
                                @endif
                            </h3>
                            <p class="mb-2">
                                <strong>Member:</strong> {{ $recipient['name'] }}<br>
                                <strong>Nomor:</strong> {{ $recipient['phone'] ?: 'Tidak tersedia' }}
                            </p>
                        </div>
                    </div>

                    <div class="list-card p-3 mb-3">
                        <div class="small text-muted mb-2">Pesan yang dikirim:</div>
                        <div style="background: rgba(255,255,255,.05); padding: 1rem; border-radius: 0.75rem; font-family: monospace; font-size: 0.85rem; white-space: pre-wrap; word-wrap: break-word;">{{ $dispatch['message'] }}</div>
                    </div>

                    @if($recipient['delivery_status'] === 'pending' && $recipient['url'])
                        <div class="alert alert-info mb-0">
                            <div class="mb-2">Pengiriman otomatis tidak berhasil. Silakan kirim manual melalui WhatsApp dengan tombol di bawah:</div>
                            <a href="{{ $recipient['url'] }}" target="_blank" class="btn btn-success rounded-pill px-4">
                                <i class="fab fa-whatsapp"></i> Buka WhatsApp
                            </a>
                        </div>
                    @elseif($recipient['delivery_status'] === 'failed' && !$recipient['url'])
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-times-circle"></i> {{ $recipient['delivery_error'] ?: 'Tidak dapat mengirim pesan' }}
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <div class="reminder-table-card p-0 overflow-hidden">
            <div class="p-4 border-bottom" style="border-color: var(--border) !important;">
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <div>
                        <div class="section-label">Daftar Reminder</div>
                        <h2 class="h5 fw-bold mt-2 mb-0">H-4 sampai H-7 kuning, H-1 sampai H-3 merah</h2>
                    </div>
                    <span class="status-badge badge-soft-teal">{{ $expiringMembers->count() }} member</span>
                </div>
            </div>

            <div class="reminder-table-wrap">
                <table class="table align-middle mb-0 reminder-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Foto</th>
                            <th>Nama Member</th>
                            <th>Telepon</th>
                            <th>Expired</th>
                            <th>Status</th>
                            <th>Pengingat Terakhir</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expiringMembers as $member)
                            @php
                                $daysLeft = $member->expires_at ? (int) now()->startOfDay()->diffInDays($member->expires_at->copy()->startOfDay(), false) : null;
                                $reminderLevel = $daysLeft <= 3 ? 'h3' : 'h7';
                                $reminderLabel = 'H-' . $daysLeft;
                            @endphp
                            <tr class="reminder-{{ $reminderLevel }}">
                                <td class="ps-4">
                                    @if ($member->profile_photo_url)
                                        <img src="{{ $member->profile_photo_url }}" alt="Foto {{ $member->full_name }}" class="table-avatar">
                                    @else
                                        <span class="table-avatar-placeholder">{{ $member->profile_initials }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $member->full_name }}</div>
                                    <div class="small muted-copy">{{ $member->membership_plan ?: 'Membership' }}</div>
                                </td>
                                <td>{{ $member->phone ?: '-' }}</td>
                                <td class="fw-semibold">{{ $member->expires_at?->format('d M Y') ?: '-' }}</td>
                                <td>
                                    <span class="reminder-badge {{ $reminderLevel }}">{{ $reminderLabel }}</span>
                                    <div class="small muted-copy mt-1">{{ $daysLeft }} hari lagi</div>
                                </td>
                                <td class="small muted-copy">
                                    {{ $member->last_membership_reminder_at ? $member->last_membership_reminder_at->format('d M Y, H:i') : 'Belum pernah dikirim' }}
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="{{ route('admin.announcements.reminders.send') }}">
                                        @csrf
                                        <input type="hidden" name="gym_member_id" value="{{ $member->id }}">
                                        <button type="submit" class="btn {{ $reminderLevel === 'h3' ? 'btn-danger' : 'btn-warning text-dark' }} rounded-pill btn-sm reminder-action">
                                            Ingatkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4">
                                    <div class="reminder-empty">Belum ada member yang masa aktifnya tersisa 1 sampai 7 hari.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
