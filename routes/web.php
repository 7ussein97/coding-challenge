<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\JudgeController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Judge\DashboardController as JudgeDashboard;
use App\Http\Controllers\Judge\SubmissionController as JudgeSubmissionController;
use App\Http\Controllers\Team\DashboardController as TeamDashboard;
use App\Http\Controllers\Team\SubmissionController as TeamSubmissionController;
use App\Http\Controllers\LeaderboardController;

// ── Root ──────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return match(auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'judge' => redirect()->route('judge.dashboard'),
        'team'  => redirect()->route('team.dashboard'),
        default => redirect()->route('login'),
    };
});

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// ── Leaderboard (public) ──────────────────────────────────────────────────────
Route::get('/leaderboard',      [LeaderboardController::class, 'index'])->name('leaderboard.index');
Route::get('/leaderboard/data', [LeaderboardController::class, 'data'])->name('leaderboard.data');

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('/teams',         [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/create',  [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams',        [TeamController::class, 'store'])->name('teams.store');
    Route::delete('/teams/{team}',[TeamController::class, 'destroy'])->name('teams.destroy');

    Route::get('/judges',          [JudgeController::class, 'index'])->name('judges.index');
    Route::get('/judges/create',   [JudgeController::class, 'create'])->name('judges.create');
    Route::post('/judges',         [JudgeController::class, 'store'])->name('judges.store');
    Route::delete('/judges/{judge}',[JudgeController::class, 'destroy'])->name('judges.destroy');

    Route::get('/questions',              [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/questions/create',       [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions',             [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}',   [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}',[QuestionController::class, 'destroy'])->name('questions.destroy');
});

// ── Judge ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:judge'])->prefix('judge')->name('judge.')->group(function () {

    Route::get('/dashboard', [JudgeDashboard::class, 'index'])->name('dashboard');

    Route::get('/submissions',                              [JudgeSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}',                 [JudgeSubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/submissions/{submission}/download',        [JudgeSubmissionController::class, 'download'])->name('submissions.download');
    Route::post('/submissions/{submission}/unlock',         [JudgeSubmissionController::class, 'unlock'])->name('submissions.unlock');
    Route::post('/submissions/{submission}/review',         [JudgeSubmissionController::class, 'review'])->name('submissions.review');
});

// ── Team ──────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:team'])->prefix('team')->name('team.')->group(function () {

    Route::get('/dashboard', [TeamDashboard::class, 'index'])->name('dashboard');

    Route::get('/questions',                          [TeamSubmissionController::class, 'questions'])->name('questions.index');
    Route::get('/questions/{question}/submit',        [TeamSubmissionController::class, 'create'])->name('questions.submit');
    Route::post('/questions/{question}/submit',       [TeamSubmissionController::class, 'store'])->name('questions.submit.store');
    Route::get('/submissions',                              [TeamSubmissionController::class, 'history'])->name('submissions.history');
    Route::get('/submissions/{submission}/download',        [TeamSubmissionController::class, 'download'])->name('submissions.download');
});
