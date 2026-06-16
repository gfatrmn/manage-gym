# WhatsApp Gratis Otomatis

Project ini sudah siap mencoba kirim WhatsApp otomatis lewat gateway gratis yang di-host sendiri.

Gateway yang disiapkan di kode:
- `Evolution API`

Template lokal yang sudah saya siapkan:
- [docker-compose.whatsapp.yml](/c:/laragon/www/psi/docker-compose.whatsapp.yml)
- [.env.whatsapp.example](/c:/laragon/www/psi/.env.whatsapp.example)

Konfigurasi `.env`:

```env
WHATSAPP_DRIVER=evolution
WHATSAPP_BASE_URL=http://127.0.0.1:8080
WHATSAPP_INSTANCE=arena-gym
WHATSAPP_API_KEY=isi_api_key_gateway_anda
WHATSAPP_TIMEOUT=15
```

Yang perlu disiapkan di gateway:
1. Copy `.env.whatsapp.example` menjadi `.env.whatsapp`.
2. Jalankan gateway lokal:

```bash
docker compose -f docker-compose.whatsapp.yml --env-file .env.whatsapp up -d
```

3. Buka manager di `http://127.0.0.1:3000`.
4. Buat atau hubungkan instance dengan nama yang sama seperti `WHATSAPP_INSTANCE`.
5. Scan QR WhatsApp dari manager.
6. Isi `WHATSAPP_API_KEY` di `.env` Laravel sesuai `AUTHENTICATION_API_KEY` pada `.env.whatsapp`.

Alternatif tanpa compose:
1. Jalankan Evolution API di server/lokal.
2. Buat instance dengan nama yang sama seperti `WHATSAPP_INSTANCE`.
3. Hubungkan instance ke akun WhatsApp lewat scan QR.
4. Isi `WHATSAPP_API_KEY` dari gateway.

Sesudah itu:
1. Publish pengumuman dari admin.
2. Sistem akan mencoba kirim otomatis ke WhatsApp member.
3. Jika gagal, admin tetap dapat tombol `Buka WhatsApp` sebagai fallback manual.

Catatan:
- Nomor member harus tersimpan dalam format Indonesia yang valid.
- Sistem akan menormalisasi `08...` menjadi `628...`.
- Jika gateway belum aktif, aplikasi tidak error; hanya kembali ke mode manual.
