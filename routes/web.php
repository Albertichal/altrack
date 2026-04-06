<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/progress', [ProgressController::class, 'index'])->name('progress');

    Route::get('/log/last-exercise', [WorkoutController::class, 'getLastExercise'])->name('log.last-exercise');
    Route::get('/log', [WorkoutController::class, 'index'])->name('log');
    Route::post('/log', [WorkoutController::class, 'store'])->name('log.store');
    Route::delete('/workout/{workout}', [WorkoutController::class, 'destroy'])->name('workout.destroy');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile/name', [ProfileController::class, 'updateName'])->name('profile.name');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Admin
    Route::middleware('admin')->group(function () {

        Route::get('/admin', [AdminController::class, 'index'])->name('admin');

        // Route statis HARUS di atas route {user}
        Route::get('/admin/create-user', [AdminController::class, 'showCreateUser'])->name('admin.create-user');
        Route::post('/admin/create-user', [AdminController::class, 'storeUser'])->name('admin.store-user');

        // Route dengan {user} parameter
        Route::post('/admin/{user}/toggle', [AdminController::class, 'toggleActive'])->name('admin.toggle');
        Route::post('/admin/{user}/add-access', [AdminController::class, 'addAccess'])->name('admin.add-access');
        Route::post('/admin/{user}/reset-access', [AdminController::class, 'resetAccess'])->name('admin.reset-access');
        Route::post('/admin/{user}/delete-avatar', [AdminController::class, 'deleteAvatar'])->name('admin.delete-avatar');
        Route::post('/admin/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.reset-password');
        Route::post('/admin/{user}/update-name', [AdminController::class, 'updateName'])->name('admin.update-name');
        Route::delete('/admin/{user}/delete', [AdminController::class, 'deleteUser'])->name('admin.delete');
    });
});
