<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Public routes
Route::get('/', function () {
    return view('home');
});

// Auth routes
Route::get('/login', fn () => abort(404))->name('login');
Route::post('/login', fn () => abort(404))->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Lecturer routes
Route::prefix('lecturer')->middleware(['auth', 'role:lecturer,gvhd,gvpb'])->group(function () {
    Route::get('/dashboard', function () {
        return view('lecturer.dashboard');
    })->name('lecturer.dashboard');
});

// Student routes
Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');

    // File submission routes
    Route::get('/file-submissions/{id}/view', [\App\Http\Controllers\Student\FileSubmissionController::class, 'view'])
        ->name('student.file-submission.view');
    Route::get('/file-submissions/{id}/download', [\App\Http\Controllers\Student\FileSubmissionController::class, 'download'])
        ->name('student.file-submission.download');
});
