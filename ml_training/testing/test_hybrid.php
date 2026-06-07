<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';

// Bootstrap Laravel Console Kernel untuk mengaktifkan Eloquent, Facades (Http, DB, Log)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Question;
use App\Services\DiagnosisService;

echo "Memulai Testing Hybrid (XGBoost + Rule-Based)...\n";
echo "Pastikan server Python (Flask) sedang berjalan di port 5000.\n\n";

$diagnosisService = app(DiagnosisService::class);

// 1. Ambil format questions asli dari database (sama seperti di AssessmentController)
$questionsRaw = Question::with('options')->get();

if ($questionsRaw->count() !== 12) {
    die("Error: Jumlah soal di database bukan 12.\n");
}

$questions = $questionsRaw->map(function ($q) {
    $correctOption = $q->options->firstWhere('is_correct', true);
    return [
        'id'                => $q->id,
        'order'             => $q->order,
        'topic'             => $q->topic,
        'correct_option_id' => $correctOption?->id,
        'options'           => $q->options->map(fn($o) => [
            'id'          => $o->id,
            'order'       => $o->order,
            'is_correct'  => $o->is_correct,
            'indicator'   => $o->indicator,
            'level_value' => $o->level_value,
        ])->toArray(),
    ];
})->toArray();

$jumlahSiswa = 100;
$hasilStatistik = [
    'Advanced' => 0,
    'Basic' => 0,
    'NSI' => 0,
    'Proficient' => 0,
];

$metode = [
    'ML' => 0,
    'Rule-Based' => 0,
];

echo str_pad("Siswa", 8) . " | " . 
     str_pad("Skor Benar", 10) . " | " . 
     str_pad("Level", 12) . " | " . 
     str_pad("Metode", 15) . " | " . 
     "Status AI\n";
echo str_repeat("-", 80) . "\n";

for ($i = 1; $i <= $jumlahSiswa; $i++) {
    $randomAnswers = [];

    // Hasilkan jawaban acak untuk tiap soal (mensimulasikan siswa menebak/menjawab)
    foreach ($questions as $q) {
        $opsiAcak = $q['options'][array_rand($q['options'])];
        $randomAnswers[$q['id']] = $opsiAcak['id'];
    }

    // Jalankan service (mencakup Fallback Rule-Base dan call API XGBoost)
    // Code ini 100% menggunakan logika real production
    $hasil = $diagnosisService->diagnose($randomAnswers, $questions);
    
    // Klasifikasikan output
    $levelAkhir = $hasil['level'];
    $hasilStatistik[$levelAkhir]++;
    
    $isML = str_contains($hasil['draw_note'], 'XGBoost');
    $metodeText = $isML ? 'ML XGBoost' : 'Rule-Based';
    $metode[$isML ? 'ML' : 'Rule-Based']++;

    // Jika ML, tampilkan probabilitas tertinggi, jika tidak, tampilkan catatan rule based
    $aiStatus = $isML ? "Probability (Hybrid ON)" : "Fallback (Hybrid OFF)";

    echo str_pad("#" . $i, 8) . " | " . 
         str_pad($hasil['correct'] . "/12", 10) . " | " . 
         str_pad($levelAkhir, 12) . " | " . 
         str_pad($metodeText, 15) . " | " . 
         $aiStatus . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "REKAPITULASI HASIL TESTING 100 SISWA ACAK\n";
echo str_repeat("=", 80) . "\n";
echo "Distribusi Level (Berdasarkan Jawaban Acak):\n";
foreach ($hasilStatistik as $lvl => $count) {
    echo "- $lvl: $count siswa\n";
}
echo "\nPerforma Mesin Integrasi (Hybrid Engine):\n";
echo "- Hit ke Server Python ML Sukses: " . $metode['ML'] . " kali\n";
echo "- Fallback ke Rule-Based Murni  : " . $metode['Rule-Based'] . " kali\n";
