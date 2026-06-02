<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\DiagnosisResult;
use App\Models\Student;
use App\Models\School;
use App\Models\AssessmentSession;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        // ── Filter ────────────────────────────────────────────────────────
        $schoolId = $request->query('school_id');

        // ── Data sekolah untuk dropdown filter ────────────────────────────
        $schools = School::orderBy('name')->get();

        // ── Total keseluruhan ─────────────────────────────────────────────
        $totalSiswa    = Student::count();
        $totalAsesmen  = DiagnosisResult::count();

        // ── Distribusi level (untuk pie/bar chart) ────────────────────────
        $distribusiLevel = DiagnosisResult::selectRaw('level, COUNT(*) as total')
            ->when($schoolId, function ($q) use ($schoolId) {
                $q->whereHas('student', fn($s) => $s->where('school_id', $schoolId));
            })
            ->groupBy('level')
            ->orderByRaw("FIELD(level, 'NSI', 'Basic', 'Proficient', 'Advanced')")
            ->pluck('total', 'level')
            ->toArray();

        // Pastikan semua level ada meskipun nilainya 0
        $distribusiLevel = array_merge(
            ['NSI' => 0, 'Basic' => 0, 'Proficient' => 0, 'Advanced' => 0],
            $distribusiLevel
        );

        // ── Rata-rata akurasi per level ────────────────────────────────────
        $rataAkurasi = DiagnosisResult::selectRaw('level, ROUND(AVG(accuracy), 1) as rata')
            ->when($schoolId, function ($q) use ($schoolId) {
                $q->whereHas('student', fn($s) => $s->where('school_id', $schoolId));
            })
            ->groupBy('level')
            ->pluck('rata', 'level')
            ->toArray();

        // ── Hasil terbaru (10 terakhir) ───────────────────────────────────
        $hasilTerbaru = DiagnosisResult::with(['student.school'])
            ->when($schoolId, function ($q) use ($schoolId) {
                $q->whereHas('student', fn($s) => $s->where('school_id', $schoolId));
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($d) => [
                'nama'    => $d->student->name,
                'sekolah' => $d->student->school->name ?? '-',
                'level'   => $d->level,
                'akurasi' => $d->accuracy,
                'tanggal' => $d->created_at->format('d M Y'),
            ]);

        // ── Topik/dimensi paling banyak lemah ─────────────────────────────
        // weaknesses disimpan sebagai JSON array di diagnosis_results
        $semuaWeakness = DiagnosisResult::when($schoolId, function ($q) use ($schoolId) {
            $q->whereHas('student', fn($s) => $s->where('school_id', $schoolId));
        })
            ->pluck('weaknesses')
            ->flatten()
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->toArray();

        // ── Statistik per sekolah ─────────────────────────────────────────
        $perSekolah = School::withCount('students')
            ->with(['students.diagnosisResults' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->when($schoolId, fn($q) => $q->where('id', $schoolId))
            ->orderBy('name')
            ->get()
            ->map(function ($school) {
                $results = DiagnosisResult::whereHas(
                    'student',
                    fn($q) => $q->where('school_id', $school->id)
                )->get();

                return [
                    'nama'         => $school->name,
                    'total_siswa'  => $school->students_count,
                    'total_asesmen' => $results->count(),
                    'rata_akurasi' => $results->avg('accuracy') ? round($results->avg('accuracy'), 1) : '-',
                    'level_dominan' => $results->groupBy('level')->map->count()->sortDesc()->keys()->first() ?? '-',
                ];
            });

        return view('statistics', compact(
            'schools',
            'schoolId',
            'totalSiswa',
            'totalAsesmen',
            'distribusiLevel',
            'rataAkurasi',
            'hasilTerbaru',
            'semuaWeakness',
            'perSekolah'
        ));
    }
}
