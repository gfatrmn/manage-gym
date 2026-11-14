# PRODUCT REQUIREMENTS DOCUMENT (PRD)

# Sistem Manajemen Member Gym

**Versi:** 1.0\
**Status:** Draft\
**Jenis aplikasi:** Web Application\
**Bahasa:** Indonesia\
**Zona waktu:** Asia/Jakarta\
**Role:** Admin, Kasir, Member

------------------------------------------------------------------------

## 1. Gambaran Umum

### 1.1 Latar Belakang

Pengelolaan operasional gym membutuhkan sistem yang dapat
mengintegrasikan pendataan member, pengelolaan paket membership,
transaksi pembayaran, pemantauan masa aktif keanggotaan, dan pencatatan
kunjungan. Pengelolaan secara manual atau menggunakan sistem terpisah
dapat menyulitkan pemantauan status keanggotaan, pencatatan transaksi,
dan penyusunan laporan.

Sistem Manajemen Member Gym dikembangkan sebagai aplikasi berbasis web
untuk mengintegrasikan proses tersebut dalam satu platform sehingga
operasional gym lebih terstruktur, efisien, dan mudah dipantau.

### 1.2 Tujuan

1.  Mempermudah pengelolaan data member.
2.  Mengelola paket dan harga membership.
3.  Mengelola pendaftaran dan perpanjangan membership.
4.  Memantau masa aktif dan status keanggotaan.
5.  Mencatat kunjungan menggunakan QR Code.
6.  Menyediakan pencatatan transaksi dan laporan keuangan.
7.  Memberikan akses mandiri kepada member untuk melihat informasi
    keanggotaannya.
8.  Membantu admin memantau operasional gym.

### 1.3 Ruang Lingkup

Sistem memiliki tiga role utama:

-   **Admin:** Mengelola data, pengguna, paket, membership, transaksi,
    pengeluaran, pengaturan, dan laporan.
-   **Kasir:** Melayani pendaftaran member, transaksi pembayaran,
    perpanjangan membership, dan absensi.
-   **Member:** Melihat profil, status membership, kartu digital,
    riwayat pembayaran, dan riwayat kunjungan.

### 1.4 Di Luar Ruang Lingkup Versi Pertama

-   Monitoring alat gym berbasis IoT.
-   Aplikasi mobile native.
-   Payroll karyawan.
-   Akuntansi lengkap.
-   Booking personal trainer dan kelas.
-   Integrasi wearable device.
-   Multi-cabang.
-   Pengelolaan inventaris alat secara mendalam.

------------------------------------------------------------------------

## 2. Role dan Hak Akses

Sistem menerapkan Role-Based Access Control (RBAC). Setiap pengguna
hanya dapat mengakses fitur sesuai role dan izin yang diberikan.

### 2.1 Admin

Admin memiliki hak akses pengelolaan sistem secara menyeluruh.

Hak akses: 1. Melihat dashboard statistik gym. 2. Mengelola data member
(tambah, lihat, ubah, dan menonaktifkan). 3. Mengelola akun kasir. 4.
Mengelola paket membership. 5. Melihat dan memantau seluruh membership.
6. Melihat seluruh transaksi dan pembayaran. 7. Melakukan koreksi data
melalui prosedur yang tercatat. 8. Melihat dan mengelola data absensi.
9. Mengelola kategori dan pengeluaran operasional. 10. Melihat laporan
member, transaksi, pendapatan, pengeluaran, dan absensi. 11. Mengelola
profil dan konfigurasi gym. 12. Melihat activity log. 13. Mengatur
notifikasi dan konfigurasi invoice.

Batasan dan pengendalian: - Perubahan transaksi yang sudah dibayar harus
melalui prosedur koreksi atau refund. - Perubahan status membership
harus mencatat alasan dan pengguna yang melakukan perubahan. - Data
transaksi dan riwayat membership tidak boleh dihapus permanen melalui
operasi biasa.

### 2.2 Kasir

Kasir bertanggung jawab terhadap pelayanan dan transaksi harian.

Hak akses: 1. Melihat dashboard kasir. 2. Mendaftarkan member baru. 3.
Melihat data dan detail member. 4. Mengubah data member yang diizinkan.
5. Melihat paket membership aktif. 6. Membuat transaksi pendaftaran
membership. 7. Membuat transaksi perpanjangan membership. 8. Memproses
pembayaran tunai. 9. Mencatat pembayaran transfer atau QRIS sesuai
prosedur verifikasi. 10. Mencetak atau mengunduh invoice. 11. Memindai
QR Code member. 12. Memvalidasi absensi. 13. Melihat riwayat transaksi
yang diproses sendiri.

Batasan: - Tidak dapat mengubah harga paket. - Tidak dapat mengelola
akun admin atau role. - Tidak dapat mengakses pengaturan sistem. - Tidak
dapat menghapus transaksi. - Tidak dapat mengubah pembayaran yang sudah
berhasil tanpa prosedur koreksi. - Akses laporan dibatasi pada transaksi
sendiri, kecuali admin memberikan izin tambahan.

### 2.3 Member

Member adalah pengguna yang terdaftar sebagai anggota gym.

Hak akses: 1. Login ke akun pribadi. 2. Melihat dashboard pribadi. 3.
Melihat dan memperbarui informasi profil yang diizinkan. 4. Melihat
status dan detail membership. 5. Melihat paket membership yang tersedia.
6. Melihat riwayat pembayaran dan perpanjangan. 7. Melihat serta
mengunduh invoice miliknya. 8. Menampilkan kartu member digital dan QR
Code. 9. Melihat riwayat kunjungan. 10. Melihat notifikasi masa aktif.
11. Mengubah password.

Batasan: - Tidak dapat mengubah harga, status pembayaran, atau status
membership. - Tidak dapat melihat data member lain. - Tidak dapat
membuat absensi untuk member lain.

------------------------------------------------------------------------

## 3. Modul dan Kebutuhan Fungsional

### 3.1 Authentication dan Authorization

Fitur: - Login menggunakan email atau username dan password. - Logout. -
Validasi kredensial. - Pengalihan ke dashboard sesuai role. - Proteksi
halaman dan endpoint berdasarkan role. - Hashing password. - Manajemen
sesi. - Lupa dan reset password. - Penonaktifan akun.

Kriteria: - Pengguna yang tidak login tidak dapat membuka halaman
internal. - Pengguna tidak dapat mengakses fitur role lain dengan
mengganti URL. - Akun nonaktif tidak dapat login. - Password tidak
pernah disimpan dalam bentuk teks biasa.

### 3.2 Dashboard Admin

Informasi yang ditampilkan: - Total member terdaftar. - Jumlah member
aktif. - Jumlah member expired. - Member yang akan expired dalam tujuh
hari. - Pendaftaran member baru. - Pendapatan hari ini dan bulan
berjalan. - Jumlah kunjungan. - Grafik pendapatan berdasarkan periode. -
Grafik pertumbuhan member. - Transaksi terbaru. - Member yang baru
mendaftar. - Paket membership populer. - Aktivitas terbaru.

Seluruh angka harus dihitung dari data aktual dan dapat difilter
berdasarkan periode bila relevan.

### 3.3 Dashboard Kasir

Informasi: - Jumlah transaksi hari ini. - Total nilai transaksi yang
diproses kasir hari ini. - Jumlah pendaftaran baru. - Jumlah
perpanjangan. - Member yang segera expired. - Daftar transaksi
terbaru. - Akses cepat pendaftaran, transaksi, dan scan QR.

### 3.4 Dashboard Member

Informasi: - Nama dan foto member. - Member ID. - Status membership. -
Paket yang digunakan. - Tanggal mulai dan berakhir. - Sisa masa aktif. -
QR Code kartu member. - Riwayat pembayaran terbaru. - Riwayat kunjungan
terbaru.

Data yang ditampilkan harus selalu terbatas pada member yang sedang
login.

### 3.5 Manajemen Member

Fitur: - Tambah, lihat, ubah, dan cari member. - Filter berdasarkan
status dan tanggal pendaftaran. - Unggah atau ubah foto profil. - Lihat
detail member. - Lihat riwayat membership. - Lihat riwayat transaksi. -
Lihat riwayat absensi. - Aktifkan atau nonaktifkan akun/member.

Data member: - Member ID unik dan otomatis. - Nama lengkap. - Foto
profil (opsional). - Jenis kelamin. - Tanggal lahir. - Nomor telepon. -
Email. - Alamat. - Tanggal pendaftaran. - Status member. - Akun pengguna
terkait.

Aturan: - Satu member memiliki satu akun. - Member ID harus unik. - Data
member yang sudah memiliki transaksi tidak boleh dihapus permanen. -
Member yang tidak berlangganan dapat tetap disimpan dan dinonaktifkan. -
Email dan nomor telepon divalidasi sesuai kebijakan sistem.

### 3.6 Manajemen Paket Membership

Fitur: - Tambah, lihat, ubah, dan nonaktifkan paket. - Atur nama,
deskripsi, durasi, harga, dan fasilitas. - Tampilkan paket yang tersedia
kepada kasir dan member.

Data paket: - ID paket. - Nama paket. - Deskripsi. - Durasi dalam
hari. - Harga. - Fasilitas. - Status aktif/nonaktif. - Waktu dibuat dan
diperbarui.

Contoh paket (harga dapat diubah admin):

  Paket          Durasi   Contoh Harga
  ---------- ---------- --------------
  Harian         1 hari       Rp25.000
  Mingguan       7 hari      Rp100.000
  Bulanan       30 hari      Rp250.000
  3 Bulan       90 hari      Rp650.000
  6 Bulan      180 hari    Rp1.200.000
  Tahunan      365 hari    Rp2.200.000

Aturan: - Paket nonaktif tidak dapat dipilih untuk transaksi baru. -
Paket yang telah digunakan tidak boleh dihapus permanen. - Perubahan
harga paket tidak mengubah transaksi sebelumnya. - Harga dan nama paket
disimpan sebagai snapshot pada detail transaksi.

### 3.7 Manajemen Membership

Setiap pembelian paket menghasilkan catatan membership tersendiri.

Data membership: - Membership ID. - Member. - Paket. - Transaksi
terkait. - Tanggal mulai. - Tanggal berakhir. - Harga saat pembelian. -
Status. - Catatan/alasan perubahan status bila ada.

Status: - `pending`: menunggu pembayaran. - `active`: sedang berlaku. -
`expired`: masa aktif telah berakhir. - `suspended`: ditangguhkan
sementara oleh admin. - `cancelled`: dibatalkan melalui prosedur yang
sah.

Aturan: 1. Membership aktif setelah pembayaran berhasil. 2. Membership
memiliki tanggal mulai dan tanggal berakhir. 3. Riwayat membership tidak
ditimpa atau dihapus saat perpanjangan. 4. Satu member dapat memiliki
banyak catatan membership sepanjang waktu. 5. Membership baru tidak
boleh menghilangkan sisa masa aktif yang masih berlaku. 6. Membership
suspended tidak dapat digunakan untuk absensi. 7. Perubahan status
manual harus mencatat alasan, pelaku, dan waktu.

Aturan perpanjangan: - Jika member masih memiliki membership aktif,
tanggal mulai membership baru secara default adalah satu hari setelah
tanggal akhir membership sebelumnya. - Jika membership sudah expired
atau belum pernah ada, tanggal mulai adalah tanggal aktivasi setelah
pembayaran berhasil. - Tanggal akhir dihitung berdasarkan durasi paket
dengan perhitungan hari kalender inklusif. - Sistem harus mencegah dua
proses perpanjangan yang sama menghasilkan membership ganda.

Contoh: - Membership lama berakhir 30 November 2026. - Member membeli
paket 30 hari pada 25 November 2026. - Membership baru dimulai 1
Desember 2026 dan berakhir 30 Desember 2026.

Jika member expired dan membayar pada 5 Desember 2026 untuk paket 30
hari, periode baru dimulai 5 Desember dan berakhir 3 Januari 2027.

### 3.8 Transaksi dan Pembayaran

Jenis transaksi versi pertama: 1. Pendaftaran membership baru. 2.
Perpanjangan membership.

Fitur: - Pilih member. - Pilih paket. - Tampilkan harga dan durasi. -
Masukkan diskon jika diizinkan. - Hitung subtotal dan total. - Pilih
metode pembayaran. - Catat pembayaran. - Hitung kembalian untuk
pembayaran tunai. - Verifikasi pembayaran non-tunai. - Buat invoice. -
Lihat riwayat transaksi.

Metode pembayaran: - Cash. - Transfer bank. - QRIS. - E-wallet. - Metode
lain yang diaktifkan admin.

Status transaksi: - `pending`. - `paid`. - `failed`. - `cancelled`. -
`refunded` (jika refund telah diproses).

Aturan: - Nomor invoice harus unik. - Pembayaran tunai dapat
dikonfirmasi kasir setelah uang diterima. - Transfer/QRIS manual harus
diverifikasi sebelum membership diaktifkan. - Pembayaran gagal atau
pending tidak boleh mengaktifkan membership. - Transaksi paid tidak
dapat diedit atau dihapus secara langsung. - Koreksi dan refund harus
memiliki alasan, pelaku, waktu, dan jejak audit. - Harga transaksi
menggunakan snapshot agar tidak berubah saat harga paket diperbarui. -
Proses pembayaran dan aktivasi membership harus atomik menggunakan
database transaction. - Sistem harus mencegah pembayaran atau callback
yang sama diproses dua kali.

Invoice memuat: - Nomor invoice. - Identitas gym. - Identitas member. -
Tanggal transaksi. - Detail paket. - Harga. - Diskon. - Total. - Metode
dan status pembayaran. - Kasir yang memproses. - Periode membership yang
dihasilkan.

### 3.9 QR Code dan Absensi

Fitur: - Generate QR Code unik untuk setiap member. - Tampilkan QR Code
di kartu digital. - Scan QR melalui kamera perangkat kasir. - Validasi
token dan status membership. - Simpan waktu check-in. - Tampilkan hasil
validasi. - Lihat riwayat absensi. - Cegah absensi ganda dalam sesi
kunjungan yang sama.

Alur: 1. Member menunjukkan QR Code. 2. Kasir membuka halaman scanner.
3. Sistem membaca token QR. 4. Sistem memvalidasi token dan identitas
member. 5. Sistem memeriksa status akun dan membership. 6. Jika valid,
sistem mencatat waktu check-in. 7. Sistem menampilkan hasil berhasil
atau alasan penolakan.

Validasi: - QR tidak dikenal: ditolak. - Member tidak ditemukan:
ditolak. - Akun nonaktif: ditolak. - Membership pending, expired,
suspended, atau cancelled: ditolak. - QR valid dan membership aktif:
absensi diterima. - Check-in berulang pada sesi yang sama: ditolak atau
ditangani sesuai aturan sesi.

Data absensi: - Attendance ID. - Member ID. - Waktu check-in. - Waktu
check-out (opsional). - Petugas pemindai. - Status. - Catatan bila
diperlukan.

Versi pertama cukup mencatat check-in. Check-out dapat ditambahkan jika
gym membutuhkan pengukuran durasi kunjungan.

QR Code harus menggunakan token acak atau identifier yang tidak mudah
ditebak. Jangan menaruh informasi pribadi sensitif secara langsung di
dalam QR.

### 3.10 Notifikasi Membership

Jenis notifikasi: - Membership berhasil diaktifkan. - Pengingat tujuh
hari sebelum berakhir. - Pengingat tiga hari sebelum berakhir. -
Pengingat satu hari sebelum berakhir. - Membership telah expired. -
Pembayaran berhasil.

Kanal: - Notifikasi dalam aplikasi sebagai fitur dasar. - WhatsApp
Gateway dan email sebagai pengembangan opsional.

Sistem harus mencegah pengiriman notifikasi pengingat yang sama berulang
kali untuk periode yang sama.

### 3.11 Riwayat Transaksi Member

Member dapat melihat transaksi miliknya dengan informasi: - Nomor
invoice. - Nama paket. - Tanggal transaksi. - Nominal. - Metode
pembayaran. - Status pembayaran. - Periode membership.

Member dapat membuka dan mengunduh invoice miliknya sendiri.

### 3.12 Pengeluaran Operasional

Admin dapat mencatat pengeluaran gym.

Contoh kategori: - Listrik. - Air. - Perawatan alat. - Perlengkapan. -
Kebersihan. - Gaji. - Operasional lainnya.

Data pengeluaran: - Nomor pengeluaran. - Kategori. - Tanggal. -
Deskripsi. - Nominal. - Bukti pengeluaran (opsional). - Pengguna
pencatat.

Fitur: - Tambah, lihat, ubah, dan koreksi pengeluaran. - Filter
berdasarkan kategori dan periode. - Lihat total pengeluaran.

Kasir tidak memiliki akses pengelolaan pengeluaran secara default.

### 3.13 Laporan

Laporan admin: 1. Seluruh member. 2. Member aktif. 3. Member expired. 4.
Pendaftaran member baru. 5. Perpanjangan membership. 6. Transaksi
pembayaran. 7. Pendapatan. 8. Pengeluaran. 9. Selisih pendapatan dan
pengeluaran tercatat. 10. Absensi member.

Fitur: - Filter tanggal, bulan, dan tahun. - Filter status dan metode
pembayaran. - Pagination untuk data besar. - Export PDF. - Export
Excel. - Cetak laporan.

Rumus: - Total pendapatan = jumlah transaksi berstatus paid yang belum
direversal/refund. - Total pengeluaran = jumlah pengeluaran pada periode
yang dipilih. - Selisih operasional tercatat = total pendapatan
dikurangi total pengeluaran.

Selisih tersebut bukan laporan laba akuntansi lengkap.

### 3.14 Pengaturan Sistem

Admin dapat mengelola: - Nama dan logo gym. - Alamat dan nomor
telepon. - Informasi yang tampil pada invoice. - Format nomor invoice. -
Pengaturan notifikasi. - Zona waktu. - Akun kasir. - Status akun. -
Pengaturan operasional yang diperlukan.

### 3.15 Activity Log

Sistem mencatat aktivitas penting: - Penambahan dan perubahan member. -
Perubahan paket. - Pembuatan transaksi. - Konfirmasi pembayaran. -
Refund atau koreksi. - Perubahan status membership. - Absensi. -
Perubahan pengaturan.

Data log: - Pengguna. - Jenis aktivitas. - Entitas dan ID data
terkait. - Ringkasan perubahan. - Waktu. - IP address bila diperlukan.

Log penting tidak dapat diubah oleh kasir atau member. Admin dapat
melihat log.

------------------------------------------------------------------------

## 4. Alur Bisnis Utama

### 4.1 Pendaftaran Member Baru

1.  Member datang atau menghubungi gym.
2.  Kasir membuka form pendaftaran.
3.  Kasir mengisi data member.
4.  Kasir memilih paket.
5.  Sistem membuat transaksi berstatus pending.
6.  Member melakukan pembayaran.
7.  Kasir memvalidasi pembayaran.
8.  Sistem mengubah status transaksi menjadi paid.
9.  Sistem membuat membership dan mengaktifkannya.
10. Sistem membuat Member ID dan QR Code.
11. Member memperoleh akses akun dan invoice.

Jika pembayaran belum berhasil, membership tetap pending dan tidak dapat
digunakan untuk absensi.

### 4.2 Perpanjangan Membership

1.  Kasir mencari member.
2.  Sistem menampilkan membership saat ini dan riwayatnya.
3.  Kasir memilih paket baru.
4.  Sistem menentukan tanggal mulai sesuai aturan perpanjangan.
5.  Sistem membuat transaksi.
6.  Member membayar.
7.  Pembayaran diverifikasi.
8.  Sistem membuat catatan membership baru.
9.  Sistem menerbitkan invoice.

### 4.3 Absensi Member

1.  Member membuka kartu digital.
2.  Member menunjukkan QR Code.
3.  Kasir memindai QR Code.
4.  Sistem memvalidasi token, akun, dan membership.
5.  Jika valid, sistem menyimpan check-in.
6.  Sistem menampilkan konfirmasi.
7.  Jika tidak valid, sistem menampilkan alasan penolakan.

### 4.4 Membership Expired

1.  Scheduler menjalankan pemeriksaan status membership.
2.  Sistem membandingkan tanggal saat ini dengan tanggal akhir.
3.  Membership yang telah melewati tanggal akhir diubah menjadi expired.
4.  Sistem membuat notifikasi sesuai konfigurasi.
5.  Member expired tidak dapat melakukan absensi.
6.  Member dapat membeli membership baru.

------------------------------------------------------------------------

## 5. Struktur Database

Struktur berikut merupakan rancangan awal. Tipe data, constraint, index,
dan nama kolom final harus disesuaikan saat implementasi migration.

### 5.1 Daftar Tabel

  Tabel                   Fungsi
  ----------------------- -----------------------------
  `roles`                 Master role
  `users`                 Akun login seluruh role
  `members`               Profil member
  `membership_packages`   Paket membership
  `memberships`           Riwayat periode keanggotaan
  `transactions`          Header transaksi
  `transaction_items`     Detail item transaksi
  `payments`              Catatan pembayaran
  `attendances`           Riwayat kunjungan
  `expense_categories`    Kategori pengeluaran
  `expenses`              Pengeluaran gym
  `notifications`         Notifikasi pengguna
  `activity_logs`         Audit aktivitas
  `gym_settings`          Konfigurasi gym

### 5.2 Kolom Utama

**roles** - `id` - `name` - `description`

**users** - `id` - `role_id` - `name` - `email` - `password` -
`is_active` - `email_verified_at` - `created_at` - `updated_at`

**members** - `id` - `user_id` - `member_code` - `gender` -
`birth_date` - `phone` - `address` - `photo` - `registered_at` -
`status` - timestamps

**membership_packages** - `id` - `name` - `description` -
`duration_days` - `price` - `facilities` - `is_active` - timestamps

**memberships** - `id` - `member_id` - `package_id` - `transaction_id` -
`start_date` - `end_date` - `price` - `status` - `status_reason` -
timestamps

**transactions** - `id` - `invoice_number` - `member_id` -
`cashier_id` - `transaction_date` - `subtotal` - `discount` - `total` -
`status` - timestamps

**transaction_items** - `id` - `transaction_id` - `package_id` (nullable
bila paket dihapus secara logis) - `item_name` - `quantity` -
`unit_price` - `subtotal`

**payments** - `id` - `transaction_id` - `payment_method` - `amount` -
`paid_at` - `payment_status` - `reference_number` - timestamps

**attendances** - `id` - `member_id` - `check_in` - `check_out` -
`scanned_by` - `status` - timestamps

**expense_categories** - `id` - `name` - `description`

**expenses** - `id` - `category_id` - `user_id` - `description` -
`amount` - `expense_date` - `attachment` - timestamps

**notifications** - `id` - `user_id` - `type` - `title` - `message` -
`read_at` - timestamps

**activity_logs** - `id` - `user_id` - `action` - `entity_type` -
`entity_id` - `old_values` - `new_values` - `ip_address` - `created_at`

**gym_settings** - `id` - `key` - `value` - timestamps

### 5.3 Relasi

-   Satu role memiliki banyak user.
-   Satu user dapat memiliki satu profil member.
-   Satu member memiliki banyak membership.
-   Satu paket dapat digunakan pada banyak membership.
-   Satu member memiliki banyak transaksi.
-   Satu kasir dapat memproses banyak transaksi.
-   Satu transaksi memiliki satu atau lebih transaction item.
-   Satu transaksi dapat memiliki catatan pembayaran.
-   Satu transaksi dapat menghasilkan membership.
-   Satu member memiliki banyak attendance.
-   Satu user/kasir dapat memvalidasi banyak attendance.
-   Satu kategori pengeluaran memiliki banyak expense.
-   Satu user dapat mencatat banyak expense.
-   Satu user dapat menerima banyak notifikasi.

Gunakan foreign key, index pada kolom pencarian/filter, unique
constraint untuk Member ID dan invoice, serta transaksi database untuk
proses yang harus konsisten.

------------------------------------------------------------------------

## 6. Business Rules

  -----------------------------------------------------------------------
  ID                                  Aturan
  ----------------------------------- -----------------------------------
  BR-001                              Setiap member memiliki Member ID
                                      unik.

  BR-002                              Satu member memiliki satu akun.

  BR-003                              Paket memiliki durasi dan harga.

  BR-004                              Membership aktif hanya setelah
                                      pembayaran berhasil.

  BR-005                              Setiap membership memiliki tanggal
                                      mulai dan akhir.

  BR-006                              Membership menjadi expired setelah
                                      melewati tanggal akhir.

  BR-007                              Member expired tidak dapat
                                      melakukan absensi.

  BR-008                              Member suspended tidak dapat
                                      melakukan absensi.

  BR-009                              Setiap transaksi memiliki invoice
                                      unik.

  BR-010                              Transaksi paid tidak dapat diubah
                                      sembarangan.

  BR-011                              Perpanjangan tidak menghilangkan
                                      sisa masa aktif.

  BR-012                              Paket nonaktif tidak dapat dipilih
                                      untuk transaksi baru.

  BR-013                              Absensi hanya dapat dilakukan oleh
                                      member valid.

  BR-014                              Member hanya dapat melihat data
                                      miliknya.

  BR-015                              Kasir tidak dapat mengubah harga
                                      paket.

  BR-016                              Aktivitas penting dicatat dalam
                                      activity log.

  BR-017                              Pembayaran yang sama tidak boleh
                                      mengaktifkan membership dua kali.

  BR-018                              Refund memerlukan otorisasi dan
                                      catatan.
  -----------------------------------------------------------------------

### 6.1 Aturan Tanggal dan Status

-   Gunakan zona waktu `Asia/Jakarta`.
-   Perhitungan durasi menggunakan hari kalender.
-   Tanggal akhir bersifat inklusif.
-   Membership berakhir pada akhir hari tanggal akhir sesuai zona waktu
    gym.
-   Scheduler harus memperbarui status expired secara berkala.
-   Validasi absensi juga harus memeriksa tanggal secara langsung, tidak
    hanya mengandalkan scheduler.
-   Status suspended atau cancelled harus mengalahkan status aktif
    secara operasional.
-   Simpan timestamp dengan pendekatan yang konsisten; konversikan ke
    zona waktu tampilan gym.

------------------------------------------------------------------------

## 7. Struktur Menu

### 7.1 Menu Admin

**Dashboard** - Ringkasan statistik - Grafik pendapatan - Grafik
pertumbuhan member - Transaksi terbaru - Member segera expired

**Master Data** - Data Member - Paket Membership - Data Kasir

**Operasional** - Membership - Transaksi - Pembayaran - Absensi

**Keuangan** - Kategori Pengeluaran - Pengeluaran - Laporan Keuangan

**Laporan** - Laporan Member - Laporan Membership - Laporan Transaksi -
Laporan Absensi

**Pengaturan** - Profil Gym - Pengaturan Sistem - Activity Log - Akun
Saya

### 7.2 Menu Kasir

-   Dashboard
-   Pendaftaran Member
-   Data Member
-   Paket Membership (lihat)
-   Perpanjangan Membership
-   Transaksi Baru
-   Riwayat Transaksi
-   Invoice
-   Scan QR Member
-   Riwayat Absensi
-   Profil Saya

### 7.3 Menu Member

-   Dashboard
-   Membership Saya
-   Kartu Member Digital
-   Paket Membership
-   Riwayat Pembayaran
-   Invoice
-   Riwayat Kunjungan
-   Profil Saya
-   Ubah Password

------------------------------------------------------------------------

## 8. Non-Functional Requirements

### 8.1 Usability

-   Antarmuka sederhana, konsisten, dan mudah dipahami.
-   Navigasi disesuaikan dengan role.
-   Form memiliki validasi dan pesan kesalahan yang jelas.
-   Proses transaksi dan scan QR dibuat sesingkat mungkin.

### 8.2 Performance

-   Gunakan pagination untuk daftar data.
-   Optimalkan query dan index database.
-   Hindari memuat seluruh transaksi sekaligus.
-   Gunakan eager loading untuk menghindari N+1 query.
-   Proses scan QR harus memberikan respons cepat pada jaringan normal.

### 8.3 Security

-   Autentikasi dan authorization berdasarkan role.
-   Password di-hash.
-   Validasi input di backend.
-   Proteksi CSRF, XSS, dan SQL Injection.
-   Batasi akses data berdasarkan kepemilikan.
-   QR Code memakai token acak yang tidak mudah ditebak.
-   Terapkan rate limiting pada login dan endpoint sensitif.
-   Simpan audit log untuk aktivitas penting.
-   Jangan menyimpan data kartu pembayaran.

### 8.4 Reliability

-   Proses pembayaran dan aktivasi membership harus konsisten.
-   Gunakan database transaction.
-   Terapkan idempotency untuk callback atau konfirmasi pembayaran.
-   Sediakan backup database berkala.
-   Tangani error tanpa membocorkan informasi sensitif.

### 8.5 Compatibility

-   Mendukung browser modern seperti Chrome dan Edge.
-   Tampilan responsif pada desktop, tablet, dan smartphone.
-   Scanner QR mendukung perangkat dengan kamera dan izin browser yang
    sesuai.

### 8.6 Maintainability

-   Struktur kode modular.
-   Pisahkan controller, service, model, policy, dan komponen UI.
-   Gunakan migration dan seeder.
-   Terapkan standar penamaan dan dokumentasi.
-   Sediakan automated test untuk aturan bisnis penting.

------------------------------------------------------------------------

## 9. Rekomendasi Teknologi

  Bagian              Teknologi
  ------------------- ----------------------------------------------------
  Backend             Laravel
  Frontend            Laravel Blade
  CSS                 Tailwind CSS
  Interaktivitas      Alpine.js
  Database            MySQL
  Authentication      Laravel Breeze atau starter kit Laravel
  Authorization       Spatie Laravel Permission atau policy/gate Laravel
  QR Code Generator   Paket QR Code Laravel yang terpelihara
  QR Scanner          HTML5 QR Code atau ZXing
  Export Excel        Laravel Excel
  Export PDF          DomPDF
  Scheduler           Laravel Task Scheduling
  Queue               Laravel Queue
  Web Server          Nginx atau Apache
  OS Server           Ubuntu Server

Arsitektur versi pertama menggunakan satu aplikasi Laravel dan satu
database MySQL. Admin, kasir, dan member mengakses aplikasi yang sama
dengan dashboard serta hak akses berbeda. API terpisah dan aplikasi
mobile native belum diperlukan.

------------------------------------------------------------------------

## 10. Prioritas Pengembangan

### 10.1 MVP --- Versi Pertama

  Fitur                      Admin             Kasir            Member
  ---------------------- -------------- ------------------- ---------------
  Login dan hak akses          Ya               Ya                Ya
  Dashboard                    Ya               Ya                Ya
  Manajemen member             Ya            Terbatas            Tidak
  Paket membership           Kelola            Lihat             Lihat
  Pendaftaran member           Ya               Ya               Tidak
  Membership                 Kelola           Proses             Lihat
  Transaksi pembayaran    Lihat/kelola        Proses             Tidak
  Perpanjangan                 Ya               Ya               Tidak
  Invoice                    Semua       Transaksi sendiri   Milik sendiri
  QR Code                    Lihat           Validasi          Tampilkan
  Scan absensi                 Ya               Ya               Tidak
  Riwayat absensi            Semua          Operasional      Milik sendiri
  Notifikasi expired         Pantau            Tidak            Terima
  Laporan                    Semua           Terbatas            Tidak
  Activity log                 Ya              Tidak             Tidak

### 10.2 Pengembangan Lanjutan

-   WhatsApp Gateway.
-   Payment Gateway.
-   Notifikasi email.
-   Diskon dan promo.
-   Referral member.
-   Pembelian merchandise.
-   Pengelolaan personal trainer.
-   Booking kelas gym.
-   Jadwal latihan.
-   Pengelolaan alat gym.
-   Multi-cabang.
-   Membership freeze.
-   Analisis pertumbuhan dan retensi member.

------------------------------------------------------------------------

## 11. Acceptance Criteria dan Testing

  -----------------------------------------------------------------------
  ID                      Skenario                Hasil yang Diharapkan
  ----------------------- ----------------------- -----------------------
  TC-001                  Login admin             Masuk dashboard admin

  TC-002                  Login kasir             Masuk dashboard kasir

  TC-003                  Login member            Masuk dashboard member

  TC-004                  Kasir membuka           Akses ditolak
                          pengaturan admin        

  TC-005                  Admin menambah member   Data tersimpan dan
                                                  Member ID unik

  TC-006                  Admin membuat paket     Paket tersimpan

  TC-007                  Kasir membuat transaksi Invoice dan transaksi
                                                  tercatat

  TC-008                  Pembayaran berhasil     Membership
                                                  dibuat/diaktifkan

  TC-009                  Pembayaran gagal        Membership tidak aktif

  TC-010                  Perpanjangan saat masih Sisa masa aktif tidak
                          aktif                   hilang

  TC-011                  Perpanjangan saat       Periode baru dimulai
                          expired                 sesuai aturan

  TC-012                  QR valid dan membership Absensi berhasil
                          aktif                   

  TC-013                  QR tidak valid          Absensi ditolak

  TC-014                  Member expired          Absensi ditolak
                          melakukan absensi       

  TC-015                  Member membuka invoice  Akses ditolak
                          orang lain              

  TC-016                  Admin melihat laporan   Data sesuai filter
                          periode                 

  TC-017                  Scheduler memproses     Status expired
                          membership              diperbarui

  TC-018                  Callback pembayaran     Tidak terjadi aktivasi
                          dikirim ulang           ganda
  -----------------------------------------------------------------------

Sistem dinyatakan siap digunakan jika fungsi utama berjalan sesuai
kebutuhan, hak akses antar-role terjaga, dan transaksi serta membership
tersimpan konsisten.

------------------------------------------------------------------------

## 12. Tahapan Pengembangan

### Tahap 1 --- Setup Project dan Database

-   Instalasi Laravel.
-   Konfigurasi MySQL.
-   Setup Tailwind CSS.
-   Setup autentikasi.
-   Setup role dan permission.
-   Migration dan seeder.

### Tahap 2 --- Layout dan Dashboard

-   Layout admin.
-   Layout kasir.
-   Layout member.
-   Sidebar dan navigasi.
-   Dashboard setiap role.

### Tahap 3 --- Manajemen Data

-   CRUD member.
-   CRUD paket membership.
-   Manajemen akun kasir.
-   Pencarian, filter, dan pagination.

### Tahap 4 --- Membership dan Transaksi

-   Pendaftaran member.
-   Transaksi membership.
-   Pembayaran.
-   Invoice.
-   Perpanjangan.
-   Status membership.

### Tahap 5 --- QR Code dan Absensi

-   Generate QR Code.
-   Kartu member digital.
-   Scanner QR.
-   Validasi membership.
-   Riwayat kunjungan.

### Tahap 6 --- Member Portal

-   Dashboard member.
-   Profil.
-   Kartu digital.
-   Riwayat transaksi.
-   Riwayat absensi.
-   Informasi membership.

### Tahap 7 --- Laporan dan Notifikasi

-   Laporan pendapatan.
-   Laporan pengeluaran.
-   Laporan member.
-   Laporan absensi.
-   Notifikasi masa aktif.
-   Export PDF dan Excel.

### Tahap 8 --- Testing dan Deployment

-   Functional testing.
-   Authorization testing.
-   Security testing.
-   Performance testing.
-   Deployment.
-   Backup database.

------------------------------------------------------------------------

## 13. Keputusan Produk

  -----------------------------------------------------------------------
  Aspek                               Keputusan
  ----------------------------------- -----------------------------------
  Akun member                         Satu member memiliki satu akun

  Member ID                           Otomatis dan unik

  Membership                          Setiap pembelian membuat riwayat
                                      baru

  Perpanjangan                        Menambah periode setelah tanggal
                                      akhir aktif

  Pembayaran                          Membership aktif setelah pembayaran
                                      berhasil

  Transaksi                           Tidak dihapus sembarangan

  Absensi                             Menggunakan QR Code

  Scanner                             Kamera perangkat kasir

  Expired                             Diperbarui otomatis oleh scheduler
                                      dan divalidasi saat absensi

  Pengingat                           7, 3, dan 1 hari sebelum expired

  Kartu member                        Digital

  Kasir                               Tidak dapat mengubah harga paket

  Laporan                             Dikelola admin

  Arsitektur                          Satu aplikasi dan satu database
  -----------------------------------------------------------------------

------------------------------------------------------------------------

## 14. Kesimpulan

Sistem Manajemen Member Gym merupakan aplikasi berbasis web yang
mengintegrasikan pengelolaan anggota, paket membership, transaksi
pembayaran, absensi, dan laporan operasional dalam satu platform.

Tiga role memiliki tanggung jawab berbeda:

-   **Admin:** Mengontrol operasional, mengelola data dan paket, serta
    memantau laporan.
-   **Kasir:** Melayani pendaftaran, pembayaran, perpanjangan
    membership, dan absensi.
-   **Member:** Mengakses profil, kartu digital, status membership,
    invoice, dan riwayat kunjungan.

Pengembangan harus memprioritaskan lima modul inti: manajemen member,
paket membership, transaksi pembayaran, pengelolaan masa aktif, dan
absensi QR Code. Fitur lanjutan dapat ditambahkan setelah MVP berjalan
stabil.
