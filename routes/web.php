<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Model Management
    Route::resource('models', ModelController::class)->middleware('role:admin,editor');
    Route::post('/models/sync', [ModelController::class, 'syncModels'])->name('models.sync')->middleware('role:admin');

    // API Key Management
    Route::resource('api-keys', \App\Http\Controllers\Admin\ApiKeyController::class)->middleware('role:admin,editor');
    Route::post('/api-keys/{apiKey}/regenerate', [\App\Http\Controllers\Admin\ApiKeyController::class, 'regenerate'])->name('api-keys.regenerate')->middleware('role:admin');

    // API Documentation
    Route::get('/documentation', [\App\Http\Controllers\Admin\DocumentationController::class, 'viewDocs'])->name('documentation');
    Route::get('/documentation/postman-collection', [\App\Http\Controllers\Admin\DocumentationController::class, 'downloadPostman'])->name('documentation.postman');

    // User Management (admin only)
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->middleware('role:admin');

    // Role Management (admin only)
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->middleware('role:admin');

    // Model Playground
    Route::get('/playground', [\App\Http\Controllers\Admin\ModelPlaygroundController::class, 'index'])->name('playground');
    Route::post('/playground/chat', [\App\Http\Controllers\Admin\ModelPlaygroundController::class, 'chat'])->name('playground.chat');
    Route::post('/playground/generate', [\App\Http\Controllers\Admin\ModelPlaygroundController::class, 'generate'])->name('playground.generate');
});

require __DIR__ . '/auth.php';
