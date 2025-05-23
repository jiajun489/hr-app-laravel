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
})->middleware(['auth', 'verified', 'role:Admin,HR Manager,Developer,Accountant, Data Entry'])->name('dashboard');

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
    Route::resource('/departments', DepartmentController::class)->middleware(['role:Admin,HR Manager']);

    // employee routes
    Route::resource('/employees', EmployeeController::class)->middleware(['role:Admin,HR Manager']);

    // leave_requests routes
    Route::resource('/leave_requests', LeaveRequestController::class)->middleware(['role:Admin,HR Manager,Developer,Accountant, Data Entry']);
    Route::get('/leave_requests/approve/{id}', [LeaveRequestController::class, 'approve'])->name('leave_requests.approve')->middleware(['role:Admin,HR Manager']);
    Route::get('/leave_requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave_requests.reject')->middleware(['role:Admin,HR Manager']);

    // payrolls routes
    Route::resource('/payrolls', PayrollController::class)->middleware(['role:Admin,HR Manager,Developer,Accountant, Data Entry']);

    // presence routes
    Route::resource('/presences', PresenceController::class)->middleware(['role:Admin,HR Manager,Developer,Accountant, Data Entry']);

    // role routes
    Route::resource('/roles', RoleController::class)->middleware(['role:Admin,HR Manager']);

    /**
     * Task management routes (CRUD + status toggle)
     */
    Route::resource('tasks', TaskController::class)->middleware(['role:Admin,HR Manager,Developer,Accountant, Data Entry']);
    Route::get('/tasks/{task}/mark-complete', [TaskController::class, 'markComplete'])->name('tasks.markComplete')->middleware(['role:Admin,HR Manager,Developer,Accountant, Data Entry']);
    Route::get('/tasks/{task}/mark-pending', [TaskController::class, 'markPending'])->name('tasks.markPending')->middleware(['role:Admin,HR Manager,Developer,Accountant, Data Entry']);
});

require __DIR__.'/auth.php';
