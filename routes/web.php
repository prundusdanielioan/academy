<?php

use App\Http\Controllers\VideoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Video routes
    Route::resource('videos', VideoController::class);
    
    // Additional video routes
    Route::post('videos/{video}/progress', [VideoController::class, 'updateProgress'])->name('videos.progress');
    Route::get('videos/{video}/status', [VideoController::class, 'status'])->name('videos.status');
    Route::get('progress', [VideoController::class, 'progress'])->name('videos.user-progress');
    
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
});

require __DIR__.'/auth.php';
