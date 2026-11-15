<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = [
            [
                'name' => 'Minuman',
                'description' => 'Minuman segar, isotonik, elektrolit, dan penambah energi saat latihan',
                'products' => [
                    [
                        'name' => 'Air Mineral Aqua 600ml',
                        'brand' => 'Danone Aqua',
                        'sku' => 'MIN-AQ-600',
                        'price' => 5000,
                        'stock' => 120,
                        'unit' => 'botol',
                        'description' => 'Air mineral pegunungan alami botol 600ml untuk hidrasi optimal.',
                    ],
                    [
                        'name' => 'Pocari Sweat 500ml',
                        'brand' => 'Otsuka',
                        'sku' => 'MIN-PS-500',
                        'price' => 10000,
                        'stock' => 65,
                        'unit' => 'botol',
                        'description' => 'Minuman isotonik pengganti ion dan cairan tubuh yang hilang saat workout.',
                    ],
                    [
                        'name' => 'Hydro Coco 330ml',
                        'brand' => 'Kalbe',
                        'sku' => 'MIN-HC-330',
                        'price' => 10000,
                        'stock' => 45,
                        'unit' => 'botol',
                        'description' => 'Air kelapa asli kaya nutrisi dan elektrolit alami tanpa pengawet.',
                    ],
                    [
                        'name' => 'Red Bull Energy Can 250ml',
                        'brand' => 'Red Bull',
                        'sku' => 'MIN-RB-250',
                        'price' => 25000,
                        'stock' => 35,
                        'unit' => 'kaleng',
                        'description' => 'Minuman berenergi dengan taurin dan vitamin B kompleks untuk daya tahan fisik.',
                    ],
                    [
                        'name' => 'Noolab Smart Energy Sparkling 330ml',
                        'brand' => 'Noolab',
                        'sku' => 'MIN-NL-330',
                        'price' => 28000,
                        'stock' => 30,
                        'unit' => 'kaleng',
                        'description' => 'Minuman energi nootropik bebas gula untuk fokus dan stamina saat latihan berat.',
                    ],
                ],
            ],
            [
                'name' => 'Suplemen',
                'description' => 'Whey protein, creatine, bcaa, dan pre-workout pendukung pembentukan otot',
                'products' => [
                    [
                        'name' => 'Evolene Isolene Whey 1 Scoop',
                        'brand' => 'Evolene',
                        'sku' => 'SUP-EVO-SCP',
                        'price' => 25000,
                        'stock' => 80,
                        'unit' => 'scoop',
                        'description' => 'Whey Isolate tinggi protein 27g per serving rendah lemak dan kalori.',
                    ],
                    [
                        'name' => 'Muscle First Pro Creatine 1 Serving',
                        'brand' => 'Muscle First',
                        'sku' => 'SUP-M1-CRT',
                        'price' => 15000,
                        'stock' => 95,
                        'unit' => 'scoop',
                        'description' => 'Creatine Monohydrate murni untuk peningkatan power, repetisi, dan massa otot.',
                    ],
                    [
                        'name' => 'C4 Original Pre-Workout 1 Scoop',
                        'brand' => 'Cellucor',
                        'sku' => 'SUP-C4-PRE',
                        'price' => 20000,
                        'stock' => 60,
                        'unit' => 'scoop',
                        'description' => 'Pre-workout legendaris untuk ledakan energi, pump otot maksimal, dan fokus.',
                    ],
                    [
                        'name' => 'Optimum Nutrition Gold Standard Whey 2 lbs',
                        'brand' => 'Optimum Nutrition',
                        'sku' => 'SUP-ON-2LB',
                        'price' => 580000,
                        'stock' => 12,
                        'unit' => 'tub',
                        'description' => 'Whey protein nomor 1 di dunia rasa Double Rich Chocolate 2 lbs kemasan segel.',
                    ],
                    [
                        'name' => 'Xtend Original BCAA 1 Serving',
                        'brand' => 'Scivation',
                        'sku' => 'SUP-XT-BCAA',
                        'price' => 18000,
                        'stock' => 50,
                        'unit' => 'scoop',
                        'description' => '7g BCAA rasio 2:1:1 dengan elektrolit pencegah penyusutan otot (catabolic).',
                    ],
                ],
            ],
            [
                'name' => 'Snack Sehat',
                'description' => 'Camilan tinggi protein dan rendah gula untuk asupan nutrisi sehat',
                'products' => [
                    [
                        'name' => 'Fitbar Tiramisu Delight 22g',
                        'brand' => 'Fitbar',
                        'sku' => 'SNK-FB-TRM',
                        'price' => 7000,
                        'stock' => 55,
                        'unit' => 'pcs',
                        'description' => 'Snack gandum oat renyah bebas kolesterol dan rendah kalori (90 kkal).',
                    ],
                    [
                        'name' => 'Soyjoy Crispy Almond & Chocolate 30g',
                        'brand' => 'Otsuka',
                        'sku' => 'SNK-SJ-ALM',
                        'price' => 12000,
                        'stock' => 48,
                        'unit' => 'pcs',
                        'description' => 'Snack kedelai utuh dengan almond panggang dan cokelat hitam kaya isoflavon.',
                    ],
                    [
                        'name' => 'Quest Protein Bar Chocolate Chip Cookie',
                        'brand' => 'Quest Nutrition',
                        'sku' => 'SNK-QST-CHIP',
                        'price' => 45000,
                        'stock' => 35,
                        'unit' => 'pcs',
                        'description' => '21g protein whey murni dengan serat tinggi dan hanya 1g gula per bar.',
                    ],
                    [
                        'name' => 'Telur Rebus Organik (Isi 2 Butir)',
                        'brand' => 'Arena Kitchen',
                        'sku' => 'SNK-EGG-2PCS',
                        'price' => 10000,
                        'stock' => 25,
                        'unit' => 'porsi',
                        'description' => 'Sumber protein alami 12-14g siap santap setelah selesai sesi latihan.',
                    ],
                ],
            ],
            [
                'name' => 'Aksesoris & Gym Gear',
                'description' => 'Perlengkapan fitness, botol minum, handuk, dan straps pendukung angkat beban',
                'products' => [
                    [
                        'name' => 'Shaker Arena Gym Premium 700ml',
                        'brand' => 'Arena Gym',
                        'sku' => 'ACC-SHK-700',
                        'price' => 65000,
                        'stock' => 30,
                        'unit' => 'pcs',
                        'description' => 'Shaker botol BPA-Free dengan bola pengocok spiral anti gumpal dan anti bocor.',
                    ],
                    [
                        'name' => 'Handuk Gym Microfiber Quick-Dry',
                        'brand' => 'Arena Gym',
                        'sku' => 'ACC-TWL-MCR',
                        'price' => 40000,
                        'stock' => 40,
                        'unit' => 'pcs',
                        'description' => 'Handuk olahraga serat microfiber daya serap tinggi dan cepat kering berlogo Arena.',
                    ],
                    [
                        'name' => 'Lifting Straps Katun Tebal Heavy Duty',
                        'brand' => 'Arena Gear',
                        'sku' => 'ACC-STP-COT',
                        'price' => 55000,
                        'stock' => 25,
                        'unit' => 'pasang',
                        'description' => 'Tali pegangan barbell/dumbbell dengan bantalan busa pergelangan anti lecet.',
                    ],
                    [
                        'name' => 'Wrist Wraps Elastic Support',
                        'brand' => 'Arena Gear',
                        'sku' => 'ACC-WRP-ELS',
                        'price' => 60000,
                        'stock' => 20,
                        'unit' => 'pasang',
                        'description' => 'Pelindung pergelangan tangan elastis untuk bench press dan shoulder press berat.',
                    ],
                ],
            ],
        ];

        foreach ($categoriesData as $catData) {
            $category = Category::query()->updateOrCreate(
                ['name' => $catData['name']],
                ['description' => $catData['description']]
            );

            foreach ($catData['products'] as $item) {
                Product::query()->updateOrCreate(
                    ['sku' => $item['sku']],
                    [
                        'name' => $item['name'],
                        'category' => strtolower($catData['name']),
                        'category_id' => $category->id,
                        'brand' => $item['brand'],
                        'price' => $item['price'],
                        'stock' => $item['stock'],
                        'unit' => $item['unit'],
                        'description' => $item['description'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
