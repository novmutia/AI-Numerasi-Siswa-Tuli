<?php
// FILE: database/migrations/2024_01_01_000001_create_numerasi_tuli_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── schools ──────────────────────────────────────────────────────
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });

        // ── students ─────────────────────────────────────────────────────
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->unsignedSmallInteger('birth_year')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── questions ────────────────────────────────────────────────────
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->string('topic');          // bilangan_bulat | pecahan | geometri | statistika | aljabar
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('video_path')->nullable();
            $table->string('subtitle_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // ── options ──────────────────────────────────────────────────────
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });

        // ── assessment_sessions ──────────────────────────────────────────
        Schema::create('assessment_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('token', 60)->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->json('question_ids');      // urutan soal yang diacak
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        // ── answers ──────────────────────────────────────────────────────
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['assessment_session_id', 'question_id']); // 1 jawaban per soal per sesi
        });

        // ── diagnosis_results ─────────────────────────────────────────────
        Schema::create('diagnosis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('level', ['NSI', 'Basic', 'Proficient', 'Advanced']);
            $table->unsignedTinyInteger('accuracy');     // 0–100
            $table->unsignedTinyInteger('correct_count');
            $table->unsignedTinyInteger('total_questions');
            $table->json('topic_scores');
            $table->json('weaknesses');
            $table->json('recommendations');
            $table->text('ai_note');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosis_results');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('assessment_sessions');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('students');
        Schema::dropIfExists('schools');
    }
};
