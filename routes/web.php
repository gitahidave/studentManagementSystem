<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'show']);

// Student Management Route
Route::get('/students', [StudentController::class, 'index']);