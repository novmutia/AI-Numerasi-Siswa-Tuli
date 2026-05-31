<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StatisticsController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Assessment
Route::prefix('assessment')->name('assessment.')->group(function () {
    Route::get('/start',             [AssessmentController::class, 'start'])->name('start');
    Route::post('/store-student',    [AssessmentController::class, 'storeStudent'])->name('store-student');
    Route::get('/{token}/soal/{no}', [AssessmentController::class, 'questions'])->name('questions');
    Route::post('/answer',           [AssessmentController::class, 'submitAnswer'])->name('answer');
    Route::get('/result/{result}',   [AssessmentController::class, 'result'])->name('result');
});

// Students
Route::prefix('students')->name('students.')->group(function () {
    Route::get('/',           [StudentController::class, 'index'])->name('index');
    Route::get('/{id}/detail',[StudentController::class, 'detail'])->name('detail');
});

// Statistics (stub)
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

Route::fallback(fn() => redirect()->route('dashboard')->with('error', 'Halaman tidak ditemukan.'));
