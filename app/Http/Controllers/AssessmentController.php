<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use App\Services\DiagnosisService;
use App\Models\School;
use App\Models\Student;
use App\Models\Question;
use App\Models\AssessmentSession;
use App\Models\Answer;
use App\Models\DiagnosisResult;

class AssessmentController extends Controller
{
    protected DiagnosisService $diagnosis;

    public function __construct(DiagnosisService $diagnosis)
    {
        $this->diagnosis = $diagnosis;
    }

    // STEP 1 — Form mulai asesmen
    public function start()
    {
        $schools = School::orderBy('name', 'asc')->get();
        return view('assessment.start', compact('schools'));
    }

    // STEP 2 — Simpan siswa & buat sesi
    public function storeStudent(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|min:2|max:100',
            'school_id'    => 'required|exists:schools,id',
        ], [
            'student_name.required' => 'Nama siswa wajib diisi.',
            'student_name.min'      => 'Nama siswa minimal 2 karakter.',
            'school_id.required'    => 'Pilih sekolah terlebih dahulu.',
            'school_id.exists'      => 'Sekolah tidak ditemukan.',
        ]);

        $student = Student::firstOrCreate(
            [
                'name'      => $request->student_name,
                'school_id' => (int) $request->school_id,
            ],
            [
                'name'      => $request->student_name,
                'school_id' => (int) $request->school_id,
            ]
        );

        // where() dengan 3 argumen eksplisit
        $questions = Question::where('is_active', '=', true)
            ->with('options')
            ->inRandomOrder()
            ->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'Belum ada soal tersedia. Hubungi administrator.');
        }

        $session = AssessmentSession::create([
            'token'        => Str::random(40),
            'student_id'   => $student->id,
            'question_ids' => $questions->pluck('id')->toArray(),
            'started_at'   => now(),
            'finished_at'  => null,
        ]);

        return redirect()->route('assessment.questions', [
            'token' => $session->token,
            'no'    => 1,
        ]);
    }

    // STEP 3 — Tampilkan soal
    public function questions(string $token, int $no = 1)
    {
        $session = AssessmentSession::where('token', '=', $token)
            ->whereNull('finished_at')
            ->firstOrFail();

        $questionIds    = (array) $session->question_ids;
        $totalQuestions = count($questionIds);

        if ($no < 1 || $no > $totalQuestions) {
            return redirect()->route('assessment.questions', [
                'token' => $token,
                'no'    => 1,
            ]);
        }

        $question = Question::with('options')->findOrFail($questionIds[$no - 1]);

        $savedAnswers = Answer::where('assessment_session_id', '=', $session->id)
            ->pluck('option_id', 'question_id')
            ->toArray();

        $student = $session->student;

        return view('assessment.questions', [
            'sessionToken'   => $token,
            'question'       => $question,
            'currentNumber'  => $no,
            'totalQuestions' => $totalQuestions,
            'studentName'    => $student->name,
            'schoolName'     => $student->school->name,
            'savedAnswers'   => $savedAnswers,
        ]);
    }

    // STEP 4 — Simpan jawaban
    public function submitAnswer(Request $request)
    {
        $request->validate([
            'session_token'   => 'required|string',
            'question_id'     => 'required|exists:questions,id',
            'option_id'       => 'required|exists:options,id',
            'question_number' => 'required|integer|min:1',
        ]);

        $session = AssessmentSession::where('token', '=', $request->session_token)
            ->whereNull('finished_at')
            ->firstOrFail();

        Answer::updateOrCreate(
            [
                'assessment_session_id' => $session->id,
                'question_id'           => (int) $request->question_id,
            ],
            [
                'option_id' => (int) $request->option_id,
            ]
        );

        $totalQuestions = count((array) $session->question_ids);
        $nextNo         = (int) $request->question_number + 1;

        if ($nextNo <= $totalQuestions) {
            return redirect()->route('assessment.questions', [
                'token' => $request->session_token,
                'no'    => $nextNo,
            ]);
        }

        return $this->processResult($session);
    }

    // STEP 5 — Proses diagnosis AI
    private function processResult(AssessmentSession $session)
    {
        $session->update(['finished_at' => now()]);

        $answers = Answer::where('assessment_session_id', '=', $session->id)
            ->pluck('option_id', 'question_id')
            ->toArray();

        $questions = Question::with('options')
            ->whereIn('id', (array) $session->question_ids)
            ->get()
            ->map(function ($q) {
                $correctOption = $q->options->firstWhere('is_correct', true);
                return [
                    'id'                => $q->id,
                    'topic'             => $q->topic,
                    'correct_option_id' => $correctOption?->id,
                    // Kirim data options lengkap ke DiagnosisService
                    // agar bisa baca indicator dan level_value tiap pilihan
                    'options'           => $q->options->map(fn($o) => [
                        'id'          => $o->id,
                        'is_correct'  => $o->is_correct,
                        'indicator'   => $o->indicator,
                        'level_value' => $o->level_value,
                    ])->toArray(),
                ];
            })
            ->toArray();

        $diagnosis = $this->diagnosis->diagnose($answers, $questions);

        $diagnosisResult = DiagnosisResult::create([
            'assessment_session_id' => $session->id,
            'student_id'            => $session->student_id,
            'level'                 => $diagnosis['level'],
            'accuracy'              => $diagnosis['accuracy'],
            'correct_count'         => $diagnosis['correct'],
            'total_questions'       => $diagnosis['total'],
            'topic_scores'          => $diagnosis['topic_scores'],
            'weaknesses'            => $diagnosis['weaknesses'],
            'recommendations'       => $diagnosis['recommendations'],
            'ai_note'               => $diagnosis['ai_note'],
        ]);

        return redirect()->route('assessment.result', ['result' => $diagnosisResult->id]);
    }

    // STEP 6 — Tampilkan hasil
    public function result(int $id)
    {
        $diagnosisResult = DiagnosisResult::with(['student.school'])
            ->findOrFail($id);

        $result = [
            'student_name'    => $diagnosisResult->student->name,
            'school_name'     => $diagnosisResult->student->school->name,
            'level'           => $diagnosisResult->level,
            'accuracy'        => $diagnosisResult->accuracy,
            'correct'         => $diagnosisResult->correct_count,
            'total'           => $diagnosisResult->total_questions,
            'topic_scores'    => $diagnosisResult->topic_scores,
            'weaknesses'      => $diagnosisResult->weaknesses,
            'recommendations' => $diagnosisResult->recommendations,
            'ai_note'         => $diagnosisResult->ai_note,
        ];

        return view('assessment.result', compact('result'));
    }
}
