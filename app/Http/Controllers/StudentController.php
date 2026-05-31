<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\School;
use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        $schools = School::withCount('students')
            ->orderBy('name', 'asc')
            ->get();

        $students = Student::with(['school', 'latestDiagnosis'])
            ->orderBy('name', 'asc')
            ->get();

        $summary = [
            'total_siswa'   => $students->count(),
            'total_sekolah' => $schools->count(),
            'sudah_asesmen' => $students->filter(fn($s) => $s->latestDiagnosis !== null)->count(),
            'belum_asesmen' => $students->filter(fn($s) => $s->latestDiagnosis === null)->count(),
        ];

        return view('students.index', compact('schools', 'students', 'summary'));
    }

    public function detail(int $id)
    {
        try {
            $student = Student::with(['school'])->findOrFail($id);

            // Ambil semua riwayat diagnosis, diurutkan terbaru
            $diagnosisAll = $student->diagnosisResults()
                ->orderBy('created_at', 'desc')
                ->get();

            $latest = $diagnosisAll->first();

            $history = $diagnosisAll->map(fn($r) => [
                'date'     => $r->created_at->format('d M Y, H:i'),
                'level'    => $r->level,
                'accuracy' => $r->accuracy,
                'correct'  => $r->correct_count,
                'total'    => $r->total_questions,
            ])->values()->toArray();

            return response()->json([
                'id'              => $student->id,
                'name'            => $student->name,
                'school'          => $student->school->name,
                'level'           => $latest?->level,
                'accuracy'        => $latest?->accuracy,
                'correct'         => $latest?->correct_count,
                'total'           => $latest?->total_questions,
                'topic_scores'    => $latest?->topic_scores   ?? [],
                'weaknesses'      => $latest?->weaknesses     ?? [],
                'recommendations' => $latest?->recommendations ?? [],
                'ai_note'         => $latest?->ai_note        ?? '',
                'history'         => $history,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
