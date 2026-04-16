<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ResumeScreeningController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'Admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('hr.dashboard');
    }

    return redirect()->route('login');
});

Auth::routes();

Route::get('/candidate/{candidate}/resume', [CandidateController::class, 'downloadResume'])
    ->name('candidates.resume')
    ->middleware(['auth', 'role:Admin,HR']);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('candidates', CandidateController::class);
    Route::post('/screening/update', [ResumeScreeningController::class, 'update'])->name('screening.update');
    Route::post('/evaluation', [EvaluationController::class, 'store'])->name('evaluation.store');
});

Route::prefix('hr')->name('hr.')->middleware(['auth', 'role:HR'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('candidates', CandidateController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('/screening/update', [ResumeScreeningController::class, 'update'])->name('screening.update');
    Route::post('/evaluation', [EvaluationController::class, 'store'])->name('evaluation.store');
});

Route::post('/evaluation/generate-ai-task', [EvaluationController::class, 'generateAiTaskEvaluation'])
    ->name('evaluation.generate.ai.task')
    ->middleware(['auth', 'role:Admin,HR']);

Route::get('/test-gemini-api', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $service = new \App\Services\GeminiService();
    $result = $service->generateTaskEvaluation(
        'Good communication skills, positive attitude',
        8,
        'Strong problem-solving abilities, good code quality',
        7
    );
    
    return response()->json([
        'api_key_set' => !empty(env('GEMINI_API_KEY')),
        'result' => $result,
        'env_key_preview' => substr(env('GEMINI_API_KEY'), 0, 10) . '...'
    ]);
})->middleware('auth');