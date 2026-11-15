<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\GymSetting;
use App\Models\MembershipPackage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['login' => env('ADMIN_LOGIN', 'admin')],
            [
                'name' => 'Admin Arena Gym',
                'email' => 'admin@arena-gym.local',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
            ]
        );

        User::query()->updateOrCreate(
            ['login' => env('MASTER_ADMIN_LOGIN', 'masteradmin')],
            [
                'name' => 'Master Admin Arena Gym',
                'email' => 'masteradmin@arena-gym.local',
                'role' => 'master_admin',
                'is_active' => true,
                'password' => Hash::make(env('MASTER_ADMIN_PASSWORD', 'master123')),
            ]
        );

        User::query()->updateOrCreate(
            ['role' => 'cashier'],
            [
                'login' => env('CASHIER_LOGIN', 'kasir'),
                'name' => 'Kasir Arena Gym',
                'email' => 'cashier@arena-gym.local',
                'role' => 'cashier',
                'is_active' => true,
                'password' => Hash::make(env('CASHIER_PASSWORD', 'kasir123')),
            ]
        );

        // Seed Membership Packages from PRD 3.6
        $packages = [
            [
                'name' => 'Harian',
                'duration_days' => 1,
                'price' => 25000,
                'description' => 'Akses gym 1 hari penuh untuk latihan mandiri.',
                'facilities' => 'Akses Gym, Loker, Kamar Mandi',
                'is_active' => true,
            ],
            [
                'name' => 'Mingguan',
                'duration_days' => 7,
                'price' => 100000,
                'description' => 'Akses gym fleksibel selama 7 hari kalender.',
                'facilities' => 'Akses Gym 7 Hari, Loker, Kamar Mandi, Konsultasi Dasar',
                'is_active' => true,
            ],
            [
                'name' => 'Bulanan',
                'duration_days' => 30,
                'price' => 250000,
                'description' => 'Paket bulanan reguler bebas jam latihan.',
                'facilities' => 'Akses Gym 30 Hari, Loker, Kamar Mandi, Wi-Fi',
                'is_active' => true,
            ],
            [
                'name' => '3 Bulan',
                'duration_days' => 90,
                'price' => 650000,
                'description' => 'Paket hemat kuartalan dengan fasilitas lengkap.',
                'facilities' => 'Akses Gym 90 Hari, Loker Pribadi, Kamar Mandi, Wi-Fi, Hemat 13%',
                'is_active' => true,
            ],
            [
                'name' => '6 Bulan',
                'duration_days' => 180,
                'price' => 1200000,
                'description' => 'Paket semesteran intensif kebugaran.',
                'facilities' => 'Akses Gym 180 Hari, Loker, Wi-Fi, Free Shaker, Hemat 20%',
                'is_active' => true,
            ],
            [
                'name' => 'Tahunan',
                'duration_days' => 365,
                'price' => 2200000,
                'description' => 'Paket tahunan eksklusif value terbaik.',
                'facilities' => 'Akses Gym 365 Hari, Loker Pribadi, Free Merchandise, Akses VIP',
                'is_active' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            MembershipPackage::query()->updateOrCreate(
                ['name' => $pkg['name']],
                $pkg
            );
        }

        // Seed Expense Categories from PRD 3.12
        $categories = [
            ['name' => 'Listrik', 'description' => 'Tagihan listrik operasional gym'],
            ['name' => 'Air', 'description' => 'Tagihan air PDAM operasional gym'],
            ['name' => 'Perawatan Alat', 'description' => 'Servis & maintenance alat fitness'],
            ['name' => 'Perlengkapan', 'description' => 'Pembelian perlengkapan & utilitas'],
            ['name' => 'Kebersihan', 'description' => 'Bahan & peralatan kebersihan gym'],
            ['name' => 'Gaji Karyawan', 'description' => 'Operasional honor staf & kasir'],
            ['name' => 'Operasional Lainnya', 'description' => 'Pengeluaran umum lain'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::query()->updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }

        // Seed Gym Settings from PRD 3.14
        $settings = [
            'gym_name' => 'Arena Gym & Fitness',
            'gym_phone' => '0812-3456-7890',
            'gym_address' => 'Jl. Arena Olahraga No. 10, Jakarta Selatan',
            'gym_email' => 'contact@arenagym.local',
            'invoice_prefix' => 'INV',
            'timezone' => 'Asia/Jakarta',
            'invoice_footer' => 'Terima kasih atas kunjungan Anda. Semangat berolahraga!',
        ];

        foreach ($settings as $key => $val) {
            GymSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $val]
            );
        }

        // Seed products and realistic members for front-office & cashier operations
        $this->call([
            ProductSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
