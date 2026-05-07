<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Admin Arena Gym</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            :root {
                --app-bg: #f4f7fb;
                --surface: #ffffff;
                --surface-soft: #f8fafc;
                --border: #e5e7eb;
                --text-main: #0f172a;
                --text-muted: #64748b;
                --navy: #0f172a;
                --navy-soft: #1e293b;
                --teal: #0f766e;
                --green: #16a34a;
                --shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            }

            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(180deg, #f8fbff 0%, var(--app-bg) 100%);
                color: var(--text-main);
            }

            .sidebar {
                min-height: 100vh;
                background: linear-gradient(180deg, var(--navy) 0%, #1f2937 100%);
                color: #fff;
                border-radius: 1.5rem;
            }

            .brand-mark {
                width: 2.8rem;
                height: 2.8rem;
                border-radius: 1rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
                font-weight: 800;
            }

            .sidebar-link {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.85rem 1rem;
                color: rgba(255,255,255,0.76);
                text-decoration: none;
                border-radius: 1rem;
                font-weight: 500;
            }

            .sidebar-link:hover,
            .sidebar-link.active {
                color: #fff;
                background: rgba(255,255,255,0.08);
            }

            .sidebar-dot {
                width: .65rem;
                height: .65rem;
                border-radius: 999px;
                background: currentColor;
            }

            .topbar-card,
            .panel-card,
            .metric-card {
                background: var(--surface);
                border: 1px solid var(--border);
                box-shadow: var(--shadow);
            }

            .topbar-card { border-radius: 1.5rem; }
            .panel-card, .metric-card { border-radius: 1.25rem; }

            .section-label {
                font-size: .78rem;
                text-transform: uppercase;
                letter-spacing: .12em;
                color: var(--text-muted);
                font-weight: 700;
            }

            .metric-icon {
                width: 3rem;
                height: 3rem;
                border-radius: 1rem;
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
            }

            .icon-teal { background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%); }
            .icon-orange { background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%); }
            .icon-navy { background: linear-gradient(135deg, #475569 0%, #0f172a 100%); }
            .icon-green { background: linear-gradient(135deg, #4ade80 0%, #16a34a 100%); }

            .list-card {
                border: 1px solid var(--border);
                border-radius: 1rem;
                background: var(--surface-soft);
            }

            .mini-progress {
                height: .55rem;
                background: #e2e8f0;
                border-radius: 999px;
                overflow: hidden;
            }

            .mini-progress-bar {
                height: 100%;
                border-radius: 999px;
            }

            .status-badge {
                font-size: .75rem;
                font-weight: 700;
                border-radius: 999px;
                padding: .45rem .8rem;
            }

            .badge-soft-teal {
                background: rgba(15,118,110,.12);
                color: var(--teal);
            }

            .badge-soft-green {
                background: rgba(22,163,74,.12);
                color: var(--green);
            }

            .dark-panel {
                background: linear-gradient(180deg, #162132 0%, #0f172a 100%);
                color: #fff;
                border: none;
            }

            .muted-copy {
                color: var(--text-muted);
            }

            .dark-panel .section-label,
            .dark-panel .muted-copy {
                color: rgba(255,255,255,.68);
            }

            .table > :not(caption) > * > * {
                padding: 1rem;
                border-bottom-color: var(--border);
                vertical-align: middle;
            }

            .table thead th {
                font-size: .76rem;
                text-transform: uppercase;
                letter-spacing: .1em;
                color: var(--text-muted);
                font-weight: 800;
                background: #f8fafc;
            }

            @media (max-width: 991.98px) {
                .sidebar {
                    min-height: auto;
                }
            }
        </style>
    </head>
    <body>
        <div class="container-fluid py-3 py-lg-4">
            <div class="row g-3 g-lg-4">
                <aside class="col-12 col-lg-3 col-xl-2">
                    <div class="sidebar p-3 p-lg-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="brand-mark">IP</div>
                            <div>
                                <div class="fw-bold">Arena Gym</div>
                                <div class="small text-white-50">Admin Dashboard</div>
                            </div>
                        </div>

                        <div class="small text-uppercase text-white-50 fw-bold mb-3" style="letter-spacing:.12em;">Navigation</div>
                        <nav class="d-grid gap-2">
                            <a class="sidebar-link active" href="#overview"><span class="sidebar-dot"></span>Overview</a>
                            <a class="sidebar-link" href="#member-management"><span class="sidebar-dot"></span>Members</a>
                            <a class="sidebar-link" href="#checkin-monitoring"><span class="sidebar-dot"></span>Check-in</a>
                            <a class="sidebar-link" href="#membership-status"><span class="sidebar-dot"></span>Membership</a>
                            <a class="sidebar-link" href="#announcements"><span class="sidebar-dot"></span>Pengumuman</a>
                            <a class="sidebar-link" href="#daily-reports"><span class="sidebar-dot"></span>Laporan</a>
                        </nav>

                        <div class="dark-panel rounded-4 p-3 mt-4">
                            <div class="section-label text-white-50">Status Hari Ini</div>
                            <div class="h4 fw-bold mt-3 mb-1">Peak Hours</div>
                            <div class="small muted-copy">Free weight zone mendekati kapasitas penuh.</div>
                        </div>
                    </div>
                </aside>

                <main class="col-12 col-lg-9 col-xl-10">
                    <div class="topbar-card p-3 p-lg-4 mb-3 mb-lg-4" id="overview">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-xl-8">
                                <div class="section-label">Operations Summary</div>
                                <h1 class="display-6 fw-bold mt-2 mb-2">Dashboard admin Arena Gym yang fokus pada operasional harian</h1>
                                <p class="muted-copy mb-0">
                                    Kelola data member dan non member, pantau aktivitas check-in, status membership, informasi dan pengumuman, serta akses laporan harian dalam satu tampilan admin yang rapi.
                                </p>
                            </div>
                            <div class="col-12 col-xl-4">
                                <div class="row g-3">
                                    <div class="col-sm-4 col-xl-12">
                                        <div class="list-card p-3">
                                            <div class="section-label">Jam Operasional</div>
                                            <div class="fw-bold fs-5 mt-2">05:30 - 22:00</div>
                                            <div class="small muted-copy">Layanan member, guest, dan front desk</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xl-12">
                                        <div class="list-card p-3">
                                            <div class="section-label">Check-in Hari Ini</div>
                                            <div class="fw-bold fs-5 mt-2">286</div>
                                            <div class="small muted-copy">Member dan non member tercatat</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xl-12">
                                        <div class="list-card p-3">
                                            <div class="section-label">Membership Alert</div>
                                            <div class="fw-bold fs-5 mt-2 text-warning-emphasis">37 Jatuh Tempo</div>
                                            <div class="small muted-copy">Perlu follow-up perpanjangan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 g-lg-4 mb-3 mb-lg-4">
                        @foreach ($stats as $index => $stat)
                            <div class="col-12 col-md-6 col-xxl-3">
                                <div class="metric-card p-4 h-100">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div class="metric-icon {{ $index === 0 ? 'icon-teal' : ($index === 1 ? 'icon-orange' : ($index === 2 ? 'icon-navy' : 'icon-green')) }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="status-badge badge-soft-teal">{{ $stat['change'] }}</span>
                                    </div>
                                    <div class="section-label mt-4">{{ $stat['label'] }}</div>
                                    <div class="fs-2 fw-bold mt-2">{{ $stat['value'] }}</div>
                                    <div class="small muted-copy mt-2">{{ $stat['note'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row g-3 g-lg-4">
                        <div class="col-12 col-xl-8">
                            <div class="panel-card p-3 p-lg-4 mb-3 mb-lg-4" id="member-management">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                                    <div>
                                        <div class="section-label">Member Management</div>
                                        <h2 class="h3 fw-bold mt-2 mb-1">Pengelolaan data member dan non member</h2>
                                        <div class="muted-copy">Memantau kategori pengguna gym untuk kebutuhan follow-up, validasi akses, dan layanan front desk.</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.export.member-data') }}">Export Data</a>
                                        <button class="btn btn-dark rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#memberActionModal">Tambah Data</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Kategori</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($memberManagement as $item)
                                                <tr>
                                                    <td class="fw-semibold">{{ $item['category'] }}</td>
                                                    <td>{{ $item['total'] }}</td>
                                                    <td>
                                                        <span class="badge text-bg-{{ $item['status_color'] }}">{{ $item['status'] }}</span>
                                                    </td>
                                                    <td class="small muted-copy">{{ $item['note'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row g-3 g-lg-4">
                                <div class="col-12 col-lg-6">
                                    <div class="panel-card p-4 h-100" id="checkin-monitoring">
                                        <div class="section-label">Check-in Monitoring</div>
                                        <h3 class="h4 fw-bold mt-2 mb-4">Aktivitas check-in terbaru</h3>

                                        <div class="d-grid gap-3">
                                            @foreach ($checkins as $item)
                                                <div class="list-card p-3">
                                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                                        <div>
                                                            <div class="fw-semibold">{{ $item['name'] }}</div>
                                                            <div class="small muted-copy">{{ $item['time'] }} • {{ $item['access'] }}</div>
                                                        </div>
                                                        <span class="badge text-bg-{{ $item['type_color'] }}">{{ $item['type'] }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-4 p-4 rounded-4" style="background: rgba(22,163,74,.08); border:1px solid rgba(22,163,74,.15);">
                                            <div class="section-label text-success">Check-in Insight</div>
                                            <div class="display-6 fw-bold mt-2 mb-1">190</div>
                                            <div class="small muted-copy">Total check-in member aktif hari ini. Sisanya berasal dari guest pass dan trial.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="panel-card dark-panel p-4 h-100" id="membership-status">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="section-label">Membership Status</div>
                                                <h3 class="h4 fw-bold mt-2 mb-0">Pengelolaan status membership</h3>
                                            </div>
                                            <span class="status-badge badge-soft-green">Live</span>
                                        </div>

                                        <div class="d-grid gap-3 mt-3">
                                            @foreach ($membershipStatuses as $item)
                                                <div class="rounded-4 p-3" style="background: rgba(255,255,255,.06);">
                                                    <div class="d-flex justify-content-between small mb-2">
                                                        <span class="muted-copy">{{ $item['label'] }}</span>
                                                        <span class="fw-semibold text-white">{{ $item['value'] }}</span>
                                                    </div>
                                                    <div class="mini-progress">
                                                        <div class="mini-progress-bar bg-{{ $item['color'] }}" style="width: {{ $item['progress'] }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-4">
                                            <div class="d-flex justify-content-between small mb-2">
                                                <span class="muted-copy">Renewal Progress</span>
                                                <span class="fw-semibold">68% akun jatuh tempo sudah dihubungi</span>
                                            </div>
                                            <div class="mini-progress">
                                                <div class="mini-progress-bar bg-success" style="width: 68%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-4">
                            <div class="panel-card p-4 mb-3 mb-lg-4" id="announcements">
                                <div class="section-label">Informasi & Pengumuman</div>
                                <h3 class="h4 fw-bold mt-2 mb-4">Kelola informasi harian</h3>

                                <div class="d-grid gap-3">
                                    @foreach ($announcements as $item)
                                        <div class="list-card p-3">
                                            <div class="d-flex align-items-start gap-3">
                                                <span class="badge rounded-pill text-bg-primary mt-1">&nbsp;</span>
                                                <div class="small">{{ $item }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="panel-card p-4 mb-3 mb-lg-4" id="daily-reports">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                    <div>
                                        <div class="section-label">Daily Reports</div>
                                        <h3 class="h4 fw-bold mt-2 mb-0">Akses laporan harian</h3>
                                    </div>
                                    <span class="status-badge badge-soft-teal">Minggu Ini</span>
                                </div>

                                <div class="d-grid gap-3">
                                    @foreach ($reports as $report)
                                        <div class="list-card p-3">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <div class="fw-semibold">{{ $report['title'] }}</div>
                                                    <div class="small muted-copy">{{ $report['detail'] }}</div>
                                                </div>
                                                <span class="badge text-bg-light border text-dark">{{ $report['status'] }}</span>
                                            </div>
                                            <div class="mt-3">
                                                <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#reportActionModal">
                                                    Buka laporan
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="panel-card dark-panel p-4">
                                <div class="section-label">Quick Actions</div>
                                <h3 class="h4 fw-bold mt-2 mb-4">Aksi admin</h3>

                                <div class="d-grid gap-3">
                                    @foreach ($quickActions as $action)
                                        <button class="btn {{ $action['style'] === 'light' ? 'btn-light' : 'btn-outline-light' }} text-start rounded-4 p-3" type="button" data-bs-toggle="modal" data-bs-target="#{{ $action['modal'] }}">
                                            <div class="small {{ $action['style'] === 'light' ? 'text-secondary' : 'text-white-50' }}">{{ $action['section'] }}</div>
                                            <div class="fw-semibold">{{ $action['title'] }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <div class="modal fade" id="memberActionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Data Member / Non Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" placeholder="Masukkan nama pengguna">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-select">
                                <option>Member Reguler</option>
                                <option>Member Personal Training</option>
                                <option>Non Member / Guest</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" rows="3" placeholder="Tambahkan catatan admin"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Simpan Data</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="checkinActionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">Pantau Aktivitas Check-in</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Aksi cepat untuk memonitor kunjungan hari ini.</p>
                        <div class="list-group">
                            <a href="#checkin-monitoring" class="list-group-item list-group-item-action" data-bs-dismiss="modal">Lihat daftar check-in terbaru</a>
                            <button type="button" class="list-group-item list-group-item-action" data-bs-dismiss="modal">Verifikasi guest pass</button>
                            <button type="button" class="list-group-item list-group-item-action" data-bs-dismiss="modal">Cetak ringkasan check-in</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="announcementActionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">Kelola Informasi dan Pengumuman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Pengumuman</label>
                            <input type="text" class="form-control" placeholder="Contoh: Promo referral April">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Isi Pengumuman</label>
                            <textarea class="form-control" rows="4" placeholder="Tulis informasi yang ingin ditampilkan"></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Tayangkan Ke</label>
                            <select class="form-select">
                                <option>Front desk</option>
                                <option>Grup member</option>
                                <option>Semua kanal</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Publikasikan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="reportActionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">Akses Laporan Harian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Pilih aksi cepat untuk laporan operasional Arena Gym.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.export.member-data') }}" class="btn btn-outline-secondary">Unduh CSV data member</a>
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Tandai laporan sudah diperiksa</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
