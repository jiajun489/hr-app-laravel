<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/**
 * Redirect root to tasks
 */
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

/**
 * Dashboard page (manual access)
 */
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Authenticated routes
 */
Route::middleware(['auth'])->group(function () {

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tasks routes (full CRUD)
    Route::resource('tasks', TaskController::class);
    Route::get('/tasks/{task}/mark-complete', [TaskController::class, 'markComplete'])->name('tasks.markComplete');
    Route::get('/tasks/{task}/mark-pending', [TaskController::class, 'markPending'])->name('tasks.markPending');

});

require __DIR__.'/auth.php';
