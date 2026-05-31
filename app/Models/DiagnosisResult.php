<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosisResult extends Model
{
    protected $fillable = [
        'assessment_session_id',
        'student_id',
        'level',
        'accuracy',
        'correct_count',
        'total_questions',
        'topic_scores',
        'weaknesses',
        'recommendations',
        'ai_note',
    ];

    protected $casts = [
        'topic_scores'    => 'array',
        'weaknesses'      => 'array',
        'recommendations' => 'array',
    ];

    public function student()           { return $this->belongsTo(Student::class); }
    public function assessmentSession() { return $this->belongsTo(AssessmentSession::class); }
}
