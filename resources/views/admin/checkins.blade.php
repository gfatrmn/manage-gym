@extends('admin.layout')

@section('content')
    <style>
        .barcode-camera-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
        }

        .barcode-camera-preview {
            position: relative;
            width: 100%;
            border-radius: 1rem;
            background: #0f172a;
            aspect-ratio: 16 / 10;
            overflow: hidden;
        }

        .barcode-camera-video,
        .barcode-camera-fallback,
        .barcode-camera-fallback video,
        .barcode-camera-fallback canvas {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Check-in</div>
        <h1 class="display-6 fw-bold mt-2 mb-2">Check-in member hari ini</h1>
        <p class="muted-copy mb-0">Halaman ini menampilkan daftar member yang sudah check-in hari ini, urut dari yang paling baru. Check-in di halaman ini dibantu langsung oleh admin.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="panel-card p-4 h-100">
                <div class="section-label">Hari Ini</div>
                <div class="display-6 fw-bold mt-2">{{ $todayCheckinsCount }}</div>
                <p class="muted-copy mb-0">Total member yang check-in pada {{ now()->format('d M Y') }}.</p>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="panel-card p-4 h-100">
                <div class="section-label">Check-in Terbaru</div>
                @if ($latestCheckin)
                    <h2 class="h4 fw-bold mt-2 mb-1">{{ $latestCheckin->member?->full_name }}</h2>
                    <p class="muted-copy mb-0">
                        {{ $latestCheckin->checked_in_at->format('d M Y, H:i') }}
                        -
                        {{ $latestCheckin->checkin_method === 'cashier' ? 'Dibantu kasir' : 'Dibantu admin' }}
                    </p>
                @else
                    <h2 class="h4 fw-bold mt-2 mb-1">Belum ada log check-in</h2>
                    <p class="muted-copy mb-0">Belum ada member yang check-in pada {{ now()->format('d M Y') }}.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="panel-card p-4 mb-4">
        <div class="section-label">Bantu Check-in Admin</div>
        <h2 class="h4 fw-bold mt-2 mb-2">Form check-in member</h2>
        <p class="muted-copy mb-3">Admin bisa scan barcode member atau tetap pilih manual. Sistem akan langsung mencatat check-in dengan jam saat ini.</p>
        <form method="POST" action="{{ route('admin.checkins.store') }}">
            @csrf
            <input type="hidden" name="source" value="admin">
            <div class="row g-3">
                <div class="col-12 col-xl-6">
                    <label class="form-label">Scan Barcode Member</label>
                    <input
                        type="text"
                        name="checkin_code"
                        class="form-control @error('checkin_code') is-invalid @enderror"
                        value="{{ old('checkin_code') }}"
                        placeholder="Scan atau ketik barcode member"
                        autocomplete="off"
                        data-barcode-input>
                    @error('checkin_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="small muted-copy mt-2">Scanner barcode biasanya langsung mengetik kode dan menekan Enter setelah scan.</div>
                </div>
                <div class="col-12">
                    <div class="barcode-camera-card">
                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                            <div>
                                <div class="fw-semibold">Scan dari Kamera</div>
                                <div class="small muted-copy">Buka kamera, arahkan ke barcode member, lalu sistem akan otomatis check-in.</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-light rounded-pill" data-camera-start>Buka Kamera Scan</button>
                                <button type="button" class="btn btn-outline-secondary rounded-pill d-none" data-camera-stop>Tutup Kamera</button>
                            </div>
                        </div>
                        <div class="barcode-camera-preview d-none" data-camera-preview-shell>
                            <video class="barcode-camera-video d-none" autoplay muted playsinline data-camera-preview></video>
                            <div class="barcode-camera-fallback d-none" data-camera-fallback></div>
                        </div>
                        <div class="small muted-copy mt-3" data-camera-status>Kamera belum aktif.</div>
                    </div>
                </div>
                <div class="col-12 col-xl-8">
                    <label class="form-label">Member Aktif Manual</label>
                    <select name="gym_member_id" class="form-select @error('gym_member_id') is-invalid @enderror">
                        <option value="">Pilih member</option>
                        @foreach ($memberOptions as $member)
                            <option value="{{ $member->id }}" @selected((string) old('gym_member_id') === (string) $member->id)>
                                {{ $member->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('gym_member_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-xl-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark rounded-pill w-100">Simpan Check-in Admin</button>
                </div>
                <div class="col-12">
                    <div class="small muted-copy">Isi salah satu: scan barcode atau pilih member manual. Saat disimpan, sistem langsung mencatat check-in dengan jam sekarang.</div>
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Opsional.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const barcodeInput = document.querySelector('[data-barcode-input]');
            const cameraStartButton = document.querySelector('[data-camera-start]');
            const cameraStopButton = document.querySelector('[data-camera-stop]');
            const cameraPreviewShell = document.querySelector('[data-camera-preview-shell]');
            const cameraPreview = document.querySelector('[data-camera-preview]');
            const cameraFallback = document.querySelector('[data-camera-fallback]');
            const cameraStatus = document.querySelector('[data-camera-status]');
            const form = barcodeInput?.closest('form');
            let mediaStream = null;
            let detector = null;
            let scanFrameId = null;
            let isDetecting = false;
            let isSubmitting = false;
            let quaggaActive = false;

            const loadScript = (src) => new Promise((resolve, reject) => {
                const existingScript = document.querySelector(`script[src="${src}"]`);

                if (existingScript) {
                    existingScript.addEventListener('load', resolve, { once: true });
                    existingScript.addEventListener('error', reject, { once: true });

                    if (existingScript.dataset.loaded === 'true') {
                        resolve();
                    }

                    return;
                }

                const script = document.createElement('script');
                script.src = src;
                script.async = true;
                script.onload = () => {
                    script.dataset.loaded = 'true';
                    resolve();
                };
                script.onerror = reject;
                document.head.appendChild(script);
            });

            const setStatus = (message) => {
                if (cameraStatus) {
                    cameraStatus.textContent = message;
                }
            };

            const stopCamera = () => {
                if (scanFrameId) {
                    cancelAnimationFrame(scanFrameId);
                    scanFrameId = null;
                }

                if (mediaStream) {
                    mediaStream.getTracks().forEach((track) => track.stop());
                    mediaStream = null;
                }

                if (quaggaActive && window.Quagga) {
                    window.Quagga.offDetected(submitByQuaggaBarcode);
                    window.Quagga.stop();
                    quaggaActive = false;
                }

                if (cameraPreviewShell) {
                    cameraPreviewShell.classList.add('d-none');
                }

                if (cameraPreview) {
                    cameraPreview.pause();
                    cameraPreview.srcObject = null;
                    cameraPreview.classList.add('d-none');
                }

                if (cameraFallback) {
                    cameraFallback.classList.add('d-none');
                    cameraFallback.innerHTML = '';
                }

                cameraStartButton?.classList.remove('d-none');
                cameraStopButton?.classList.add('d-none');
                detector = null;
                isDetecting = false;
            };

            const submitByBarcode = (rawValue) => {
                if (!barcodeInput || !form || isSubmitting) {
                    return;
                }

                isSubmitting = true;
                barcodeInput.value = String(rawValue || '').toUpperCase().trim();
                setStatus(`Barcode terbaca: ${barcodeInput.value}. Mengirim check-in...`);
                stopCamera();
                form.submit();
            };

            const submitByQuaggaBarcode = (result) => {
                const rawValue = result?.codeResult?.code;

                if (rawValue) {
                    submitByBarcode(rawValue);
                }
            };

            const scanLoop = async () => {
                if (!cameraPreview || !detector || isSubmitting) {
                    return;
                }

                if (cameraPreview.readyState >= 2 && !isDetecting) {
                    isDetecting = true;

                    try {
                        const barcodes = await detector.detect(cameraPreview);
                        const firstBarcode = barcodes.find((barcode) => barcode.rawValue);

                        if (firstBarcode?.rawValue) {
                            submitByBarcode(firstBarcode.rawValue);
                            return;
                        }
                    } catch (error) {
                        setStatus('Kamera aktif, tetapi barcode belum terbaca. Coba dekatkan barcode ke kamera.');
                    } finally {
                        isDetecting = false;
                    }
                }

                scanFrameId = requestAnimationFrame(scanLoop);
            };

            const startQuaggaFallback = async () => {
                setStatus('Browser ini tidak mendukung scanner bawaan. Menyiapkan fallback scanner...');

                try {
                    await loadScript('https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.2/dist/quagga.min.js');
                } catch (error) {
                    setStatus('Fallback scanner gagal dimuat. Pastikan koneksi internet aktif lalu coba lagi.');
                    return;
                }

                if (!window.Quagga || !cameraFallback || !cameraPreviewShell) {
                    setStatus('Fallback scanner tidak tersedia di browser ini.');
                    return;
                }

                cameraPreviewShell.classList.remove('d-none');
                cameraPreview.classList.add('d-none');
                cameraFallback.classList.remove('d-none');
                cameraFallback.innerHTML = '';

                window.Quagga.init({
                    inputStream: {
                        type: 'LiveStream',
                        target: cameraFallback,
                        constraints: {
                            facingMode: 'environment',
                        },
                    },
                    decoder: {
                        readers: ['code_39_reader'],
                    },
                    locate: true,
                }, function (error) {
                    if (error) {
                        setStatus('Kamera fallback tidak bisa dibuka. Pastikan izin kamera diberikan di browser.');
                        return;
                    }

                    quaggaActive = true;
                    cameraStartButton?.classList.add('d-none');
                    cameraStopButton?.classList.remove('d-none');
                    window.Quagga.offDetected(submitByQuaggaBarcode);
                    window.Quagga.onDetected(submitByQuaggaBarcode);
                    window.Quagga.start();
                    setStatus('Kamera aktif dengan mode fallback. Arahkan barcode member ke kamera.');
                });
            };

            barcodeInput?.addEventListener('input', function () {
                this.value = this.value.toUpperCase().trimStart();
            });

            cameraStartButton?.addEventListener('click', async function () {
                if (!('BarcodeDetector' in window)) {
                    await startQuaggaFallback();
                    return;
                }

                try {
                    const supportedFormats = await window.BarcodeDetector.getSupportedFormats();

                    if (!supportedFormats.includes('code_39')) {
                        await startQuaggaFallback();
                        return;
                    }

                    detector = new window.BarcodeDetector({ formats: ['code_39'] });
                    mediaStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                        },
                        audio: false,
                    });

                    cameraPreviewShell.classList.remove('d-none');
                    cameraFallback?.classList.add('d-none');
                    cameraPreview.srcObject = mediaStream;
                    cameraPreview.classList.remove('d-none');
                    await cameraPreview.play();
                    cameraStartButton.classList.add('d-none');
                    cameraStopButton?.classList.remove('d-none');
                    setStatus('Kamera aktif. Arahkan barcode member ke kamera.');
                    scanLoop();
                } catch (error) {
                    stopCamera();
                    await startQuaggaFallback();
                }
            });

            cameraStopButton?.addEventListener('click', function () {
                stopCamera();
                setStatus('Kamera dimatikan.');
            });
        });
    </script>

    <div class="panel-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold mb-0">Daftar member check-in hari ini</h2>
            <div class="small muted-copy">{{ $checkinRecords->count() }} member check-in hari ini</div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Member</th>
                        <th>Kode Check-in</th>
                        <th>Jam Check-in</th>
                        <th>Sumber</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($checkinRecords as $record)
                        <tr>
                            <td>
                                @if ($record->member?->profile_photo_url)
                                    <img src="{{ $record->member->profile_photo_url }}" alt="Foto {{ $record->member->full_name }}" class="table-avatar">
                                @elseif ($record->member)
                                    <span class="table-avatar-placeholder">{{ $record->member->profile_initials }}</span>
                                @else
                                    <span class="table-avatar-placeholder">-</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $record->member?->full_name ?? '-' }}</td>
                            <td>{{ $record->member?->checkin_code ?? '-' }}</td>
                            <td>{{ $record->checked_in_at->format('H:i') }}</td>
                            <td>
                                <span class="badge {{ $record->checkin_method === 'cashier' ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                    {{ $record->checkin_method === 'cashier' ? 'Kasir' : 'Admin' }}
                                </span>
                            </td>
                            <td class="small muted-copy">{{ $record->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">Belum ada member yang check-in hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
