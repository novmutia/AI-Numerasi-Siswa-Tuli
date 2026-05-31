<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'school_id', 'gender', 'birth_year', 'notes'];

    public function school()             { return $this->belongsTo(School::class); }
    public function assessmentSessions() { return $this->hasMany(AssessmentSession::class); }
    public function diagnosisResults()   { return $this->hasMany(DiagnosisResult::class); }

    // Hasil diagnosis terbaru
    public function latestDiagnosis()
    {
        return $this->hasOne(DiagnosisResult::class)->latestOfMany();
    }
}
