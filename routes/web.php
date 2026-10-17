<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('students', StudentController::class);

    Route::resource('courses', CourseController::class)->except(['show']);

    Route::resource('fees', FeeController::class)->only(['index', 'create', 'store']);

    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);

    Route::view('/reports', 'pages.placeholder', [
        'title' => 'Reports',
        'description' => 'View student management reports from this section.',
    ])->name('reports.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::view('/settings', 'pages.placeholder', [
        'title' => 'Settings',
        'description' => 'Account settings are available from your profile.',
        'actionUrl' => '/profile',
        'actionLabel' => 'Manage profile',
    ])->name('settings');
});

require __DIR__.'/auth.php';
