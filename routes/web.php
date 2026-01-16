<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DJController as AdminDJController;
use App\Http\Controllers\Admin\DateController as AdminDateController;
use App\Http\Controllers\Admin\DashboardController;
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


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin DJ resource routes (omit separate create page — modal used)
    Route::get('admin/djs/list', [AdminDJController::class, 'list'])->name('admin.djs.list');
    Route::get('admin/djs/available-dates', [AdminDJController::class, 'availableDates'])->name('admin.djs.available-dates');
    Route::resource('admin/djs', AdminDJController::class)->except(['create'])->names([
        'index' => 'admin.djs.index',
        'store' => 'admin.djs.store',
        'edit' => 'admin.djs.edit',
        'update' => 'admin.djs.update',
        'destroy' => 'admin.djs.destroy',
    ]);

    // Admin Date resource routes (omit show, create pages — modal used)
    //Admin dates for AJAX
    Route::get('admin/dates/list', [AdminDateController::class, 'list'])->name('admin.dates.list');
    Route::get('admin/dates/{date}/edit', [AdminDateController::class, 'edit'])->name('admin.dates.edit');
    
    Route::resource('admin/dates', AdminDateController::class)->except(['show', 'create', 'edit'])->names([
        'index' => 'admin.dates.index',
        'store' => 'admin.dates.store',
        'update' => 'admin.dates.update',
        'destroy' => 'admin.dates.destroy',
    ]);
    // Admin DJs for AJAX (moved to API routes)
});

require __DIR__.'/auth.php';
