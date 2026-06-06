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

        // ── Timeline: 8 aktivitas terbaru ──────────────────────────────
        $timeline = DiagnosisResult::with('student.school')
            ->latest()
            ->take(8)
            ->get();

        // ── Detail data untuk popup ────────────────────────────────────
        // Daftar siswa (max 10)
        $detailSiswa = Student::with('school')->latest()->take(10)->get()
            ->map(fn($s) => [
                'nama'    => $s->name,
                'sekolah' => $s->school->name ?? '—',
            ]);

        // Daftar sekolah
        $detailSekolah = School::withCount('students')->get()
            ->map(fn($s) => [
                'nama'  => $s->name,
                'siswa' => $s->students_count,
            ]);

        // Asesmen terbaru (max 10)
        $detailAsesmen = DiagnosisResult::with('student.school')->latest()->take(10)->get()
            ->map(fn($r) => [
                'nama'    => $r->student->name ?? '—',
                'sekolah' => $r->student->school->name ?? '—',
                'level'   => $r->level,
                'akurasi' => $r->accuracy,
                'tanggal' => $r->created_at->format('d M Y'),
            ]);

        // Akurasi per level
        $detailAkurasi = DiagnosisResult::selectRaw('level, ROUND(AVG(accuracy)) as rata, COUNT(*) as jml')
            ->groupBy('level')
            ->get()
            ->map(fn($r) => [
                'level' => $r->level,
                'rata'  => $r->rata,
                'jml'   => $r->jml,
            ]);

        // Siswa per level
        $detailPerLevel = [];
        foreach (['NSI', 'Basic', 'Proficient', 'Advanced'] as $lvl) {
            $detailPerLevel[$lvl] = DiagnosisResult::with('student.school')
                ->where('level', $lvl)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn($r) => [
                    'nama'    => $r->student->name ?? '—',
                    'sekolah' => $r->student->school->name ?? '—',
                    'akurasi' => $r->accuracy,
                    'tanggal' => $r->created_at->format('d M Y'),
                ]);
        }

        $popupData = [
            'total_siswa'     => $detailSiswa,
            'total_sekolah'   => $detailSekolah,
            'asesmen_selesai' => $detailAsesmen,
            'rata_skor'       => $detailAkurasi,
            'level'           => $detailPerLevel,
        ];

        return view('dashboard', compact(
            'stats',
            'distribusiLevel',
            'hasilTerbaru',
            'timeline',
            'popupData'
        ));
    }
}
