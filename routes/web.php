<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResearchController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Auth;

// 1. Home Route Redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin' 
            ? redirect()->route('admin.dashboard')
            : redirect()->route('voter.dashboard');
    }
    return redirect()->route('login');
});

// 2. Authentication Paths (Guest / Temp OTP routes)
Route::middleware([])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/otp', [AuthController::class, 'showOtp'])->name('otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// 3. Voter Dedicated Paths (Auth protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [VotingController::class, 'dashboard'])->name('voter.dashboard');
    Route::post('/vote', [VotingController::class, 'vote'])->name('vote.cast');
    Route::get('/vote/success', [VotingController::class, 'success'])->name('vote.success');
});

// 4. Admin Dedicated Paths (AdminMiddleware protected)
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/candidates', [AdminController::class, 'createCandidate'])->name('admin.candidate.create');
    Route::post('/admin/candidates/{id}/update', [AdminController::class, 'updateCandidate'])->name('admin.candidate.update');
    Route::post('/admin/candidates/{id}/delete', [AdminController::class, 'deleteCandidate'])->name('admin.candidate.delete');
    Route::post('/admin/toggle-voting', [AdminController::class, 'toggleVoting'])->name('admin.toggle-voting');
    Route::post('/admin/categories', [AdminController::class, 'createCategory'])->name('admin.category.create');
    Route::post('/admin/categories/{id}/delete', [AdminController::class, 'deleteCategory'])->name('admin.category.delete');
    Route::post('/admin/voters/{id}/update', [AdminController::class, 'updateVoter'])->name('admin.voter.update');
    Route::post('/admin/voters/{id}/delete', [AdminController::class, 'deleteVoter'])->name('admin.voter.delete');
});

// 5. Research & Lab Sandbox Paths (Publicly accessible for easy academic demo)
Route::get('/research-lab', [ResearchController::class, 'index'])->name('research.lab');
Route::post('/research-lab/reset', [ResearchController::class, 'resetDb'])->name('research.reset');
Route::get('/security-lab', [ResearchController::class, 'securityLab'])->name('security.lab');
Route::post('/security-lab/test', [AuthController::class, 'testSqlInjection'])->name('security.test');
