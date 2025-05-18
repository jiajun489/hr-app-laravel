<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

/**
 * Redirect root URL to tasks index
 */
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

/**
 * Dashboard page (optional manual access)
 */
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Authenticated routes group
 */
Route::middleware(['auth'])->group(function () {

    /**
     * Profile management routes
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Employee management routes (CRUD)
     */
    Route::resource('employees', EmployeeController::class);
    // Optional: mark active/inactive if needed
    // Route::get('/employees/{employee}/activate', [EmployeeController::class, 'markActive'])->name('employees.activate');
    // Route::get('/employees/{employee}/deactivate', [EmployeeController::class, 'markInactive'])->name('employees.deactivate');

    /**
     * Task management routes (CRUD + status toggle)
     */
    Route::resource('tasks', TaskController::class);
    Route::get('/tasks/{task}/mark-complete', [TaskController::class, 'markComplete'])->name('tasks.markComplete');
    Route::get('/tasks/{task}/mark-pending', [TaskController::class, 'markPending'])->name('tasks.markPending');
});

require __DIR__.'/auth.php';
