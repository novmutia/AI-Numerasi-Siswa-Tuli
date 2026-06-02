<?php

namespace App\Services;

/**
 * DiagnosisService
 * ─────────────────────────────────────────────────────────────────────────────
 * Service ini adalah TITIK INTEGRASI untuk model AI rule-based dari tim lain.
 *
 * Tim AI cukup mengisi method `diagnose()` dengan logika rule-based mereka.
 * Format input dan output sudah ditetapkan di sini.
 * ─────────────────────────────────────────────────────────────────────────────
 */
class DiagnosisService
{
    /**
     * Topik-topik numerasi yang diujikan.
     * Sesuaikan dengan soal yang dibuat tim konten.
     */
    const TOPICS = [
        'bilangan_bulat'  => 'Bilangan Bulat',
        'pecahan'         => 'Pecahan & Desimal',
        'geometri'        => 'Geometri & Pengukuran',
        'statistika'      => 'Statistika Dasar',
        'aljabar'         => 'Pola & Aljabar',
    ];

    /**
     * Threshold level kemampuan (berdasarkan akurasi %)
     */
    const LEVEL_THRESHOLDS = [
        'Advanced'   => 80,
        'Proficient' => 60,
        'Basic'      => 40,
        'NSI'        => 0,
    ];

    /**
     * Rekomendasi per level — ditetapkan oleh tim AI / konten.
     * Tim AI dapat mengganti atau memperluas array ini.
     */
    const RECOMMENDATIONS = [
        'NSI' => [
            'Mulai dari konsep numerasi paling dasar: mengenal angka 1–100 dengan media visual.',
            'Gunakan benda konkret (kelereng, blok) untuk menjelaskan operasi hitung dasar.',
            'Berikan latihan harian 15 menit dengan soal bergambar tanpa teks.',
            'Lakukan evaluasi mingguan untuk memantau perkembangan secara konsisten.',
        ],
        'Basic' => [
            'Perkuat pemahaman operasi hitung (+, −, ×, ÷) dengan contoh nyata sehari-hari.',
            'Gunakan video BISINDO tambahan untuk topik yang masih lemah.',
            'Berikan soal latihan bertahap dari mudah ke sedang, 3× seminggu.',
            'Libatkan siswa dalam kegiatan mengukur atau menghitung benda di sekitar sekolah.',
        ],
        'Proficient' => [
            'Tingkatkan ke soal aplikasi: numerasi dalam konteks kehidupan nyata.',
            'Perkenalkan soal multi-langkah yang membutuhkan lebih dari satu operasi.',
            'Dorong siswa untuk menjelaskan cara mereka menyelesaikan soal (metacognisi).',
            'Berikan proyek kecil seperti menghitung anggaran belanja sederhana.',
        ],
        'Advanced' => [
            'Berikan tantangan soal olimpiade tingkat dasar untuk stimulasi lebih.',
            'Libatkan siswa sebagai tutor sebaya bagi teman yang levelnya lebih rendah.',
            'Perkenalkan konsep matematika lanjutan: statistika, probabilitas sederhana.',
            'Eksplorasi aplikasi numerasi di bidang lain: sains, ekonomi, seni.',
        ],
    ];

    /**
     * Rekomendasi tambahan per topik yang lemah.
     * Tim AI dapat menambah atau mengedit sesuai kurikulum.
     */
    const TOPIC_RECOMMENDATIONS = [
        'bilangan_bulat' => 'Latih operasi bilangan bulat dengan permainan kartu angka atau timbangan bilangan.',
        'pecahan'        => 'Gunakan media pizza atau kue untuk menjelaskan konsep pecahan secara visual.',
        'geometri'       => 'Ajak siswa mengukur benda di kelas untuk menghubungkan geometri dengan kehidupan nyata.',
        'statistika'     => 'Buat diagram batang sederhana dari data keseharian siswa (tinggi badan, nilai).',
        'aljabar'        => 'Gunakan pola gambar atau warna untuk mengenalkan konsep pola bilangan.',
    ];

    /**
     * ════════════════════════════════════════════════════════════════════════
     * METHOD UTAMA — Dipanggil oleh AssessmentController setelah semua jawaban
     * dikumpulkan.
     *
     * @param array $answers  Format: [ question_id => option_id, ... ]
     * @param array $questions Format: koleksi Question dengan relasi options & topic
     * @return array  Hasil diagnosis lengkap (lihat format return di bawah)
     * ════════════════════════════════════════════════════════════════════════
     */
    public function diagnose(array $answers, array $questions, $context = null): array
    {
        // ── 1. Hitung skor per topik ──────────────────────────────────────
        $topicStats = [];
        $totalCorrect = 0;
        $totalQuestions = count($questions);

        foreach ($questions as $question) {
            $topic   = $question['topic'] ?? 'bilangan_bulat';
            $correct = $question['correct_option_id'];
            $given   = $answers[$question['id']] ?? null;
            $isRight = ($given == $correct);

            if (!isset($topicStats[$topic])) {
                $topicStats[$topic] = ['correct' => 0, 'total' => 0];
            }
            $topicStats[$topic]['total']++;
            if ($isRight) {
                $topicStats[$topic]['correct']++;
                $totalCorrect++;
            }
        }

        // ── 2. Hitung akurasi & tentukan level ────────────────────────────
        $accuracy = $totalQuestions > 0
            ? round(($totalCorrect / $totalQuestions) * 100)
            : 0;

        $level = $this->determineLevel($accuracy);

        // ── 3. Tentukan topik lemah (< 60% benar di topik tersebut) ───────
        $weakTopics    = [];
        $weaknesses    = [];
        $topicScores   = [];

        foreach ($topicStats as $key => $stat) {
            $score   = $stat['total'] > 0 ? round(($stat['correct'] / $stat['total']) * 100) : 0;
            $isWeak  = $score < 60;

            $topicScores[] = [
                'key'     => $key,
                'name'    => self::TOPICS[$key] ?? $key,
                'correct' => $stat['correct'],
                'total'   => $stat['total'],
                'score'   => $score,
                'is_weak' => $isWeak,
            ];

            if ($isWeak) {
                $weakTopics[] = $key;
                $weaknesses[] = (self::TOPICS[$key] ?? $key) . " ({$score}% benar)";
            }
        }

        // Urutkan dari skor terendah
        usort($topicScores, fn($a, $b) => $a['score'] - $b['score']);

        // ── 4. Bangun rekomendasi ─────────────────────────────────────────
        $recommendations = self::RECOMMENDATIONS[$level] ?? [];

        // Tambah rekomendasi spesifik untuk topik lemah
        foreach ($weakTopics as $topic) {
            if (isset(self::TOPIC_RECOMMENDATIONS[$topic])) {
                $recommendations[] = self::TOPIC_RECOMMENDATIONS[$topic];
            }
        }

        // ── 5. Bangun catatan AI ──────────────────────────────────────────
        $aiNote = $this->buildAiNote($level, $accuracy, $weakTopics, $totalCorrect, $totalQuestions);

        // ── 6. Return hasil ───────────────────────────────────────────────
        return [
            'level'           => $level,
            'accuracy'        => $accuracy,
            'correct'         => $totalCorrect,
            'total'           => $totalQuestions,
            'topic_scores'    => $topicScores,
            'weaknesses'      => $weaknesses,
            'recommendations' => array_slice($recommendations, 0, 5), // maks 5 rekomendasi
            'ai_note'         => $aiNote,
            'weak_topics'     => $weakTopics,
        ];
    }

    /**
     * Tentukan level berdasarkan akurasi.
     * Tim AI dapat mengganti logika ini dengan rule yang lebih kompleks.
     */
    private function determineLevel(int $accuracy): string
    {
        foreach (self::LEVEL_THRESHOLDS as $level => $threshold) {
            if ($accuracy >= $threshold) {
                return $level;
            }
        }
        return 'NSI';
    }

    /**
     * Buat narasi catatan AI berdasarkan hasil diagnosis.
     */
    private function buildAiNote(string $level, int $accuracy, array $weakTopics, int $correct, int $total): string
    {
        $weakList = empty($weakTopics)
            ? 'tidak ada kelemahan signifikan yang terdeteksi'
            : 'kelemahan terdeteksi pada topik: ' . implode(', ', array_map(fn($t) => self::TOPICS[$t] ?? $t, $weakTopics));

        $notes = [
            'NSI'        => "Siswa menjawab {$correct} dari {$total} soal dengan benar ({$accuracy}%). Sistem mendeteksi bahwa siswa belum mencapai indikator kemampuan numerasi minimum. {$weakList}. Disarankan intervensi dini dengan pendekatan multisensori.",
            'Basic'      => "Siswa menjawab {$correct} dari {$total} soal dengan benar ({$accuracy}%). Kemampuan dasar numerasi sudah terbentuk, namun masih membutuhkan penguatan. {$weakList}. Fokus pada pemahaman konsep sebelum prosedur.",
            'Proficient' => "Siswa menjawab {$correct} dari {$total} soal dengan benar ({$accuracy}%). Siswa menunjukkan penguasaan numerasi yang baik. {$weakList}. Disarankan perluasan ke konteks aplikasi numerasi yang lebih beragam.",
            'Advanced'   => "Siswa menjawab {$correct} dari {$total} soal dengan benar ({$accuracy}%). Siswa menunjukkan penguasaan numerasi yang sangat baik dan siap untuk tantangan lebih tinggi. {$weakList}.",
        ];

        return $notes[$level] ?? "Akurasi: {$accuracy}%. Level: {$level}.";
    }
}
