<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DJController as AdminDJController;
use App\Http\Controllers\Api\AdminDJController as ApiAdminDJController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Admin DJs for AJAX - supports search & pagination (queried from JS)
    // Use dedicated API controller for list endpoint
    Route::get('admin/djs/list', [ApiAdminDJController::class, 'list'])->name('admin.djs.list');
    // Create a new DJ (with optional video upload)
    Route::post('admin/djs', [ApiAdminDJController::class, 'store'])->name('admin.djs.store');
});
