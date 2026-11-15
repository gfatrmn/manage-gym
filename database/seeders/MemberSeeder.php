<?php

namespace Database\Seeders;

use App\Models\CashierTransaction;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\Membership;
use App\Models\MembershipPackage;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $cashierUser = User::query()->where('role', 'cashier')->first();
        $cashierId = $cashierUser?->id;

        $packages = MembershipPackage::query()->get()->keyBy('name');
        $bulananPkg = $packages->get('Bulanan') ?? MembershipPackage::query()->first();
        $tigaBulanPkg = $packages->get('3 Bulan') ?? $bulananPkg;
        $enamBulanPkg = $packages->get('6 Bulan') ?? $bulananPkg;
        $tahunanPkg = $packages->get('Tahunan') ?? $bulananPkg;
        $mingguanPkg = $packages->get('Mingguan') ?? $bulananPkg;

        $membersData = [
            [
                'name' => 'Rian Pratama',
                'login' => 'rian.pratama',
                'email' => 'rian@arenagym.local',
                'phone' => '081298765431',
                'gender' => 'Laki-laki',
                'birth_date' => '1996-03-12',
                'address' => 'Jl. Tebet Barat Dalam No. 14, Jakarta Selatan',
                'checkin_code' => 'AGM-1001',
                'package' => $bulananPkg,
                'joined_at' => $today->copy()->subDays(12),
                'expires_at' => $today->copy()->addDays(18),
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'checkin_today' => 'verified',
                'checkin_time' => '08:15:00',
            ],
            [
                'name' => 'Dewi Sartika',
                'login' => 'dewi.sartika',
                'email' => 'dewi@arenagym.local',
                'phone' => '081345678912',
                'gender' => 'Perempuan',
                'birth_date' => '1998-07-25',
                'address' => 'Jl. Kebayoran Baru No. 28, Jakarta Selatan',
                'checkin_code' => 'AGM-1002',
                'package' => $tigaBulanPkg,
                'joined_at' => $today->copy()->subDays(25),
                'expires_at' => $today->copy()->addDays(65),
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'checkin_today' => 'pending', // Antrean verifikasi check-in!
                'checkin_time' => '09:20:00',
            ],
            [
                'name' => 'Budi Santoso',
                'login' => 'budi.santoso',
                'email' => 'budi@arenagym.local',
                'phone' => '081234567890',
                'gender' => 'Laki-laki',
                'birth_date' => '1994-11-05',
                'address' => 'Jl. Panglima Polim No. 5, Jakarta Selatan',
                'checkin_code' => 'AGM-1003',
                'package' => $bulananPkg,
                'joined_at' => $today->copy()->subDays(27),
                'expires_at' => $today->copy()->addDays(3), // Expiring in 3 days!
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'checkin_today' => null,
            ],
            [
                'name' => 'Siti Rahmawati',
                'login' => 'siti.rahmawati',
                'email' => 'siti@arenagym.local',
                'phone' => '085712349876',
                'gender' => 'Perempuan',
                'birth_date' => '1992-09-18',
                'address' => 'Jl. Gandaria Tengah No. 8, Jakarta Selatan',
                'checkin_code' => 'AGM-1004',
                'package' => $tahunanPkg,
                'joined_at' => $today->copy()->subDays(60),
                'expires_at' => $today->copy()->addDays(305),
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'checkin_today' => 'verified',
                'checkin_time' => '07:30:00',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'login' => 'ahmad.fauzi',
                'email' => 'fauzi@arenagym.local',
                'phone' => '087890123456',
                'gender' => 'Laki-laki',
                'birth_date' => '1999-01-30',
                'address' => 'Jl. Cilandak KKO No. 45, Jakarta Selatan',
                'checkin_code' => 'AGM-1005',
                'package' => $enamBulanPkg,
                'joined_at' => $today->copy()->subDays(15),
                'expires_at' => $today->copy()->addDays(165),
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'checkin_today' => null,
            ],
            [
                'name' => 'Jessica Tan',
                'login' => 'jessica.tan',
                'email' => 'jessica@arenagym.local',
                'phone' => '081122334455',
                'gender' => 'Perempuan',
                'birth_date' => '2000-06-14',
                'address' => 'Jl. Senopati No. 88, Jakarta Selatan',
                'checkin_code' => 'AGM-1006',
                'package' => $bulananPkg,
                'joined_at' => $today->copy()->subDays(5),
                'expires_at' => $today->copy()->addDays(25),
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'checkin_today' => 'verified',
                'checkin_time' => '10:05:00',
            ],
            [
                'name' => 'Dimas Anggara',
                'login' => 'dimas.anggara',
                'email' => 'dimas@arenagym.local',
                'phone' => '081399887766',
                'gender' => 'Laki-laki',
                'birth_date' => '1995-12-22',
                'address' => 'Jl. Antasari No. 19, Jakarta Selatan',
                'checkin_code' => 'AGM-1007',
                'package' => $bulananPkg,
                'joined_at' => $today->copy(),
                'expires_at' => $today->copy()->addDays(30),
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'has_pending_qris' => true, // Pending QRIS verification!
            ],
            [
                'name' => 'Maya Indah',
                'login' => 'maya.indah',
                'email' => 'maya@arenagym.local',
                'phone' => '085611223344',
                'gender' => 'Perempuan',
                'birth_date' => '1997-04-10',
                'address' => 'Jl. Fatmawati No. 70, Jakarta Selatan',
                'checkin_code' => 'AGM-1008',
                'package' => $mingguanPkg,
                'joined_at' => $today->copy()->subDays(9),
                'expires_at' => $today->copy()->subDays(2), // Expired!
                'status' => 'member',
                'package_status' => 'expired',
                'can_check_in' => false,
                'checkin_today' => null,
            ],
            [
                'name' => 'Rizky Ramadhan',
                'login' => 'rizky.ramadhan',
                'email' => 'rizky@arenagym.local',
                'phone' => '081277889900',
                'gender' => 'Laki-laki',
                'birth_date' => '2001-08-19',
                'address' => 'Jl. Bangka Raya No. 11, Jakarta Selatan',
                'checkin_code' => 'AGM-1009',
                'package' => $bulananPkg,
                'joined_at' => $today->copy()->subDays(20),
                'expires_at' => $today->copy()->addDays(10),
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'checkin_today' => null,
            ],
            [
                'name' => 'Nadya Putri',
                'login' => 'nadya.putri',
                'email' => 'nadya@arenagym.local',
                'phone' => '081900112233',
                'gender' => 'Perempuan',
                'birth_date' => '2002-02-14',
                'address' => 'Jl. Kemang Raya No. 42, Jakarta Selatan',
                'checkin_code' => 'AGM-1010',
                'package' => $bulananPkg,
                'joined_at' => $today->copy()->subDays(2),
                'expires_at' => $today->copy()->addDays(28),
                'status' => 'member',
                'package_status' => 'active',
                'can_check_in' => true,
                'checkin_today' => null,
            ],
        ];

        foreach ($membersData as $m) {
            // 1. User
            $user = User::query()->updateOrCreate(
                ['login' => $m['login']],
                [
                    'name' => $m['name'],
                    'email' => $m['email'],
                    'role' => 'member',
                    'is_active' => true,
                    'password' => Hash::make('member123'),
                ]
            );

            // 2. GymMember
            $gymMember = GymMember::query()->updateOrCreate(
                ['checkin_code' => $m['checkin_code']],
                [
                    'user_id' => $user->id,
                    'full_name' => $m['name'],
                    'email' => $m['email'],
                    'phone' => $m['phone'],
                    'gender' => $m['gender'],
                    'birth_date' => $m['birth_date'],
                    'address' => $m['address'],
                    'member_status' => 'member',
                    'membership_plan' => $m['package']->name,
                    'package_status' => $m['package_status'],
                    'can_check_in' => $m['can_check_in'],
                    'joined_at' => $m['joined_at'],
                    'expires_at' => $m['expires_at'],
                    'payment_method' => 'cash',
                    'payment_amount' => $m['package']->price,
                    'status' => 'member',
                    'notes' => 'Member aktif Arena Gym',
                ]
            );

            // 3. Membership record
            Membership::query()->updateOrCreate(
                [
                    'member_id' => $gymMember->id,
                    'package_id' => $m['package']->id,
                ],
                [
                    'start_date' => $m['joined_at'],
                    'end_date' => $m['expires_at'],
                    'price' => $m['package']->price,
                    'status' => $m['package_status'],
                ]
            );

            // 4. Checkin today if applicable
            if (! empty($m['checkin_today'])) {
                $checkinDateTime = Carbon::parse($today->toDateString() . ' ' . $m['checkin_time']);
                GymCheckin::query()->updateOrCreate(
                    [
                        'gym_member_id' => $gymMember->id,
                        'checked_in_at' => $checkinDateTime,
                    ],
                    [
                        'checkin_method' => 'qr',
                        'verification_status' => $m['checkin_today'],
                        'submitted_name' => $m['name'],
                        'submitted_phone' => $m['phone'],
                        'verified_at' => $m['checkin_today'] === 'verified' ? $checkinDateTime : null,
                        'verified_by' => $m['checkin_today'] === 'verified' ? $cashierId : null,
                        'notes' => $m['checkin_today'] === 'verified' ? 'Check-in mandiri QR terverifikasi' : 'Menunggu verifikasi kasir di meja depan',
                    ]
                );
            }

            // 5. Pending QRIS transaction if specified
            if (! empty($m['has_pending_qris'])) {
                CashierTransaction::query()->updateOrCreate(
                    ['invoice' => 'INV-QRIS-' . $today->format('Ymd') . '-001'],
                    [
                        'gym_member_id' => $gymMember->id,
                        'cashier_id' => $cashierId,
                        'package_id' => $m['package']->id,
                        'customer_name' => $m['name'],
                        'transaction_group' => 'member_payment',
                        'transaction_type' => 'Pendaftaran ' . $m['package']->name,
                        'amount' => $m['package']->price,
                        'discount' => 0,
                        'paid_amount' => $m['package']->price,
                        'change_amount' => 0,
                        'quantity' => 1,
                        'payment_method' => 'qris',
                        'payment_status' => 'pending',
                        'receipt_status' => 'pending',
                        'transaction_at' => now()->subMinutes(15),
                        'notes' => 'Menunggu verifikasi dana masuk QRIS di m-banking kasir',
                    ]
                );
            }
        }

        // Seed some completed today's cashier transactions
        $productAqua = Product::query()->where('sku', 'MIN-AQ-600')->first();
        $productPocari = Product::query()->where('sku', 'MIN-PS-500')->first();
        $memberRian = GymMember::query()->where('checkin_code', 'AGM-1001')->first();
        $memberJessica = GymMember::query()->where('checkin_code', 'AGM-1006')->first();

        // Transaction 1: Member renewal cash (Rian)
        CashierTransaction::query()->updateOrCreate(
            ['invoice' => 'INV-' . $today->format('Ymd') . '-001'],
            [
                'gym_member_id' => $memberRian?->id,
                'cashier_id' => $cashierId,
                'package_id' => $bulananPkg->id,
                'customer_name' => $memberRian?->full_name ?? 'Rian Pratama',
                'transaction_group' => 'member_payment',
                'transaction_type' => 'Aktivasi Paket Bulanan',
                'amount' => 250000,
                'discount' => 0,
                'paid_amount' => 300000,
                'change_amount' => 50000,
                'quantity' => 1,
                'payment_method' => 'cash',
                'payment_status' => 'verified',
                'receipt_status' => 'ready',
                'transaction_at' => $today->copy()->setHour(8)->setMinute(20),
                'notes' => 'Pembayaran tunai kasir shift pagi',
            ]
        );

        // Transaction 2: Daily Pass cash (Tamu Umum 1)
        CashierTransaction::query()->updateOrCreate(
            ['invoice' => 'INV-' . $today->format('Ymd') . '-002'],
            [
                'gym_member_id' => null,
                'cashier_id' => $cashierId,
                'customer_name' => 'Fajar Nugroho (Tamu)',
                'transaction_group' => 'daily_pass',
                'transaction_type' => 'Daily Pass Kunjungan Harian',
                'amount' => 25000,
                'discount' => 0,
                'paid_amount' => 50000,
                'change_amount' => 25000,
                'quantity' => 1,
                'payment_method' => 'cash',
                'payment_status' => 'verified',
                'receipt_status' => 'ready',
                'transaction_at' => $today->copy()->setHour(8)->setMinute(50),
                'notes' => 'Daily pass 1 sesi gym',
            ]
        );

        // Transaction 3: Daily Pass QRIS (Tamu Umum 2)
        CashierTransaction::query()->updateOrCreate(
            ['invoice' => 'INV-' . $today->format('Ymd') . '-003'],
            [
                'gym_member_id' => null,
                'cashier_id' => $cashierId,
                'customer_name' => 'Indra Wijaya (Tamu)',
                'transaction_group' => 'daily_pass',
                'transaction_type' => 'Daily Pass Kunjungan Harian',
                'amount' => 25000,
                'discount' => 0,
                'paid_amount' => 25000,
                'change_amount' => 0,
                'quantity' => 1,
                'payment_method' => 'qris',
                'payment_status' => 'verified',
                'receipt_status' => 'ready',
                'transaction_at' => $today->copy()->setHour(9)->setMinute(15),
                'notes' => 'QRIS verified',
            ]
        );

        // Transaction 4: Product sale (Pocari & Aqua to Jessica)
        if ($productPocari) {
            CashierTransaction::query()->updateOrCreate(
                ['invoice' => 'INV-' . $today->format('Ymd') . '-004'],
                [
                    'gym_member_id' => $memberJessica?->id,
                    'cashier_id' => $cashierId,
                    'product_id' => $productPocari->id,
                    'customer_name' => $memberJessica?->full_name ?? 'Jessica Tan',
                    'transaction_group' => 'product',
                    'transaction_type' => 'Penjualan Produk',
                    'amount' => 10000,
                    'discount' => 0,
                    'paid_amount' => 10000,
                    'change_amount' => 0,
                    'quantity' => 1,
                    'payment_method' => 'qris',
                    'payment_status' => 'verified',
                    'receipt_status' => 'ready',
                    'transaction_at' => $today->copy()->setHour(10)->setMinute(10),
                    'notes' => '1x Pocari Sweat 500ml',
                ]
            );
        }

        if ($productAqua) {
            CashierTransaction::query()->updateOrCreate(
                ['invoice' => 'INV-' . $today->format('Ymd') . '-005'],
                [
                    'gym_member_id' => null,
                    'cashier_id' => $cashierId,
                    'product_id' => $productAqua->id,
                    'customer_name' => 'Fajar Nugroho (Tamu)',
                    'transaction_group' => 'product',
                    'transaction_type' => 'Penjualan Produk',
                    'amount' => 5000,
                    'discount' => 0,
                    'paid_amount' => 5000,
                    'change_amount' => 0,
                    'quantity' => 1,
                    'payment_method' => 'cash',
                    'payment_status' => 'verified',
                    'receipt_status' => 'ready',
                    'transaction_at' => $today->copy()->setHour(9)->setMinute(00),
                    'notes' => '1x Aqua 600ml',
                ]
            );
        }
    }
}
