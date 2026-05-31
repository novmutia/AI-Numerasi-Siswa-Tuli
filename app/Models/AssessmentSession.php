<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentSession extends Model
{
    protected $fillable = ['token', 'student_id', 'question_ids', 'started_at', 'finished_at'];

    protected $casts = [
        'question_ids' => 'array',      // simpan sebagai JSON array
        'started_at'   => 'datetime',
        'finished_at'  => 'datetime',
    ];

    public function student()         { return $this->belongsTo(Student::class); }
    public function answers()         { return $this->hasMany(Answer::class); }
    public function diagnosisResult() { return $this->hasOne(DiagnosisResult::class); }

    public function isFinished(): bool { return !is_null($this->finished_at); }
}
