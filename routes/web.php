<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DJController as AdminDJController;
use App\Http\Controllers\Admin\DateController as AdminDateController;
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
    
    // Admin DJ resource routes (omit separate create page — modal used)
    Route::resource('admin/djs', AdminDJController::class)->except(['create'])->names([
        'index' => 'admin.djs.index',
        'store' => 'admin.djs.store',
        'edit' => 'admin.djs.edit',
        'update' => 'admin.djs.update',
        'destroy' => 'admin.djs.destroy',
    ]);

    // Admin Date resource routes (omit show, create pages — modal used)
    Route::resource('admin/dates', AdminDateController::class)->except(['show', 'create', 'edit'])->names([
        'index' => 'admin.dates.index',
        'store' => 'admin.dates.store',
        'update' => 'admin.dates.update',
        'destroy' => 'admin.dates.destroy',
    ]);
});

require __DIR__.'/auth.php';
