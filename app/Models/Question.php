<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_text',
        'topic',
        'difficulty',   // easy | medium | hard
        'video_path',   // storage/videos/soal_01.mp4
        'subtitle_path',// storage/subtitles/soal_01.vtt
        'is_active',
        'order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function options() { return $this->hasMany(Option::class)->orderBy('order'); }

    public function correctOption()
    {
        return $this->hasOne(Option::class)->where('is_correct', true);
    }
}
