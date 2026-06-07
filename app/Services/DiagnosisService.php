<?php

namespace App\Services;

/**
 * DiagnosisService — Rule-Based + Weighted Probability
 * ─────────────────────────────────────────────────────────────────────────────
 * Logika sistem pakar untuk mendiagnosis kemampuan numerasi siswa tunarungu.
 *
 * Alur:
 * 1. Baca indikator dari setiap pilihan yang dipilih siswa
 * 2. Hitung akumulasi pola indikator per dimensi
 * 3. Tentukan level dominan (rule IF-THEN + handle draw)
 * 4. Hitung probabilitas tiap level (weighted scoring)
 * 5. Identifikasi dimensi paling lemah
 * 6. Ambil rekomendasi berdasarkan level + dimensi lemah
 * ─────────────────────────────────────────────────────────────────────────────
 */
class DiagnosisService
{
    // ── Dimensi soal (sesuai 12 soal dari dosen) ─────────────────────────
    const DIMENSIONS = [
        'Skills'           => 'Keterampilan Numerasi',
        'Problem Solving'  => 'Pemecahan Masalah',
        'Literasi Konteks' => 'Literasi Konteks',
    ];

    // ── Kategori indikator → bobot level ─────────────────────────────────
    // Setiap indikator masuk ke satu kategori: lemah / parsial / tepat
    // Kategori menentukan bobot kontribusi ke level masing-masing
    const INDICATOR_CATEGORY = [
        // KATEGORI LEMAH → kontribusi ke NSI
        'tidak_memahami_operasi'     => 'lemah',
        'tidak_pahami_konteks'       => 'lemah',
        'hanya_baca_sebagian'        => 'lemah',
        'salah_hitung_semua'         => 'lemah',
        'salah_operasi_jumlah_bukan_kurang' => 'lemah',
        'salah_operasi_tambah'       => 'lemah',
        'salah_konteks_harga'        => 'lemah',
        'salah_strategi_bagi'        => 'lemah',
        'salah_strategi_perkalian'   => 'lemah',
        'ambil_nilai_terkecil'       => 'lemah',
        'ambil_nilai_terbesar'       => 'lemah',
        'salah_strategi_kompleks'    => 'lemah',
        'salah_strategi_bagi_konteks' => 'lemah',
        'salah_hitung_konteks'       => 'lemah',

        // KATEGORI PARSIAL → kontribusi ke Basic
        'salah_hitung_minor'         => 'parsial',
        'salah_hitung_lebih'         => 'parsial',
        'salah_operasi_bagi_dua'     => 'parsial',
        'terbalik_pembilang_penyebut' => 'parsial',
        'belum_sederhanakan'         => 'parsial',
        'salah_hitung_pembagian'     => 'parsial',
        'salah_hitung_bagi_konteks'  => 'parsial',
        'salah_hitung_konteks_perkalian' => 'parsial',

        // KATEGORI TEPAT → kontribusi ke Proficient / Advanced
        'operasi_dasar_benar'        => 'tepat',
        'identifikasi_info_tepat'    => 'tepat',
        'konteks_sederhana_benar'    => 'tepat',
        'operasi_konteks_benar'      => 'tepat',
        'operasi_rutin_benar'        => 'tepat',
        'konteks_sehari_benar'       => 'tepat',
        'konsep_pecahan_benar'       => 'tepat',
        'strategi_pembagian_benar'   => 'tepat',
        'konteks_perkalian_benar'    => 'tepat',
        'konsep_rata_rata_benar'     => 'tepat',
        'strategi_kompleks_benar'    => 'tepat',
        'konteks_bagi_kompleks_benar' => 'tepat',
    ];

    // ── Bobot kontribusi kategori ke tiap level ───────────────────────────
    // Setiap jawaban memberikan bobot ke SEMUA level, tapi dengan besar berbeda
    // Total bobot per jawaban = 10 (untuk normalisasi mudah)
    const CATEGORY_WEIGHTS = [
        'lemah'   => ['NSI' => 7, 'Basic' => 2, 'Proficient' => 1, 'Advanced' => 0],
        'parsial' => ['NSI' => 1, 'Basic' => 6, 'Proficient' => 2, 'Advanced' => 1],
        'tepat'   => ['NSI' => 0, 'Basic' => 1, 'Proficient' => 5, 'Advanced' => 4],
    ];

    // ── Urutan prioritas level saat draw (lebih rendah = lebih konservatif) ─
    const LEVEL_PRIORITY = [
        'NSI' => 1,
        'Basic' => 2,
        'Proficient' => 3,
        'Advanced' => 4
    ];

    // ── Rekomendasi per level + dimensi ───────────────────────────────────
    const RECOMMENDATIONS = [
        'NSI' => [
            'Skills' => [
                'judul'    => 'Penguatan Operasi Dasar dengan Media Visual',
                'strategi' => 'Gunakan benda konkret (koin, blok, kartu angka) untuk menjelaskan penjumlahan dan pengurangan. Hindari teks — fokus pada gambar dan manipulasi langsung.',
                'aktivitas' => 'Permainan menghitung kelereng, tebak angka dengan jari, kartu flash angka 1–20.',
            ],
            'Problem Solving' => [
                'judul'    => 'Identifikasi Informasi dengan Gambar',
                'strategi' => 'Latih siswa membaca informasi dari gambar sebelum menjawab soal. Gunakan strategi "tunjuk dan ceritakan" menggunakan bahasa isyarat.',
                'aktivitas' => 'Latihan membaca tabel sederhana, diagram bergambar, soal bergambar tanpa teks.',
            ],
            'Literasi Konteks' => [
                'judul'    => 'Numerasi dalam Kehidupan Sehari-hari',
                'strategi' => 'Hubungkan matematika dengan aktivitas harian: belanja, waktu, jumlah benda. Gunakan foto nyata sebagai stimulus soal.',
                'aktivitas' => 'Simulasi belanja dengan uang mainan, menghitung benda di kelas, membaca jam.',
            ],
        ],
        'Basic' => [
            'Skills' => [
                'judul'    => 'Penguatan Operasi Hitung dalam Konteks',
                'strategi' => 'Siswa sudah paham operasi dasar tapi masih salah dalam konteks. Perbanyak soal cerita bergambar dengan langkah-langkah terstruktur.',
                'aktivitas' => 'Soal cerita visual 2 langkah, latihan pengurangan dengan gambar, lembar kerja bergambar.',
            ],
            'Problem Solving' => [
                'judul'    => 'Latihan Strategi Penyelesaian Rutin',
                'strategi' => 'Ajarkan strategi "baca soal → temukan yang diketahui → pilih operasi → hitung" secara visual dengan kartu langkah.',
                'aktivitas' => 'Kartu langkah strategi, soal pola berulang, permainan tebak operasi.',
            ],
            'Literasi Konteks' => [
                'judul'    => 'Konteks Kehidupan yang Lebih Beragam',
                'strategi' => 'Perluas dari konteks belanja ke konteks lain: memasak, mengukur, waktu. Gunakan foto dan video BISINDO.',
                'aktivitas' => 'Video konteks sehari-hari, soal foto belanja, latihan membaca label harga.',
            ],
        ],
        'Proficient' => [
            'Skills' => [
                'judul'    => 'Pendalaman Konsep Numerasi Lanjut',
                'strategi' => 'Perkenalkan pecahan, persentase, dan rasio dengan representasi visual (diagram lingkaran, batang). Dorong siswa menjelaskan cara berpikirnya.',
                'aktivitas' => 'Soal pecahan bergambar, diagram persentase, latihan konversi satuan.',
            ],
            'Problem Solving' => [
                'judul'    => 'Soal Non-Rutin dan Multi-Langkah',
                'strategi' => 'Berikan soal yang membutuhkan lebih dari satu langkah. Minta siswa tuliskan langkah-langkah sebelum menjawab.',
                'aktivitas' => 'Soal cerita multi-langkah, teka-teki numerasi, proyek menghitung sederhana.',
            ],
            'Literasi Konteks' => [
                'judul'    => 'Aplikasi Numerasi Multi-Konteks',
                'strategi' => 'Gunakan data nyata (harga, jarak, waktu) dalam berbagai konteks. Dorong siswa transfer pengetahuan ke situasi baru.',
                'aktivitas' => 'Proyek anggaran sederhana, membaca grafik data nyata, simulasi transaksi.',
            ],
        ],
        'Advanced' => [
            'Skills' => [
                'judul'    => 'Eksplorasi Konsep Matematika Lanjutan',
                'strategi' => 'Tantang siswa dengan konsep statistika dasar, probabilitas sederhana, dan pola bilangan. Libatkan dalam proyek berbasis data.',
                'aktivitas' => 'Proyek statistika kelas, soal olimpiade tingkat dasar, eksplorasi pola.',
            ],
            'Problem Solving' => [
                'judul'    => 'Tantangan dan Proyek Mandiri',
                'strategi' => 'Berikan masalah terbuka yang punya lebih dari satu solusi. Libatkan sebagai tutor sebaya.',
                'aktivitas' => 'Proyek investigasi, soal open-ended, tutor sebaya untuk teman NSI/Basic.',
            ],
            'Literasi Konteks' => [
                'judul'    => 'Numerasi dalam Konteks Kompleks dan Abstrak',
                'strategi' => 'Hubungkan numerasi ke isu global: lingkungan, ekonomi, teknologi. Gunakan data nyata dari media.',
                'aktivitas' => 'Analisis data berita, proyek lingkungan berbasis numerasi, presentasi data.',
            ],
        ],
    ];

    // ════════════════════════════════════════════════════════════════════
    // METHOD UTAMA — dipanggil AssessmentController setelah semua jawaban
    //
    // @param array $answers   Format: [ question_id => option_id, ... ]
    // @param array $questions Format: koleksi Question dengan relasi options
    // ════════════════════════════════════════════════════════════════════
    public function diagnose(array $answers, array $questions, $context = null): array
    {
        // ── 1. Kumpulkan data indikator dari setiap jawaban ───────────────
        $jawabanDetail = $this->kumpulkanJawabanDetail($answers, $questions);

        // ── 2. Hitung bobot per level dari semua indikator ────────────────
        $bobotLevel = $this->hitungBobotLevel($jawabanDetail);

        // ── 3. Tentukan level dominan (rule IF-THEN + handle draw) ────────
        $levelResult = $this->tentukanLevel($bobotLevel);

        // ── 4. Hitung probabilitas tiap level (weighted scoring → %) ──────
        $probabilitas = $this->hitungProbabilitas($bobotLevel);

        // --- HYBRID ML INTEGRATION ---
        $apiAnswers = $this->formatAnswersForApi($jawabanDetail);
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)
                ->post('http://127.0.0.1:5000/predict', [
                    'answers' => $apiAnswers
                ]);

            if ($response->successful()) {
                $mlData = $response->json();
                if (isset($mlData['status']) && $mlData['status'] === 'success') {
                    $levelResult['level'] = $mlData['level'];
                    $levelResult['draw_note'] = 'Klasifikasi oleh XGBoost AI Model';
                    $probabilitas = $mlData['probabilities'];
                }
            }
        } catch (\Exception $e) {
            // ML API gagal atau mati, biarkan jatuh kembali ke Rule-Based secara mulus (Fallback)
            \Illuminate\Support\Facades\Log::warning("XGBoost API Error: " . $e->getMessage());
        }
        // --- END HYBRID ML INTEGRATION ---

        // ── 5. Hitung akurasi (benar/salah untuk backward-compatibility) ──
        $totalBenar = collect($jawabanDetail)->where('kategori', 'tepat')->count();
        $totalSoal  = count($jawabanDetail);
        $akurasi    = $totalSoal > 0 ? round(($totalBenar / $totalSoal) * 100) : 0;

        // ── 6. Identifikasi dimensi paling lemah ──────────────────────────
        $dimensiLemah = $this->identifikasiDimensiLemah($jawabanDetail);

        // ── 7. Hitung skor per dimensi ────────────────────────────────────
        $skorDimensi = $this->hitungSkorDimensi($jawabanDetail);

        // ── 8. Ambil rekomendasi ──────────────────────────────────────────
        $rekomendasi = $this->ambilRekomendasi($levelResult['level'], $dimensiLemah);

        // ── 9. Bangun catatan AI ──────────────────────────────────────────
        $aiNote = $this->buildAiNote(
            $levelResult['level'],
            $akurasi,
            $dimensiLemah,
            $totalBenar,
            $totalSoal,
            $levelResult['draw_note'],
            $probabilitas
        );

        // ── 10. Return hasil lengkap ──────────────────────────────────────
        return [
            // Kompatibel dengan struktur controller teman
            'level'           => $levelResult['level'],
            'accuracy'        => $akurasi,
            'correct'         => $totalBenar,
            'total'           => $totalSoal,
            'topic_scores'    => $skorDimensi,      // pakai dimensi kita
            'weaknesses'      => [$dimensiLemah],
            'recommendations' => [
                $rekomendasi['judul'],
                $rekomendasi['strategi'],
                $rekomendasi['aktivitas'],
            ],
            'ai_note'         => $aiNote,
            'weak_topics'     => [$dimensiLemah],

            // Data tambahan sistem kita (tersedia untuk view jika dibutuhkan)
            'probabilitas'    => $probabilitas,
            'dimensi_lemah'   => $dimensiLemah,
            'draw_note'       => $levelResult['draw_note'],
            'detail_jawaban'  => $jawabanDetail,
        ];
    }

    // ── PRIVATE: Kumpulkan detail indikator per jawaban ───────────────────
    private function kumpulkanJawabanDetail(array $answers, array $questions): array
    {
        $detail = [];
        $qIndex = 1;

        foreach ($questions as $question) {
            $qId      = $question['id'];
            $optionId = $answers[$qId] ?? null;

            if (!$optionId) {
                $qIndex++;
                continue;
            }

            // Cari option yang dipilih
            $options   = $question['options'] ?? [];
            $optDipilih = collect($options)->firstWhere('id', $optionId);

            if (!$optDipilih) {
                $qIndex++;
                continue;
            }

            $indicator  = $optDipilih['indicator']   ?? null;
            $levelValue = $optDipilih['level_value']  ?? null;
            $isCorrect  = $optDipilih['is_correct']   ?? false;
            $dimensi    = $question['topic']           ?? 'Skills';
            $kategori   = $this->getKategori($indicator, $isCorrect);

            $detail[] = [
                'question_id' => $qId,
                'option_id'   => $optionId,
                'order'       => $question['order'] ?? $qIndex,
                'opt_order'   => $optDipilih['order'] ?? 1,
                'dimensi'     => $dimensi,
                'indicator'   => $indicator,
                'level_value' => $levelValue,
                'is_correct'  => $isCorrect,
                'kategori'    => $kategori,
            ];
            $qIndex++;
        }

        return $detail;
    }

    // ── PRIVATE: Format jawaban untuk Microservice API ─────────────────────
    private function formatAnswersForApi(array $jawabanDetail): array
    {
        $apiAnswers = [];
        foreach ($jawabanDetail as $j) {
            $qNum = $j['order'];
            $letter = match ((int) $j['opt_order']) {
                1 => 'A',
                2 => 'B',
                3 => 'C',
                default => 'A'
            };
            $apiAnswers[] = "{$qNum}_{$letter}";
        }
        return $apiAnswers;
    }

    // ── PRIVATE: Tentukan kategori dari indikator ─────────────────────────
    private function getKategori(?string $indicator, bool $isCorrect): string
    {
        if ($indicator && isset(self::INDICATOR_CATEGORY[$indicator])) {
            return self::INDICATOR_CATEGORY[$indicator];
        }
        // Fallback: kalau tidak ada indikator, pakai is_correct
        return $isCorrect ? 'tepat' : 'lemah';
    }

    // ── PRIVATE: Hitung total bobot per level dari semua jawaban ──────────
    private function hitungBobotLevel(array $jawabanDetail): array
    {
        $bobot = ['NSI' => 0, 'Basic' => 0, 'Proficient' => 0, 'Advanced' => 0];

        foreach ($jawabanDetail as $j) {
            $weights = self::CATEGORY_WEIGHTS[$j['kategori']] ?? self::CATEGORY_WEIGHTS['lemah'];
            foreach ($bobot as $level => $_) {
                $bobot[$level] += $weights[$level];
            }
        }

        return $bobot;
    }

    // ── PRIVATE: Tentukan level dominan — Rule-Based IF-THEN ─────────────
    private function tentukanLevel(array $bobotLevel): array
    {
        $nilaiMaks  = max($bobotLevel);
        $drawNote   = null;

        // Cari semua level dengan bobot tertinggi
        $levelMaks = array_keys(
            array_filter($bobotLevel, fn($v) => $v === $nilaiMaks)
        );

        if (count($levelMaks) === 1) {
            $finalLevel = $levelMaks[0];
        } else {
            // DRAW — ambil level paling konservatif (prioritas terendah)
            usort(
                $levelMaks,
                fn($a, $b) =>
                self::LEVEL_PRIORITY[$a] - self::LEVEL_PRIORITY[$b]
            );
            $finalLevel = $levelMaks[0];
            $parts = array_map(fn($l) => "{$l}={$bobotLevel[$l]}", $levelMaks);
            $drawNote = 'Draw: ' . implode(', ', $parts)
                . " → diambil {$finalLevel} (konservatif)";
        }

        return ['level' => $finalLevel, 'draw_note' => $drawNote];
    }

    // ── PRIVATE: Hitung probabilitas dari bobot (normalisasi ke 100%) ─────
    private function hitungProbabilitas(array $bobotLevel): array
    {
        $total = array_sum($bobotLevel);
        if ($total === 0) return array_fill_keys(array_keys($bobotLevel), 0.0);

        $prob = [];
        foreach ($bobotLevel as $level => $bobot) {
            $prob[$level] = round(($bobot / $total) * 100, 1);
        }
        return $prob;
    }

    // ── PRIVATE: Identifikasi dimensi dengan indikator lemah terbanyak ────
    private function identifikasiDimensiLemah(array $jawabanDetail): string
    {
        $lemahPerDimensi = [];

        foreach ($jawabanDetail as $j) {
            $dim = $j['dimensi'];
            if (!isset($lemahPerDimensi[$dim])) {
                $lemahPerDimensi[$dim] = 0;
            }
            if (in_array($j['kategori'], ['lemah', 'parsial'])) {
                $lemahPerDimensi[$dim]++;
            }
        }

        if (empty($lemahPerDimensi) || max($lemahPerDimensi) === 0) {
            return 'Skills'; // default kalau semua benar
        }

        arsort($lemahPerDimensi);
        return array_key_first($lemahPerDimensi);
    }

    // ── PRIVATE: Hitung skor per dimensi (untuk topic_scores di controller) ─
    private function hitungSkorDimensi(array $jawabanDetail): array
    {
        $stats = [];

        foreach ($jawabanDetail as $j) {
            $dim = $j['dimensi'];
            if (!isset($stats[$dim])) {
                $stats[$dim] = ['correct' => 0, 'total' => 0];
            }
            $stats[$dim]['total']++;
            if ($j['is_correct']) $stats[$dim]['correct']++;
        }

        $result = [];
        foreach ($stats as $dim => $stat) {
            $score    = $stat['total'] > 0
                ? round(($stat['correct'] / $stat['total']) * 100)
                : 0;
            $result[] = [
                'key'     => $dim,
                'name'    => self::DIMENSIONS[$dim] ?? $dim,
                'correct' => $stat['correct'],
                'total'   => $stat['total'],
                'score'   => $score,
                'is_weak' => $score < 60,
            ];
        }

        usort($result, fn($a, $b) => $a['score'] - $b['score']);
        return $result;
    }

    // ── PRIVATE: Ambil rekomendasi berdasarkan level + dimensi lemah ──────
    private function ambilRekomendasi(string $level, string $dimensi): array
    {
        return self::RECOMMENDATIONS[$level][$dimensi]
            ?? self::RECOMMENDATIONS[$level]['Skills']
            ?? [
                'judul'    => 'Lanjutkan Pembelajaran Terstruktur',
                'strategi' => 'Konsultasikan dengan guru untuk program pembelajaran yang sesuai.',
                'aktivitas' => 'Evaluasi berkala setiap 2 minggu.',
            ];
    }

    // ── PRIVATE: Bangun narasi catatan AI ─────────────────────────────────
    private function buildAiNote(
        string  $level,
        int     $akurasi,
        string  $dimensiLemah,
        int     $benar,
        int     $total,
        ?string $drawNote,
        array   $prob
    ): string {
        $dimLabel = self::DIMENSIONS[$dimensiLemah] ?? $dimensiLemah;
        $probStr  = "NSI {$prob['NSI']}% · Basic {$prob['Basic']}%"
            . " · Proficient {$prob['Proficient']}% · Advanced {$prob['Advanced']}%";

        $kalimatLevel = [
            'NSI'        => "Siswa memerlukan intervensi khusus dan pendampingan intensif.",
            'Basic'      => "Siswa memiliki pemahaman dasar yang perlu diperkuat.",
            'Proficient' => "Siswa menunjukkan kemampuan yang baik dan siap untuk tantangan lebih.",
            'Advanced'   => "Siswa menguasai numerasi dengan sangat baik.",
        ];

        $note = "Siswa menjawab {$benar} dari {$total} soal dengan benar ({$akurasi}%). "
            . "Level kemampuan: {$level}. "
            . ($kalimatLevel[$level] ?? '')
            . " Dimensi yang perlu perhatian lebih: {$dimLabel}. "
            . "Distribusi probabilitas level: {$probStr}.";

        if ($drawNote) {
            $note .= " Catatan: {$drawNote}.";
        }

        return $note;
    }
}
