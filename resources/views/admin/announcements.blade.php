@extends('admin.layout')

@section('content')
    @php
        $activeAnnouncements = $announcements->where('status', 'active')->values();
        $scheduledAnnouncements = $announcements->where('status', 'scheduled')->values();
        $archivedAnnouncements = $announcements->where('status', 'archived')->values();
    @endphp

    <style>
        .announcement-hero {
            position: relative;
            overflow: hidden;
        }

        .announcement-hero::before {
            content: '';
            position: absolute;
            inset: -30% auto auto 55%;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 59, 59, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }

        .announcement-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }

        .announcement-stat {
            position: relative;
            padding: 1.1rem 1.15rem;
            border-radius: 1.15rem;
            border: 1px solid rgba(255,255,255,0.08);
            background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
            min-width: 0;
        }

        .announcement-stat-value {
            font-size: clamp(1.5rem, 2.4vw, 2.2rem);
            font-weight: 800;
            line-height: 1;
            margin-top: .55rem;
        }

        .announcement-actions {
            display: grid;
            gap: .85rem;
        }

        .announcement-action-card {
            border: 1px solid var(--border);
            border-radius: 1.15rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.02) 100%);
            padding: 1rem;
        }

        .announcement-action-card .btn {
            width: 100%;
        }

        .announcement-list {
            display: grid;
            gap: 1rem;
        }

        .announcement-card {
            border: 1px solid var(--border);
            border-radius: 1.2rem;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.02) 100%);
            padding: 1.15rem;
        }

        .announcement-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .announcement-card-copy {
            min-width: 0;
        }

        .announcement-card-copy h3,
        .announcement-card-copy p {
            overflow-wrap: anywhere;
        }

        .announcement-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .85rem;
        }

        .announcement-dot {
            width: .8rem;
            height: .8rem;
            margin-top: .35rem;
            border-radius: 50%;
            flex: 0 0 auto;
            box-shadow: 0 0 0 6px rgba(255, 255, 255, 0.04);
        }

        .announcement-dot-active {
            background: linear-gradient(135deg, #ff7373, #ff3b3b);
        }

        .announcement-dot-scheduled {
            background: linear-gradient(135deg, #ffca7a, #ff9736);
        }

        .announcement-dot-archived {
            background: linear-gradient(135deg, #7a7a7a, #4a4a4a);
        }

        .announcement-meta-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: .45rem .8rem;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            color: var(--text-muted);
            font-size: .82rem;
            font-weight: 600;
            white-space: normal;
            max-width: 100%;
        }

        .announcement-expiring-wrap {
            overflow-x: auto;
        }

        .announcement-expiring-table {
            min-width: 880px;
        }

        .announcement-empty {
            border: 1px dashed rgba(255,255,255,0.12);
            border-radius: 1.15rem;
            padding: 2rem 1.25rem;
            text-align: center;
            color: var(--text-muted);
            background: rgba(255,255,255,0.02);
        }

        .announcement-summary-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: .7rem;
        }

        .announcement-summary-row .list-card {
            min-width: 0;
            padding: .85rem .95rem;
        }

        .announcement-summary-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .announcement-summary-copy {
            min-width: 0;
        }

        .announcement-summary-title {
            font-size: .92rem;
            line-height: 1.25;
        }

        .announcement-summary-row .fw-semibold,
        .announcement-summary-row .small {
            overflow-wrap: anywhere;
        }

        .announcement-summary-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            padding: .42rem .72rem;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .announcement-reminder-button {
            white-space: nowrap;
        }

        @media (max-width: 1399.98px) {
            .announcement-hero {
                overflow: hidden;
            }

            .announcement-hero::before {
                inset: -15% -8% auto auto;
                width: 240px;
                height: 240px;
            }
        }

        @media (max-width: 1199.98px) {
            .announcement-expiring-table {
                min-width: 760px;
            }
        }

        @media (max-width: 991.98px) {
            .announcement-card-head {
                flex-direction: column;
            }

            .announcement-card {
                padding: 1rem;
            }

            .announcement-meta-pill {
                width: 100%;
                justify-content: flex-start;
                border-radius: 1rem;
            }

            .announcement-summary-card {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .announcement-expiring-table {
                min-width: 680px;
            }

            .announcement-reminder-button {
                min-width: 138px;
            }
        }
    </style>

    <div class="topbar-card announcement-hero p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-xl-7">
                <div class="section-label">Pengumuman</div>
                <h1 class="display-6 fw-bold mt-2 mb-3">Pusat informasi operasional dan komunikasi member</h1>
                <p class="muted-copy mb-0">Halaman ini saya rapikan supaya alur kerja admin lebih jelas: status pengumuman cepat terbaca, aksi utama lebih dekat, dan daftar member yang perlu reminder tetap mudah dipantau.</p>
            </div>
            <div class="col-12 col-xl-5">
                <div class="announcement-stats">
                    <div class="announcement-stat">
                        <div class="section-label">Aktif</div>
                        <div class="announcement-stat-value">{{ $activeAnnouncements->count() }}</div>
                        <div class="small muted-copy mt-2">Sedang tampil untuk operasional atau member.</div>
                    </div>
                    <div class="announcement-stat">
                        <div class="section-label">Terjadwal</div>
                        <div class="announcement-stat-value">{{ $scheduledAnnouncements->count() }}</div>
                        <div class="small muted-copy mt-2">Siap tayang otomatis sesuai waktu publikasi.</div>
                    </div>
                    <div class="announcement-stat">
                        <div class="section-label">Reminder</div>
                        <div class="announcement-stat-value">{{ $expiringMembers->count() }}</div>
                        <div class="small muted-copy mt-2">Member dengan masa aktif yang perlu di-follow-up.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="panel-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <div class="section-label">Feed Pengumuman</div>
                        <h2 class="h4 fw-bold mt-2 mb-1">Daftar pengumuman terbaru</h2>
                        <div class="small muted-copy">Status, jadwal tayang, dan isi pesan ditata dalam kartu agar lebih mudah dipindai.</div>
                    </div>
                    <span class="status-badge badge-soft-teal">{{ $announcements->count() }} total</span>
                </div>

                <div class="announcement-list">
                    @forelse ($announcements as $item)
                        @php
                            $statusClass = match ($item->status) {
                                'scheduled' => 'text-bg-warning',
                                'archived' => 'text-bg-secondary',
                                default => 'text-bg-success',
                            };
                            $dotClass = match ($item->status) {
                                'scheduled' => 'announcement-dot-scheduled',
                                'archived' => 'announcement-dot-archived',
                                default => 'announcement-dot-active',
                            };
                        @endphp
                        <article class="announcement-card">
                            <div class="d-flex align-items-start gap-3">
                                <span class="announcement-dot {{ $dotClass }}"></span>
                                <div class="flex-grow-1 announcement-card-copy">
                                    <div class="announcement-card-head">
                                        <div>
                                            <h3 class="h5 fw-bold mb-2">{{ $item->title }}</h3>
                                            <p class="muted-copy mb-0">{{ $item->body }}</p>
                                        </div>
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                    </div>
                                    <div class="announcement-card-meta">
                                        <span class="announcement-meta-pill">
                                            Publish:
                                            {{ $item->publish_at ? $item->publish_at->format('d M Y, H:i') : 'Belum ditentukan' }}
                                        </span>
                                        @if ($item->archived_at)
                                            <span class="announcement-meta-pill">
                                                Arsip:
                                                {{ $item->archived_at->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="announcement-empty">Belum ada pengumuman yang tercatat di database.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="panel-card p-4 mb-4">
                <div class="section-label">Aksi Cepat</div>
                <h2 class="h4 fw-bold mt-2 mb-2">Kelola publikasi</h2>
                <p class="muted-copy mb-4">Aksi yang paling sering dipakai saya kelompokkan di sisi kanan supaya lebih cepat dijangkau.</p>

                <div class="announcement-actions">
                    <div class="announcement-action-card">
                        <div class="fw-semibold mb-1">Publikasikan sekarang</div>
                        <div class="small muted-copy mb-3">Buat pengumuman yang langsung tampil.</div>
                        <button class="btn btn-dark rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#publishAnnouncementModal">Buat pengumuman baru</button>
                    </div>
                    <div class="announcement-action-card">
                        <div class="fw-semibold mb-1">Jadwalkan publikasi</div>
                        <div class="small muted-copy mb-3">Siapkan pesan untuk tayang di waktu tertentu.</div>
                        <button class="btn btn-outline-secondary rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#scheduleAnnouncementModal">Atur jadwal tayang</button>
                    </div>
                    <div class="announcement-action-card">
                        <div class="fw-semibold mb-1">Arsipkan pengumuman</div>
                        <div class="small muted-copy mb-3">Pindahkan info lama agar feed tetap bersih.</div>
                        <button class="btn btn-outline-secondary rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#archiveAnnouncementModal">Pindahkan ke arsip</button>
                    </div>
                </div>
            </div>

            <div class="panel-card p-4">
                <div class="section-label">Ringkasan Status</div>
                <h2 class="h4 fw-bold mt-2 mb-3">Distribusi pengumuman</h2>
                <div class="announcement-summary-row">
                    <div class="list-card">
                        <div class="announcement-summary-card">
                            <div class="announcement-summary-copy">
                                <div class="fw-semibold announcement-summary-title">Pengumuman aktif</div>
                                <div class="small muted-copy mt-1">Sedang aktif.</div>
                            </div>
                            <span class="announcement-summary-count text-bg-success">{{ $activeAnnouncements->count() }}</span>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="announcement-summary-card">
                            <div class="announcement-summary-copy">
                                <div class="fw-semibold announcement-summary-title">Pengumuman terjadwal</div>
                                <div class="small muted-copy mt-1">Menunggu tayang.</div>
                            </div>
                            <span class="announcement-summary-count text-bg-warning">{{ $scheduledAnnouncements->count() }}</span>
                        </div>
                    </div>
                    <div class="list-card">
                        <div class="announcement-summary-card">
                            <div class="announcement-summary-copy">
                                <div class="fw-semibold announcement-summary-title">Pengumuman terarsip</div>
                                <div class="small muted-copy mt-1">Sudah diarsipkan.</div>
                            </div>
                            <span class="announcement-summary-count text-bg-secondary">{{ $archivedAnnouncements->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <div class="section-label">Reminder Membership</div>
                <h2 class="h4 fw-bold mt-2 mb-1">Member yang akan expired</h2>
                <div class="small muted-copy">Daftar member yang masa aktifnya berakhir dalam 7 hari agar follow-up perpanjangan lebih rapi.</div>
            </div>
            <span class="status-badge badge-soft-teal">{{ $expiringMembers->count() }} member</span>
        </div>

        <div class="announcement-expiring-wrap">
            <table class="table align-middle mb-0 announcement-expiring-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Member</th>
                        <th>Telepon</th>
                        <th>Berakhir</th>
                        <th>Sisa Waktu</th>
                        <th>Pengingat Terakhir</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expiringMembers as $member)
                        <tr>
                            <td>
                                @if ($member->profile_photo_url)
                                    <img src="{{ $member->profile_photo_url }}" alt="Foto {{ $member->full_name }}" class="table-avatar">
                                @else
                                    <span class="table-avatar-placeholder">{{ $member->profile_initials }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $member->full_name }}</td>
                            <td>{{ $member->phone ?: '-' }}</td>
                            <td>{{ $member->expires_at?->format('d M Y') ?: '-' }}</td>
                            <td>
                                <span class="badge text-bg-warning">
                                    {{ now()->diffInDays($member->expires_at, false) === 0 ? 'Hari ini' : now()->diffInDays($member->expires_at, false).' hari lagi' }}
                                </span>
                            </td>
                            <td class="small muted-copy">
                                {{ $member->last_membership_reminder_at ? $member->last_membership_reminder_at->format('d M Y, H:i') : 'Belum pernah dikirim' }}
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.announcements.reminders.send') }}">
                                    @csrf
                                    <input type="hidden" name="gym_member_id" value="{{ $member->id }}">
                                    <button type="submit" class="btn btn-dark rounded-pill btn-sm announcement-reminder-button">Kirim Pengingat</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">Belum ada member yang masa langganannya akan berakhir dalam 7 hari.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="publishAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('admin.announcements.publish') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Pengumuman Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input name="title" class="form-control mb-3" placeholder="Judul pengumuman" required>
                        <textarea name="body" class="form-control" rows="4" placeholder="Isi pengumuman" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-dark">Publikasikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scheduleAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('admin.announcements.schedule') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Jadwalkan Publikasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input name="title" class="form-control mb-3" placeholder="Judul pengumuman" required>
                        <textarea name="body" class="form-control mb-3" rows="4" placeholder="Isi pengumuman" required></textarea>
                        <input type="datetime-local" name="publish_at" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-dark">Jadwalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="archiveAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('admin.announcements.archive') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Arsipkan Pengumuman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <select name="announcement_id" class="form-select" required>
                            <option value="">Pilih pengumuman</option>
                            @foreach ($announcements->where('status', '!=', 'archived') as $item)
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-dark">Arsipkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
