<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = [
    ['name' => 'Andi Pratama', 'email' => 'andi.member@example.com', 'subject' => 'AC kurang dingin', 'message' => 'Area free weight sore hari terasa panas, mohon AC ditingkatkan.'],
    ['name' => 'Siti Lestari', 'email' => 'siti.member@example.com', 'subject' => 'Saran jadwal kelas', 'message' => 'Kalau bisa tambah kelas pagi jam 06:00 untuk member yang kerja jam kantor.'],
    ['name' => 'Rizki Maulana', 'email' => 'rizki.member@example.com', 'subject' => 'Kebersihan locker', 'message' => 'Locker room sudah bagus, tapi mohon jadwal bersih-bersih ditambah di malam hari.'],
    ['name' => 'Nadia Putri', 'email' => 'nadia.member@example.com', 'subject' => 'Musik terlalu keras', 'message' => 'Volume musik di cardio zone kadang terlalu keras, mungkin bisa sedikit diturunkan.'],
    ['name' => 'Bima Saputra', 'email' => 'bima.member@example.com', 'subject' => 'Apresiasi trainer', 'message' => 'Coach malam sangat membantu koreksi form squat, pelayanan sangat baik.'],
];

foreach ($rows as $row) {
    App\Models\MemberFeedback::create($row);
}

echo 'Inserted: ' . count($rows) . PHP_EOL;
