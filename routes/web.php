<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\RoleController;
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

    // department routes
    Route::resource('/departments', DepartmentController::class);

    // employee routes
    Route::resource('/employees', EmployeeController::class);
    // Optional: mark active/inactive if needed
    // Route::get('/employees/{employee}/activate', [EmployeeController::class, 'markActive'])->name('employees.activate');
    // Route::get('/employees/{employee}/deactivate', [EmployeeController::class, 'markInactive'])->name('employees.deactivate');

    // leave_requests routes
    Route::resource('/leave_requests', LeaveRequestController::class);

    // payrolls routes
    Route::resource('/payrolls', PayrollController::class);

    // presence routes
    Route::resource('/presences', PresenceController::class);

    // role routes
    Route::resource('/roles', RoleController::class);

    /**
     * Task management routes (CRUD + status toggle)
     */
    Route::resource('tasks', TaskController::class);
    Route::get('/tasks/{task}/mark-complete', [TaskController::class, 'markComplete'])->name('tasks.markComplete');
    Route::get('/tasks/{task}/mark-pending', [TaskController::class, 'markPending'])->name('tasks.markPending');
});

require __DIR__.'/auth.php';
