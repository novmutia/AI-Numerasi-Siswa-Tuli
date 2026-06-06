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
        // ── Data sekolah untuk dropdown ────────────────────────────
        $schools = School::orderBy('name')->get();

        // ── Data mentah untuk initial load (Semua Sekolah) ─────────
        $totalSiswa    = Student::count();
        $totalAsesmen  = DiagnosisResult::count();

        $distribusiLevel = DiagnosisResult::selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->orderByRaw("FIELD(level, 'NSI', 'Basic', 'Proficient', 'Advanced')")
            ->pluck('total', 'level')
            ->toArray();

        $distribusiLevel = array_merge(
            ['NSI' => 0, 'Basic' => 0, 'Proficient' => 0, 'Advanced' => 0],
            $distribusiLevel
        );

        $rataAkurasi = DiagnosisResult::selectRaw('level, ROUND(AVG(accuracy), 1) as rata')
            ->groupBy('level')
            ->pluck('rata', 'level')
            ->toArray();

        $hasilTerbaru = DiagnosisResult::with(['student.school'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($d) => [
                'nama'    => $d->student->name,
                'sekolah' => $d->student->school->name ?? '-',
                'level'   => $d->level,
                'akurasi' => $d->accuracy,
                'tanggal' => $d->created_at->format('d M Y'),
                'dimensi_lemah' => count($d->weaknesses ?? []) > 0 ? implode(', ', array_slice($d->weaknesses, 0, 2)) : '—'
            ]);

        $semuaWeakness = DiagnosisResult::pluck('weaknesses')
            ->flatten()
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->toArray();

        $perSekolah = School::withCount('students')
            ->with(['students.diagnosisResults' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($school) {
                $results = DiagnosisResult::whereHas(
                    'student',
                    fn($q) => $q->where('school_id', $school->id)
                )->get();

                return [
                    'id'           => $school->id,
                    'nama'         => $school->name,
                    'total_siswa'  => $school->students_count,
                    'total_asesmen' => $results->count(),
                    'rata_akurasi' => $results->avg('accuracy') ? round($results->avg('accuracy'), 1) : '-',
                    'level_dominan' => $results->groupBy('level')->map->count()->sortDesc()->keys()->first() ?? '-',
                ];
            });

        // ── Data JS ────────────────────────────────────────────────
        $allStudentsJS = Student::select('id', 'school_id')->get();
        $allResultsJS = DiagnosisResult::with(['student.school'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($d) => [
                'id'            => $d->id,
                'school_id'     => $d->student->school_id ?? null,
                'level'         => $d->level,
                'accuracy'      => $d->accuracy,
                'weaknesses'    => $d->weaknesses ?? [],
                'nama'          => $d->student->name ?? '—',
                'sekolah'       => $d->student->school->name ?? '-',
                'tanggal'       => $d->created_at->format('d M Y'),
                'dimensi_lemah' => count($d->weaknesses ?? []) > 0 ? implode(', ', array_slice($d->weaknesses, 0, 2)) : '—'
            ]);
            
        // We set schoolId as null for view compatibility
        $schoolId = null;

        return view('statistics', compact(
            'schools',
            'schoolId',
            'totalSiswa',
            'totalAsesmen',
            'distribusiLevel',
            'rataAkurasi',
            'hasilTerbaru',
            'semuaWeakness',
            'perSekolah',
            'allStudentsJS',
            'allResultsJS'
        ));
    }
}
