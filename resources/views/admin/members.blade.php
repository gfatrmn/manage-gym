@extends('admin.layout')

@section('content')
    <style>
        .member-summary-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            margin-bottom: 1.5rem;
        }

        .member-summary-card {
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            background: var(--surface);
            padding: 1.25rem;
        }

        .member-summary-card .summary-label {
            font-size: 0.82rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .12em;
            margin-bottom: .75rem;
        }

        .member-summary-card .summary-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .member-table-wrap {
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 0.6rem;
            -webkit-overflow-scrolling: touch;
            scrollbar-gutter: stable;
        }

        .member-table {
            width: 100%;
            min-width: 1360px;
            table-layout: auto;
        }

        .member-table th,
        .member-table td {
            vertical-align: middle;
        }

        .member-table th {
            white-space: nowrap;
        }

        .member-table td {
            overflow-wrap: anywhere;
        }

        .member-col-photo {
            width: 96px;
            min-width: 96px;
            max-width: 96px;
        }

        .member-col-name {
            min-width: 190px;
        }

        .member-col-email {
            min-width: 210px;
        }

        .member-col-phone,
        .member-col-code,
        .member-col-plan,
        .member-col-payment,
        .member-col-amount,
        .member-col-status,
        .member-col-period {
            white-space: nowrap;
        }

        .member-col-actions {
            width: 230px;
            min-width: 230px;
        }

        .member-photo {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.12);
            background: rgba(255,255,255,0.06);
        }

        .member-photo-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff3b3b, #a60f1f);
            color: #fff;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .member-photo-upload-preview {
            width: 84px;
            height: 84px;
            border-radius: 1rem;
            object-fit: cover;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.05);
        }

        .member-period {
            display: grid;
            gap: .15rem;
            line-height: 1.35;
            white-space: nowrap;
        }

        .member-actions {
            display: flex;
            flex-wrap: nowrap;
            gap: .55rem;
            justify-content: flex-end;
        }

        .member-actions form {
            margin: 0;
        }

        .member-actions .btn {
            white-space: nowrap;
        }

        .member-training-history {
            display: grid;
            gap: .75rem;
        }

        .member-training-history-item {
            border: 1px solid var(--border);
            border-radius: .9rem;
            padding: .8rem .95rem;
            background: rgba(255,255,255,0.03);
        }

        .member-photo-request-card {
            border-radius: 1.25rem;
            background: rgba(255,255,255,0.03);
        }

        @media (max-width: 1200px) {
            .member-table {
                min-width: 1320px;
            }

            .member-table > :not(caption) > * > * {
                padding: .85rem .9rem;
            }

            .member-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .member-actions .btn,
            .member-actions form .btn {
                width: 100%;
            }
        }
    </style>

    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Member</div>
        <h1 class="display-6 fw-bold mt-2 mb-2">Data Member Aktif & Expired</h1>
        <p class="muted-copy mb-0">Tampilkan daftar member persis seperti contoh UI yang diinginkan, tanpa merubah logika backend.</p>
    </div>

    <div class="member-summary-grid mb-4">
        <div class="member-summary-card">
            <div class="summary-label">Member Aktif</div>
            <div class="summary-value">{{ $activeMembers->count() }}</div>
            <div class="small muted-copy mt-2">Member dengan masa aktif yang masih berjalan.</div>
        </div>
        <div class="member-summary-card">
            <div class="summary-label">Expired</div>
            <div class="summary-value">{{ $expiredMembers->count() }}</div>
            <div class="small muted-copy mt-2">Member yang perlu diperbarui atau dihubungi kembali.</div>
        </div>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">Daftar Member</h2>
                <div class="small muted-copy">Pilih kategori Aktif atau Expired untuk melihat daftar secara detail.</div>
            </div>
            <a href="{{ route('admin.export.member-data') }}" class="btn btn-outline-secondary rounded-pill">Export Data</a>
        </div>

        @php
            $isExpiredSection = $memberSection === 'expired';
            $currentSection = [
                'title' => $isExpiredSection ? 'Member Expired' : 'Member Aktif',
                'items' => $isExpiredSection ? $expiredMembers : $activeMembers,
                'badge' => $isExpiredSection ? 'text-bg-danger' : 'text-bg-success',
                'status' => $isExpiredSection ? 'Expired' : 'Aktif',
            ];
        @endphp

        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="{{ route('admin.members', ['section' => 'active', 'q' => $memberSearch ?: null]) }}" class="btn rounded-pill {{ $isExpiredSection ? 'btn-outline-secondary' : 'btn-dark' }}">Member Aktif</a>
            <a href="{{ route('admin.members', ['section' => 'expired', 'q' => $memberSearch ?: null]) }}" class="btn rounded-pill {{ $isExpiredSection ? 'btn-dark' : 'btn-outline-secondary' }}">Member Expired</a>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h3 class="h5 fw-bold mb-1">{{ $currentSection['title'] }}</h3>
                <div class="small muted-copy">{{ $currentSection['items']->count() }} member dalam kategori ini.</div>
            </div>
            <span class="badge {{ $currentSection['badge'] }} rounded-pill py-2 px-3">{{ $currentSection['status'] }}</span>
        </div>

        <form method="GET" action="{{ route('admin.members') }}" class="row g-3 align-items-end mb-4">
            <input type="hidden" name="section" value="{{ $memberSection }}">
            <div class="col-12 col-lg-8">
                <label for="memberSearch" class="form-label fw-semibold">Cari member</label>
                <input
                    type="search"
                    id="memberSearch"
                    name="q"
                    class="form-control"
                    value="{{ $memberSearch }}"
                    placeholder="Cari nama, email, telepon, kode check-in, atau paket">
            </div>
            <div class="col-6 col-lg-2">
                <button type="submit" class="btn btn-dark rounded-pill px-4 w-100">Cari</button>
            </div>
            <div class="col-6 col-lg-2">
                <a href="{{ route('admin.members', ['section' => $memberSection]) }}" class="btn btn-outline-secondary rounded-pill px-4 w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive member-table-wrap">
            <table class="table align-middle mb-0 member-table">
                <thead>
                    <tr>
                        <th class="member-col-photo">Foto</th>
                        <th class="member-col-name">Nama</th>
                        <th class="member-col-email">Email</th>
                        <th class="member-col-phone">Telepon</th>
                        <th class="member-col-code">Kode Check-in</th>
                        <th class="member-col-plan">Paket</th>
                        <th class="member-col-payment">Metode Bayar</th>
                        <th class="member-col-amount">Biaya</th>
                        <th class="member-col-status">Status</th>
                        <th class="member-col-period">Masa Aktif</th>
                        <th class="member-col-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($currentSection['items'] as $member)
                        <tr>
                            <td class="member-col-photo">
                                @if ($member->profile_photo_url)
                                    <img src="{{ $member->profile_photo_url }}" alt="Foto {{ $member->full_name }}" class="member-photo">
                                @else
                                    <span class="member-photo-placeholder">{{ \Illuminate\Support\Str::of($member->full_name)->trim()->explode(' ')->take(2)->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->implode('') }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold member-col-name">{{ $member->full_name }}</td>
                            <td class="member-col-email">{{ $member->email ?: '-' }}</td>
                            <td class="member-col-phone">{{ $member->phone ?: '-' }}</td>
                            <td class="small fw-semibold member-col-code">{{ $member->checkin_code ?: '-' }}</td>
                            <td class="member-col-plan">{{ $member->membership_plan ?: 'Bulanan' }}</td>
                            <td class="member-col-payment"><span class="badge text-bg-primary">{{ strtoupper($member->payment_method ?? 'cash') }}</span></td>
                            <td class="fw-semibold member-col-amount">Rp{{ number_format($member->payment_amount ?? 50000, 0, ',', '.') }}</td>
                            <td class="member-col-status"><span class="badge {{ $currentSection['badge'] }}">{{ $currentSection['status'] }}</span></td>
                            <td class="small muted-copy member-period member-col-period">
                                <span>{{ $member->joined_at?->format('d M Y') ?: '-' }}</span>
                                @if ($member->expires_at)
                                    <span>s/d {{ $member->expires_at->format('d M Y') }}</span>
                                @endif
                            </td>
                            <td class="member-col-actions">
                                <div class="member-actions">
                                    <button class="btn btn-sm btn-outline-light rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#detailMemberModal{{ $member->id }}">Detail</button>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#editMemberModal{{ $member->id }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-secondary">{{ $memberSearch ? 'Data member tidak ditemukan untuk kata kunci tersebut.' : 'Belum ada data pada kategori ini.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($members as $member)
        <div class="modal fade" id="detailMemberModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">Detail Member {{ $member->full_name }}</h5>
                            <div class="small text-secondary">Riwayat latihan dan transaksi selama 1 bulan terakhir.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $memberCheckins = $monthlyCheckinHistory->get($member->id, collect());
                        @endphp
                        <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                            @if ($member->profile_photo_url)
                                <img src="{{ $member->profile_photo_url }}" alt="Foto {{ $member->full_name }}" class="member-photo-upload-preview">
                            @else
                                <span class="member-photo-placeholder member-photo-upload-preview">{{ \Illuminate\Support\Str::of($member->full_name)->trim()->explode(' ')->take(2)->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->implode('') }}</span>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $member->full_name }}</div>
                                <div class="small text-secondary">{{ $member->email ?: 'Email belum diisi' }}</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                            <div class="fw-semibold">{{ $memberCheckins->count() }} sesi latihan tercatat</div>
                            <div class="small text-secondary">Periode {{ now()->subMonth()->format('d M Y') }} - {{ now()->format('d M Y') }}</div>
                        </div>

                        @php
                            $memberProductHistory = $memberProductHistory ?? collect();
                            $memberProductPurchases = $memberProductHistory->get($member->id, collect());
                            $hasPendingPhotoRequest = $member->profile_photo_pending_status === 'pending' && $member->profile_photo_pending_url;
                        @endphp

                        @if ($hasPendingPhotoRequest)
                            <div class="member-photo-request-card mb-4 p-4 rounded-3 border">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <div class="fw-semibold mb-1">Permintaan ganti foto profil</div>
                                        <div class="small text-secondary">Member mengajukan permintaan penggantian foto profil. Admin dapat menyetujui atau menolak permintaan ini.</div>
                                    </div>
                                    <span class="badge text-bg-warning">Pending</span>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <img src="{{ $member->profile_photo_pending_url }}" alt="Preview foto terbaru {{ $member->full_name }}" class="rounded-3" style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                    <div class="col">
                                        <div class="small text-secondary mb-2">Foto baru yang diajukan:</div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <form method="POST" action="{{ route('admin.members.profile-photo-requests.approve', $member) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success rounded-pill btn-sm">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.members.profile-photo-requests.reject', $member) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger rounded-pill btn-sm">Tolak</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="member-product-history mb-4">
                            <div class="fw-semibold mb-2">Riwayat pembelian produk</div>
                            <div class="small text-secondary mb-3">Semua transaksi produk yang dicatat untuk member ini.</div>

                            @if ($memberProductPurchases->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Produk</th>
                                                <th>Qty</th>
                                                <th>Nominal</th>
                                                <th>Metode</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($memberProductPurchases as $purchase)
                                                <tr>
                                                    <td>{{ $purchase->transaction_at->format('d M Y') }}</td>
                                                    <td>{{ $purchase->product?->name ?? $purchase->transaction_type }}</td>
                                                    <td>{{ $purchase->quantity }}</td>
                                                    <td>Rp{{ number_format($purchase->amount, 0, ',', '.') }}</td>
                                                    <td>{{ strtoupper($purchase->payment_method) }}</td>
                                                    <td><span class="badge text-bg-{{ $purchase->payment_status === 'verified' ? 'success' : 'warning' }}">{{ $purchase->payment_status === 'verified' ? 'Lunas' : 'Pending' }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-secondary">Belum ada riwayat pembelian produk untuk member ini.</div>
                            @endif
                        </div>

                        @if ($memberCheckins->isNotEmpty())
                            <div class="member-training-history">
                                @foreach ($memberCheckins as $checkin)
                                    <div class="member-training-history-item">
                                        <div class="fw-semibold">{{ $checkin->checked_in_at->format('d M Y') }}</div>
                                        <div class="small text-secondary">{{ $checkin->checked_in_at->format('H:i') }} | {{ ucfirst($checkin->checkin_method ?? 'admin') }}</div>
                                        <div class="small text-secondary mt-1">{{ $checkin->notes ?: 'Latihan member tercatat tanpa catatan tambahan.' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-secondary">Belum ada riwayat latihan untuk member ini dalam 1 bulan terakhir.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editMemberModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form method="POST" action="{{ route('admin.members.update', $member) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Member {{ $member->full_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="full_name" class="form-control" value="{{ $member->full_name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $member->email }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $member->phone }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Foto Profil</label>
                                    <input type="file" name="profile_photo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="small text-secondary mt-2">Format: JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Foto Saat Ini</label>
                                    @if ($member->profile_photo_url)
                                        <img src="{{ $member->profile_photo_url }}" alt="Foto {{ $member->full_name }}" class="member-photo-upload-preview">
                                    @else
                                        <span class="member-photo-placeholder member-photo-upload-preview">{{ \Illuminate\Support\Str::of($member->full_name)->trim()->explode(' ')->take(2)->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->implode('') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Paket Membership</label>
                                    <input type="text" class="form-control" value="Bulanan" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Biaya Membership</label>
                                    <input type="text" class="form-control" value="Rp50.000 / bulan" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="cash" @selected($member->payment_method === 'cash')>Cash</option>
                                        <option value="qris" @selected($member->payment_method === 'qris')>QRIS</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Gabung</label>
                                    <input type="date" name="joined_at" class="form-control" value="{{ $member->joined_at?->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Masa Aktif Sampai</label>
                                    <input type="text" class="form-control" value="{{ $member->expires_at?->format('d M Y') ?: 'Otomatis 1 bulan setelah tanggal daftar' }}" disabled>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ $member->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-dark">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
