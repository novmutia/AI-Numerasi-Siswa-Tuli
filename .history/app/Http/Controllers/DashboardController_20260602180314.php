<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Student;
use App\Models\School;
use App\Models\DiagnosisResult;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Statistik utama dari database ─────────────────────────────
        $totalSiswa     = Student::count();
        $totalSekolah   = School::count();
        $asesmenSelesai = DiagnosisResult::count();

        // Rata-rata akurasi dari semua hasil diagnosis
        $rataAkurasi = $asesmenSelesai > 0
            ? round(DiagnosisResult::avg('accuracy')) . '%'
            : '0%';

        $stats = [
            'total_siswa'     => $totalSiswa,
            'total_sekolah'   => $totalSekolah,
            'asesmen_selesai' => $asesmenSelesai,
            'rata_skor'       => $rataAkurasi,
        ];

        // ── Distribusi level dari semua hasil ─────────────────────────
        $distribusiLevel = DiagnosisResult::selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level')
            ->toArray();

        // Pastikan semua level ada meski nilainya 0
        $distribusiLevel = array_merge(
            ['NSI' => 0, 'Basic' => 0, 'Proficient' => 0, 'Advanced' => 0],
            $distribusiLevel
        );

        // ── 5 hasil asesmen terbaru ────────────────────────────────────
        $hasilTerbaru = DiagnosisResult::with('student.school')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'distribusiLevel',
            'hasilTerbaru'
        ));
    }
}
