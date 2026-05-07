<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? 'Check-in Member Arena Gym' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                min-height: 100vh;
                background:
                    radial-gradient(circle at top right, rgba(45, 212, 191, 0.12), transparent 28%),
                    radial-gradient(circle at bottom left, rgba(14, 165, 233, 0.12), transparent 26%),
                    linear-gradient(160deg, #f6fdff 0%, #eef9fb 48%, #e8f5f8 100%);
                color: #11212b;
            }

            .member-checkin-shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                padding: 2rem 0;
            }

            .member-checkin-card {
                border: 1px solid rgba(8, 145, 178, 0.12);
                border-radius: 1.75rem;
                background: rgba(255, 255, 255, 0.95);
                box-shadow: 0 24px 48px rgba(8, 47, 73, 0.08);
            }

            .section-label {
                font-size: .78rem;
                text-transform: uppercase;
                letter-spacing: .12em;
                color: #4b6470;
                font-weight: 800;
            }
        </style>
    </head>
    <body>
        <div class="member-checkin-shell">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-7 col-xl-6">
                        <div class="member-checkin-card p-4 p-lg-5">
                            <div class="section-label">Check-in Member</div>
                            <h1 class="display-6 fw-bold mt-2 mb-3">Check-in membership Arena Gym</h1>
                            <p class="text-secondary mb-4">Isi data member terlebih dahulu. Pengajuan ini akan masuk ke kasir untuk divalidasi sebelum check-in tercatat sebagai hadir.</p>

                            @if (session('status'))
                                <div class="alert alert-success rounded-4 mb-4" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger rounded-4 mb-4" role="alert">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('member.checkin.store') }}">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold" for="submitted_name">Nama yang Mengisi</label>
                                        <input id="submitted_name" name="submitted_name" type="text" class="form-control @error('submitted_name') is-invalid @enderror" value="{{ old('submitted_name') }}" placeholder="Nama lengkap Anda" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold" for="submitted_phone">No. HP Member</label>
                                        <input id="submitted_phone" name="submitted_phone" type="text" class="form-control @error('submitted_phone') is-invalid @enderror" value="{{ old('submitted_phone') }}" placeholder="Contoh: 08123456789" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold" for="notes">Catatan</label>
                                        <textarea id="notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Opsional, misalnya latihan pagi atau catatan khusus">{{ old('notes') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-dark rounded-pill w-100 py-3">Kirim untuk Validasi Kasir</button>
                                    </div>
                                </div>
                            </form>

                            <div class="small text-secondary mt-4">
                                Setelah dikirim, tunjukkan halaman konfirmasi kepada kasir agar data bisa dicocokkan dengan member aktif.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
