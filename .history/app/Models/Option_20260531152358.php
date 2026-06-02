<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order',
        'indicator',    // ← tambah ini
        'level_value',  // ← tambah ini
    ];
    protected $casts    = ['is_correct' => 'boolean'];
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
