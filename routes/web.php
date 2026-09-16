<?php

use App\Http\Controllers\SharePointFileController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

Route::inertia('/files', 'files')->name('files');

Route::get(
    '/sharepoint/files',
    [SharePointFileController::class, 'index']
)->name('sharepoint.files');

require __DIR__.'/settings.php';