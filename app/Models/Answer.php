<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['assessment_session_id', 'question_id', 'option_id'];

    public function assessmentSession() { return $this->belongsTo(AssessmentSession::class); }
    public function question()          { return $this->belongsTo(Question::class); }
    public function option()            { return $this->belongsTo(Option::class); }
}
