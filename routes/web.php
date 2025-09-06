<?php

use App\Http\Controllers\VideoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Google OAuth routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware(['auth'])->group(function () {
    // Video viewing routes (all authenticated users)
    Route::get('videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('videos/{video}', [VideoController::class, 'show'])->name('videos.show');
    Route::post('videos/{video}/progress', [VideoController::class, 'updateProgress'])->name('videos.progress');
    Route::get('videos/{video}/status', [VideoController::class, 'status'])->name('videos.status');
    Route::get('progress', [VideoController::class, 'progress'])->name('videos.user-progress');
    Route::get('api/progress', [VideoController::class, 'progressApi'])->name('videos.user-progress-api');
    
    // Category browsing routes (all authenticated users)
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/videos', [AdminController::class, 'allVideos'])->name('videos');
    
    // Admin-only video management routes
    Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::get('/videos/{video}/edit', [VideoController::class, 'edit'])->name('videos.edit');
    Route::put('/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    
    // Category management routes
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
});

require __DIR__.'/auth.php';
