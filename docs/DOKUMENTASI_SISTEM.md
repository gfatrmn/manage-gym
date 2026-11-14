# Dokumentasi Lengkap Sistem Manajemen Arena Gym & Fitness

Dokumen ini disusun sebagai dokumentasi teknis dan perancangan perangkat lunak untuk sistem informasi **Arena Gym & Fitness**, mencakup **Arsitektur Sistem**, **Design System**, **Entity Relationship Diagram (ERD)**, serta **Unified Modeling Language (UML)** yang mencerminkan implementasi kode aktual berbasis **Laravel 12 + Inertia.js + React + Tailwind CSS v4**.

---

## 1. Arsitektur Sistem & Pemisahan Tanggung Jawab (Clean MVC)

Sistem dibangun menggunakan pola **Inertia.js Monolith-SPA**: Laravel bertindak sebagai pengelola routing, otentikasi/otorisasi berbasis peran (`EnsureRole`), validasi, dan transaksi basis data; sementara antarmuka pengguna dirender secara reaktif oleh **React** menggunakan komponen **Tailwind CSS v4**.

### 1.1 Aktor & Hak Akses (`EnsureRole`)

| Peran (`users.role`) | Deskripsi Hak Akses | Panel Tujuan |
| :--- | :--- | :--- |
| `master_admin` | Akses penuh ke modul Admin dan modul Kasir POS | `/admin` & `/cashier` |
| `admin` | Kelola member, daily pass, produk & kategori, pengumuman, moderasi foto, feedback, akun kasir, laporan & ekspor Excel | `/admin` |
| `cashier` | Scan & verifikasi check-in QR, perpanjangan member, daily pass, POS penjualan produk, registrasi member baru, verifikasi QRIS, cetak struk | `/cashier` |
| `member` | Portal Member (QR pribadi, riwayat latihan, statistik, pesan pengumuman, info paket, profil & kritik saran). Otomatis diblokir jika `expires_at` telah lewat | `/member/dashboard` |
| Kiosk / Tamu | Pengisian check-in mandiri tanpa login melalui `/member-checkin` dan aktivasi akun member melalui `/member/activate` | `/member-checkin` |

### 1.2 Struktur Layer Aplikasi Backend

- **Middleware**:
  - `App\Http\Middleware\HandleInertiaRequests` — membagikan props global (`appName`, `auth`, `flash`, `memberNotifications`, `adminBadges`).
  - `App\Http\Middleware\EnsureRole` — memvalidasi sesi peran (`admin`, `cashier`, `member`) dan memeriksa status aktif membership secara real-time.
- **Service & Helper**:
  - `App\Services\MemberProfilePhotoService` — mengelola kuota ganti foto langsung (maksimal `3` kali) dan pengajuan moderasi foto admin (`ProfilePhotoChangeRequest`).
  - `App\Services\WhatsAppGatewayService` & `App\Helpers\WhatsAppHelper` — penyusunan pesan pengingat jatuh tempo dan tautan berbagi saluran WhatsApp.
  - `App\Helpers\RouteHelpers` — agregasi statistik dashboard kasir via SQL aggregate, pencatatan check-in atomik (`DB::transaction`), dan kalkulasi perpanjangan siklus tanggal (`addMonthsNoOverflow`).
- **Controllers** (`app/Http/Controllers/`):
  - `Auth\LoginController`
  - `Kiosk\MemberCheckinController`
  - `Member\MemberAuthController`, `Member\MemberPortalController`
  - `Admin\DashboardController`, `MemberController`, `DailyPassController`, `ProductController`, `CheckinController`, `AnnouncementController`, `ProfilePhotoRequestController`, `FeedbackController`, `CashierAccountController`, `ReportController`
  - `Cashier\DashboardController`, `CheckinController`, `MemberPaymentController`, `DailyPaymentController`, `TransactionController`, `ReceiptController`

---

## 2. Design System

Antarmuka website dirombak menyeluruh menggunakan **React + Tailwind CSS v4** dengan dukungan penuh **Dark Mode & Light Mode** (`localStorage['arena-gym-theme']`).

### 2.1 Palet Warna & Token Semantik

| Token / Peran | Light Mode | Dark Mode | Kegunaan |
| :--- | :--- | :--- | :--- |
| **Canvas Utama** | `bg-slate-50` (`#f8fafc`) | `dark:bg-slate-950` (`#020617`) | Latar belakang utama halaman |
| **Surface Card** | `bg-white` + `border-slate-200/80` | `dark:bg-slate-900` + `dark:border-slate-800` | Kartu, tabel, modal, dan form |
| **Aksen Utama (Brand)** | Gradient `from-rose-600 to-orange-500` | Gradient `from-rose-600 to-orange-500` | CTA primer, item navigasi aktif, progress bar |
| **Success / Aktif** | `emerald-600` / `emerald-500/15` | `emerald-400` / `emerald-500/15` | Status aktif, pembayaran terverifikasi, check-in sah |
| **Warning / Pending** | `amber-500` / `amber-500/15` | `amber-300` / `amber-500/15` | Jatuh tempo ≤7 hari, QRIS pending, antrean verifikasi |
| **Danger / Expired** | `rose-600` / `rose-500/15` | `rose-400` / `rose-500/15` | Expired, penolakan, aksi hapus |
| **Info / Kategori** | `sky-600` / `sky-500/15` | `sky-300` / `sky-500/15` | Badge informasi dan kategori produk |

### 2.2 Tipografi

- **Sans Utama**: `Outfit` (Google/Bunny Fonts) — tegas dan modern untuk heading, KPI angka, tombol, dan navigasi.
- **Display / Monospace Aksen**: `Space Grotesk` & font monospace — untuk nomor invoice (`MBR-...`, `PRD-...`, `DP-...`), kode QR check-in (`AG-...`), dan struk kasir.

### 2.3 Komponen Standar (`resources/js/Components/UI.jsx` & `Layouts/`)

- **Layouts**:
  - `GuestLayout.jsx`: Halaman autentikasi & kiosk dengan ambient gradient dan toggle tema.
  - `AppLayout.jsx`: Sidebar responsif (slide-over pada mobile) untuk Admin & Kasir, menampilkan badge notifikasi pending secara real-time.
  - `MemberLayout.jsx`: Navbar atas untuk desktop dan *bottom navigation bar* untuk mobile, dilengkapi banner peringatan jatuh tempo dan modal saluran WhatsApp.
- **Atom & Molekul UI**: `Button` (6 varian, 4 ukuran), `Card`, `StatCard`, `Badge`, `Input`, `Select`, `Textarea`, `InputLabel`, `FieldError`, `Modal`, `Pagination`, `FlashBanner`, `EmptyState`, dan `ThemeToggle`.

---

## 3. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    users ||--o| gym_members : "memiliki profil (user_id)"
    users ||--o{ member_feedbacks : "mengirim (user_id)"
    users ||--o{ profile_photo_change_requests : "mengajukan (user_id)"
    users ||--o{ membership_renewal_requests : "mengajukan (user_id)"

    gym_members ||--o{ gym_checkins : "melakukan (gym_member_id)"
    gym_members ||--o{ cashier_transactions : "bertransaksi (gym_member_id)"
    gym_members ||--o{ member_histories : "mencatat histori (gym_member_id)"
    gym_members ||--o{ member_feedbacks : "memberi masukan (gym_member_id)"
    gym_members ||--o{ profile_photo_change_requests : "meminta ganti foto (gym_member_id)"
    gym_members ||--o{ membership_renewal_requests : "meminta perpanjangan (gym_member_id)"

    categories ||--o{ products : "mengelompokkan (category_id)"
    products ||--o{ cashier_transactions : "terjual di (product_id)"
    products ||--o{ member_histories : "dibeli di (product_id)"

    users {
        bigint id PK
        string name
        string login UK
        string email UK
        string role
        string password
    }

    gym_members {
        bigint id PK
        bigint user_id FK
        string full_name
        string email
        string phone
        string checkin_code UK
        string status
        string member_status
        string membership_plan
        string package_status
        string payment_method
        integer payment_amount
        date joined_at
        date expires_at
        string profile_photo_path
        integer profile_photo_change_count
        datetime last_membership_reminder_at
    }

    gym_checkins {
        bigint id PK
        bigint gym_member_id FK
        datetime checked_in_at
        string checkin_method
        string verification_status
        string submitted_name
        string submitted_phone
        timestamp verified_at
        string verified_by
        text notes
    }

    daily_guests {
        bigint id PK
        string full_name
        string phone
        integer payment_amount
        string payment_method
        timestamp visit_at
    }

    categories {
        bigint id PK
        string name UK
        text description
    }

    products {
        bigint id PK
        bigint category_id FK
        string name
        string category
        string brand
        string sku UK
        integer price
        integer stock
        string unit
        boolean is_active
    }

    cashier_transactions {
        bigint id PK
        string invoice UK
        bigint gym_member_id FK
        bigint product_id FK
        string customer_name
        string transaction_group
        string transaction_type
        integer quantity
        integer amount
        integer paid_amount
        integer change_amount
        string payment_method
        string payment_status
        string receipt_status
        datetime transaction_at
    }

    member_histories {
        bigint id PK
        bigint gym_member_id FK
        bigint product_id FK
        string history_type
        datetime occurred_at
        string title
        integer quantity
        integer amount
        string source_type
        bigint source_id
    }

    expense_records {
        bigint id PK
        string title
        string category
        integer amount
        string payment_method
        date expense_date
        text notes
    }

    announcements {
        bigint id PK
        string title
        text body
        string status
        datetime publish_at
        datetime archived_at
    }

    member_feedbacks {
        bigint id PK
        bigint user_id FK
        bigint gym_member_id FK
        string name
        string email
        string subject
        text message
        timestamp read_at
    }

    profile_photo_change_requests {
        bigint id PK
        bigint gym_member_id FK
        bigint user_id FK
        string requested_photo_path
        string status
        timestamp reviewed_at
        string reviewed_by
    }
```

---

## 4. Diagram UML Lengkap

### 4.1 Use Case Diagram

```mermaid
flowchart LR
    Guest["Tamu / Kiosk"]
    Member["Member Aktif"]
    Kasir["Kasir"]
    Admin["Admin / Master Admin"]

    subgraph System["Sistem Manajemen Arena Gym"]
        UC1(("Check-in Mandiri Kiosk"))
        UC2(("Aktivasi Akun Member"))
        UC3(("Login Portal & Lihat QR Pribadi"))
        UC4(("Pantau Statistik & Riwayat Latihan"))
        UC5(("Update Profil, Foto & Kirim Feedback"))
        UC6(("Scan QR & Verifikasi Check-in"))
        UC7(("POS Penjualan Produk Multi-Item"))
        UC8(("Pembayaran Member & Daily Pass"))
        UC9(("Verifikasi QRIS & Cetak Struk"))
        UC10(("Registrasi Member Baru + Akun"))
        UC11(("CRUD Member, Produk & Akun Kasir"))
        UC12(("Moderasi Foto & Balas Feedback"))
        UC13(("Publikasi Pengumuman & Reminder WA"))
        UC14(("Laporan Keuangan, Pengeluaran & Export XLS"))
    end

    Guest --- UC1
    Guest --- UC2

    Member --- UC3
    Member --- UC4
    Member --- UC5

    Kasir --- UC6
    Kasir --- UC7
    Kasir --- UC8
    Kasir --- UC9
    Kasir --- UC10

    Admin --- UC11
    Admin --- UC12
    Admin --- UC13
    Admin --- UC14
    Admin --- UC6
    Admin --- UC7
```

### 4.2 Class Diagram (Model & Service Inti)

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string login
        +string email
        +string role
        +gymMember() HasOne
        +feedbacks() HasMany
    }

    class GymMember {
        +int id
        +int user_id
        +string full_name
        +string checkin_code
        +string status
        +date joined_at
        +date expires_at
        +int profile_photo_change_count
        +getProfilePhotoUrlAttribute() string
        +getProfileInitialsAttribute() string
        +checkins() HasMany
        +verifiedCheckins() HasMany
        +transactions() HasMany
        +checkinHistories() HasMany
    }

    class GymCheckin {
        +int id
        +int gym_member_id
        +datetime checked_in_at
        +string checkin_method
        +string verification_status
        +member() BelongsTo
    }

    class Product {
        +int id
        +int category_id
        +string name
        +int price
        +int stock
        +bool is_active
        +categoryRelation() BelongsTo
        +transactions() HasMany
    }

    class Category {
        +int id
        +string name
        +products() HasMany
    }

    class CashierTransaction {
        +int id
        +string invoice
        +int gym_member_id
        +int product_id
        +string transaction_group
        +int quantity
        +int amount
        +string payment_method
        +string payment_status
        +member() BelongsTo
        +product() BelongsTo
    }

    class MemberProfilePhotoService {
        +MAX_DIRECT_CHANGES = 3
        +handleUpload(User, GymMember, UploadedFile) array
    }

    User "1" --> "0..1" GymMember
    GymMember "1" --> "*" GymCheckin
    GymMember "1" --> "*" CashierTransaction
    Category "1" --> "*" Product
    Product "1" --> "*" CashierTransaction
    MemberProfilePhotoService ..> GymMember : manages photo quota
```

### 4.3 Activity Diagram — POS Penjualan Produk Multi-Item (Kasir)

```mermaid
flowchart TD
    A(["Mulai: Kasir buka /cashier/transactions/products"]) --> B["Pilih produk ke keranjang & tentukan qty"]
    B --> C["Pilih member opsional atau isi nama pembeli"]
    C --> D["Pilih metode pembayaran (Cash / QRIS)"]
    D --> E["Kirim POST /cashier/transactions"]
    E --> F{"Mulai DB::transaction + lockForUpdate produk"}
    F --> G{"Semua produk aktif & stok mencukupi?"}
    G -- "Tidak" --> H["Rollback transaksi & tampilkan error stok"]
    H --> B
    G -- "Ya" --> I{"Metode = Cash?"}
    I -- "Ya" --> J{"Uang diterima >= total checkout?"}
    J -- "Tidak" --> H
    J -- "Ya" --> K["Simpan CashierTransaction (verified/ready) + decrement stok"]
    I -- "Tidak (QRIS)" --> L["Simpan CashierTransaction (pending) + decrement stok"]
    K --> M{"Pembeli adalah Member?"}
    L --> M
    M -- "Ya" --> N["Catat MemberHistory (product_purchase)"]
    M -- "Tidak" --> O["Commit Transaksi"]
    N --> O
    O --> P{"Status Verified?"}
    P -- "Ya" --> Q["Kembali ke POS dengan flash sukses"]
    P -- "Tidak" --> R["Arahkan ke Antrean Verifikasi QRIS (/cashier/receipts)"]
    Q --> S(["Selesai"])
    R --> S
```

### 4.4 Sequence Diagram — Check-in QR Mandiri & Verifikasi Kasir

```mermaid
sequenceDiagram
    actor Member
    participant Kiosk as Kiosk (/member-checkin)
    participant Helper as RouteHelpers::storeMemberCheckin
    participant DB as Database
    actor Kasir
    participant CashierCtrl as Cashier\CheckinController

    Member->>Kiosk: Input Nama/Kode & No. HP lalu Submit
    Kiosk->>Helper: POST /member-checkin (actor: qr_member)
    Helper->>DB: Cari GymMember aktif berdasarkan nama/HP/kode
    alt Member valid & masa aktif masih berlaku
        Helper->>DB: Insert GymCheckin (verification_status = pending)
        Helper-->>Kiosk: Redirect + flash welcome_name (Web Speech API)
        Kiosk-->>Member: Tampilkan notifikasi sukses & sapaan suara
    else Tidak ditemukan / Expired
        Helper-->>Kiosk: Redirect dengan pesan error validasi
    end

    Kasir->>CashierCtrl: Buka /cashier/checkins & klik "Setujui"
    CashierCtrl->>DB: DB::transaction()
    CashierCtrl->>DB: Update GymCheckin -> verified + verified_at
    CashierCtrl->>DB: firstOrCreate MemberHistory (checkin)
    CashierCtrl-->>Kasir: Redirect dengan flash status tervalidasi
```

### 4.5 Sequence Diagram — Pengajuan Ganti Foto Profil Member

```mermaid
sequenceDiagram
    actor Member
    participant Portal as MemberPortalController
    participant PhotoSvc as MemberProfilePhotoService
    participant Disk as Storage (public disk)
    actor Admin
    participant AdminCtrl as ProfilePhotoRequestController

    Member->>Portal: Unggah foto baru (POST /member/profile/photo)
    Portal->>PhotoSvc: handleUpload(user, member, file)
    alt Kuota ganti langsung < 3 kali
        PhotoSvc->>Disk: Simpan ke member-photos/ & hapus foto lama
        PhotoSvc->>PhotoSvc: Increment profile_photo_change_count
        PhotoSvc-->>Member: Foto profil langsung diperbarui
    else Kuota habis (>= 3 kali)
        PhotoSvc->>Disk: Simpan ke member-photo-requests/
        PhotoSvc->>AdminCtrl: Buat ProfilePhotoChangeRequest (status = pending)
        PhotoSvc-->>Member: Tampilkan status menunggu moderasi Admin
        Admin->>AdminCtrl: Klik "Setujui" (POST /admin/profile-photo-requests/{id}/approve)
        AdminCtrl->>Disk: Pindahkan file ke member-photos/ & hapus foto lama
        AdminCtrl->>AdminCtrl: Update status = approved & update member.profile_photo_path
    end
```

---

## 5. Daftar Peningkatan Efisiensi & Perbaikan Bug dari Sistem Awal

1. **Dekomposisi Rute Monolitik ke Controller**: Memecah ribuan baris *route closure* di `routes/member.php`, `routes/admin/*.php`, dan `routes/cashier/*.php` ke 19 controller terstruktur plus `MemberProfilePhotoService`.
2. **Optimasi Query Dashboard Kasir**: `RouteHelpers::buildCashierViewData()` sebelumnya memuat seluruh tabel `cashier_transactions` ke memori PHP; kini menggunakan agregasi SQL langsung (`SUM`, `COUNT`, `GROUP BY`) dan membatasi antrean pending QRIS terbaru (`limit(15)`).
3. **Transaksi Atomik & Anti Race Condition pada POS**: `Cashier\TransactionController::store()` membungkus validasi stok, pencatatan transaksi, pencatatan `MemberHistory`, dan pengurangan stok dalam `DB::transaction()` disertai `lockForUpdate()`.
4. **Penyatuan Portal Login Satu Pintu & Keamanan Role**: `Auth\LoginController` kini mengimplementasikan **satu halaman login terpadu (`/login`) untuk seluruh peran** (Master Admin, Admin, Kasir, dan Member) tanpa perlu memilih tombol peran secara manual. Sistem secara otomatis mendeteksi role pengguna berdasarkan akun/email, memvalidasi password, memeriksa masa aktif membership (`EnsureRole::isMemberMembershipActive`), dan mengarahkan pengguna langsung ke panel tujuan masing-masing. Rute login lama (`/member/login`, `/login/admin`, `/login/cashier`, dll.) otomatis dialihkan ke `/login`.
5. **Sinkronisasi Transaksi Perpanjangan & Akun**: `Admin\MemberController::update()` memperbaiki pencatatan `transaction_group` dari `'membership'` menjadi `'member_payment'` (agar masuk ke laporan pendapatan member), menyinkronkan perubahan nama/email ke tabel `users`, serta membersihkan akun `User` saat member dihapus.
6. **Perbaikan Data Laporan Keuangan & Stok**: `Admin\ReportController` menghapus data dummy statis pada *Laporan Keuangan* sehingga menampilkan pemasukan dan pengeluaran aktual dari basis data, serta menyertakan seluruh master produk (termasuk yang belum terjual) di *Laporan Stok Barang*.
7. **Perbaikan Path Penyimpanan Foto Moderasi**: `Admin\ProfilePhotoRequestController` kini menggunakan `storage_path('app/public/...')` dan `Storage::disk('public')` secara konsisten saat menampilkan maupun menyetujui foto member.

