<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DJController as AdminDJController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    $totalDJs = 0; // Placeholder value for total DJs
    $totalTimeSlots = 0; // Placeholder value for total time slots
    $activeUsers = 0; // Placeholder value for active users
    $notifications = 0; // Placeholder value for notifications
    $recentActivities = []; // Empty array for recent activities

    return view('admin.dashboard', compact('totalDJs', 'totalTimeSlots', 'activeUsers', 'notifications', 'recentActivities'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin DJ resource routes
    Route::resource('admin/djs', AdminDJController::class)->names([
        'index' => 'admin.djs.index',
        'create' => 'admin.djs.create',
        'store' => 'admin.djs.store',
        'edit' => 'admin.djs.edit',
        'update' => 'admin.djs.update',
        'destroy' => 'admin.djs.destroy',
    ]);
});

require __DIR__.'/auth.php';
