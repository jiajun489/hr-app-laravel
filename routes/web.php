<?php
// hrm.reltroner.com: routes/web.php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeWellbeingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Models\Employee;

/**
 * Redirect root URL to tasks index
 */
Route::get('/', function () {
    return redirect()->route('login');
});

/**
 * Dashboard page (now uses controller to send data)
 */
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer'])
    ->name('dashboard');

Route::get('/dashboard/presence', [DashboardController::class, 'presence']);
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
    Route::resource('/departments', DepartmentController::class)->middleware(['role:Admin,HR Manager']);
    Route::resource('/employees', EmployeeController::class)->middleware(['role:Admin,HR Manager']);

    /**
     * Leave requests routes
     */
    Route::resource('/leave_requests', LeaveRequestController::class)
        ->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);
    Route::get('/leave_requests/approve/{id}', [LeaveRequestController::class, 'approve'])
        ->name('leave_requests.approve')
        ->middleware(['role:Admin,HR Manager']);
    Route::get('/leave_requests/reject/{id}', [LeaveRequestController::class, 'reject'])
        ->name('leave_requests.reject')
        ->middleware(['role:Admin,HR Manager']);
        
    /**
     * Employee Wellbeing Analytics routes
     */
    Route::get('/wellbeing', [EmployeeWellbeingController::class, 'index'])
        ->name('employee.wellbeing.index')
        ->middleware(['role:Admin,HR Manager']);
    Route::get('/wellbeing/{employee}', [EmployeeWellbeingController::class, 'show'])
        ->name('employee.wellbeing')
        ->middleware(['role:Admin,HR Manager']);
    Route::post('/wellbeing/{employee}/analyze', [EmployeeWellbeingController::class, 'runAnalysis'])
        ->name('employee.wellbeing.analyze')
        ->middleware(['role:Admin,HR Manager']);
    
    // Debug route to test authentication and roles
    Route::get('/debug/auth', function() {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        $employee = $user->employee;
        if (!$employee) {
            return response()->json(['error' => 'No employee record']);
        }
        
        $role = $employee->role;
        if (!$role) {
            return response()->json(['error' => 'No role assigned']);
        }
        
        return response()->json([
            'user' => $user->name,
            'email' => $user->email,
            'employee' => $employee->fullname,
            'role' => $role->title,
            'can_access_wellbeing' => in_array($role->title, ['Admin', 'HR Manager', 'Developer'])
        ]);
    })->middleware('auth');
    Route::post('/wellbeing/feedback/{analysis}', [EmployeeWellbeingController::class, 'storeFeedback'])
        ->name('employee.wellbeing.feedback')
        ->middleware(['role:Admin,HR Manager']);

    /**
     * Payroll and presence routes
     */
    Route::resource('/payrolls', PayrollController::class)->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);
    Route::resource('/presences', PresenceController::class)->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);
    Route::post('/presences/clock-out', [PresenceController::class, 'clockOut'])->name('presences.clock-out')->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);

    /**
     * Role management routes
     */
    Route::resource('/roles', RoleController::class)->middleware(['role:Admin,HR Manager']);

    /**
     * Task management routes
     */
    Route::resource('tasks', TaskController::class)->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);
    Route::get('/tasks/{task}/mark-in-progress', [TaskController::class, 'markInProgress'])->name('tasks.markInProgress')->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);
    Route::get('/tasks/{task}/mark-complete', [TaskController::class, 'markComplete'])->name('tasks.markComplete')->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);
    Route::get('/tasks/{task}/mark-pending', [TaskController::class, 'markPending'])->name('tasks.markPending')->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);

    /**
     * Work-Life Balance routes
     */
    Route::get('/work-life-balance/admin', [\App\Http\Controllers\WorkLifeBalanceController::class, 'adminDashboard'])
        ->name('work-life-balance.admin')
        ->middleware(['role:Admin,HR Manager']);
    
    Route::get('/work-life-balance/employee', [\App\Http\Controllers\WorkLifeBalanceController::class, 'employeeDashboard'])
        ->name('work-life-balance.employee')
        ->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);
    
    Route::get('/work-life-balance/manager', [\App\Http\Controllers\WorkLifeBalanceController::class, 'managerDashboard'])
        ->name('work-life-balance.manager')
        ->middleware(['role:Admin,HR Manager,Developer,Accountant,Data Entry,Animator,Marketer']);

    /**
     * Analytics routes
     */
    Route::get('/admin/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])
        ->name('admin.analytics.index')
        ->middleware(['role:Admin,HR Manager']);
});

// Database status check route
Route::get('/check-database-status', function() {
    try {
        $info = [];
        
        // Environment info
        $info['environment'] = app()->environment();
        $info['base_path'] = base_path();
        $info['database_path_helper'] = database_path();
        
        // Database configuration
        $info['db_connection'] = config('database.default');
        $info['db_config'] = config('database.connections.' . config('database.default'));
        
        // Environment variables
        $info['env_db_connection'] = env('DB_CONNECTION');
        $info['env_db_host'] = env('DB_HOST');
        $info['env_db_database'] = env('DB_DATABASE');
        
        // File system checks for SQLite
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            $info['configured_db_path'] = $dbPath;
            $info['db_file_exists'] = file_exists($dbPath);
            $info['db_directory_exists'] = file_exists(dirname($dbPath));
            $info['db_directory_writable'] = is_writable(dirname($dbPath));
            
            if (file_exists($dbPath)) {
                $info['db_file_size'] = filesize($dbPath) . ' bytes';
                $info['db_file_permissions'] = substr(sprintf('%o', fileperms($dbPath)), -4);
            }
        }
        
        // Connection test
        try {
            $pdo = DB::connection()->getPdo();
            $info['connection_status'] = 'SUCCESS';
            $info['pdo_driver'] = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            // Check if sessions table exists
            try {
                $sessionCount = DB::table('sessions')->count();
                $info['sessions_table'] = "EXISTS (count: {$sessionCount})";
            } catch (Exception $e) {
                $info['sessions_table'] = 'MISSING - ' . $e->getMessage();
            }
            
        } catch (Exception $e) {
            $info['connection_status'] = 'FAILED - ' . $e->getMessage();
        }
        
        return response()->json([
            'status' => 'info',
            'message' => 'Database status check',
            'info' => $info,
            'timestamp' => now()
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Status check failed: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// Emergency database creation route
Route::get('/emergency-create-database', function() {
    try {
        $results = [];
        
        // Get current environment info
        $results[] = "Environment: " . app()->environment();
        $results[] = "Database connection: " . config('database.default');
        $results[] = "ENV DB_CONNECTION: " . env('DB_CONNECTION');
        
        // Handle different database types
        if (config('database.default') === 'sqlite') {
            // SQLite handling
            $configPath = config('database.connections.sqlite.database');
            $results[] = "Configured DB path: {$configPath}";
            
            $dbPath = $configPath;
            if (!str_starts_with($dbPath, '/')) {
                $dbPath = base_path($dbPath);
            }
            
            $results[] = "Actual DB path: {$dbPath}";
            
            // Create database directory if it doesn't exist
            $dbDir = dirname($dbPath);
            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0755, true);
                $results[] = "Created database directory: {$dbDir}";
            } else {
                $results[] = "Database directory exists: {$dbDir}";
            }
            
            // Create SQLite database file if it doesn't exist
            if (!file_exists($dbPath)) {
                touch($dbPath);
                chmod($dbPath, 0644);
                $results[] = "Created SQLite database file: {$dbPath}";
            } else {
                $results[] = "Database file already exists: {$dbPath}";
            }
            
            $perms = substr(sprintf('%o', fileperms($dbPath)), -4);
            $results[] = "Database file permissions: {$perms}";
            
        } else {
            // PostgreSQL/MySQL handling
            $results[] = "Using " . config('database.default') . " database - no file creation needed";
            $results[] = "Host: " . config('database.connections.' . config('database.default') . '.host');
            $results[] = "Database: " . config('database.connections.' . config('database.default') . '.database');
        }
        
        // Clear all caches first
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $results[] = "Cleared all caches";
        
        // Run migrations to create tables
        Artisan::call('migrate', ['--force' => true]);
        $results[] = "Ran database migrations";
        
        // Test database connection
        try {
            $pdo = DB::connection()->getPdo();
            $results[] = "Database connection test: SUCCESS";
            $results[] = "PDO Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            // Test session table specifically
            $sessionCount = DB::table('sessions')->count();
            $results[] = "Sessions table accessible - current count: {$sessionCount}";
            
        } catch (Exception $e) {
            $results[] = "Database connection test: FAILED - " . $e->getMessage();
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Database setup completed!',
            'details' => $results,
            'timestamp' => now(),
            'warning' => 'Please remove this route after use for security!'
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database setup failed: ' . $e->getMessage(),
            'details' => $results ?? [],
            'timestamp' => now()
        ], 500);
    }
});

// Emergency cache clearing route
Route::get('/emergency-clear-cache', function() {
    try {
        $results = [];
        
        // Clear all caches
        Artisan::call('cache:clear');
        $results[] = 'Application cache cleared';
        
        Artisan::call('config:clear');
        $results[] = 'Configuration cache cleared';
        
        Artisan::call('route:clear');
        $results[] = 'Route cache cleared';
        
        Artisan::call('view:clear');
        $results[] = 'View cache cleared';
        
        Artisan::call('optimize:clear');
        $results[] = 'All compiled files cleared';
        
        // Optional: Clear session data
        if (config('session.driver') === 'file') {
            Artisan::call('session:clear');
            $results[] = 'Session files cleared';
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'All caches cleared successfully!',
            'details' => $results,
            'timestamp' => now(),
            'warning' => 'Please remove this route after use for security!'
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Cache clearing failed: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// Emergency database and session fix route
Route::get('/fix-database-emergency', function() {
    try {
        $results = [];
        
        // Get current environment info
        $results[] = "Environment: " . app()->environment();
        $results[] = "Database connection: " . config('database.default');
        $results[] = "Database host: " . config('database.connections.' . config('database.default') . '.host');
        $results[] = "Database name: " . config('database.connections.' . config('database.default') . '.database');
        $results[] = "ENV DB_CONNECTION: " . env('DB_CONNECTION');
        $results[] = "ENV DB_HOST: " . env('DB_HOST');
        $results[] = "ENV DB_DATABASE: " . env('DB_DATABASE');
        
        // Special handling for the specific error case
        if (config('database.default') === 'sqlite') {
            // SQLite handling
            $configPath = config('database.connections.sqlite.database');
            $results[] = "Configured DB path: {$configPath}";
            
            // Check for the specific error path
            $errorPath = '/var/www/database/database.sqlite';
            $results[] = "Checking for error path: {$errorPath}";
            
            // Try to create the error path directory and file
            try {
                $errorDir = dirname($errorPath);
                if (!file_exists($errorDir)) {
                    mkdir($errorDir, 0755, true);
                    $results[] = "Created error directory: {$errorDir}";
                } else {
                    $results[] = "Error directory exists: {$errorDir}";
                }
                
                if (!file_exists($errorPath)) {
                    touch($errorPath);
                    chmod($errorPath, 0644);
                    $results[] = "Created SQLite database file at error path: {$errorPath}";
                } else {
                    $results[] = "Database file at error path already exists: {$errorPath}";
                }
            } catch (\Exception $e) {
                $results[] = "Failed to create error path database: " . $e->getMessage();
            }
            
            // Now handle the configured path
            $dbPath = $configPath;
            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($configPath);
            }
            
            $results[] = "Actual DB path: {$dbPath}";
            
            // Create database directory if it doesn't exist
            $dbDir = dirname($dbPath);
            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0755, true);
                $results[] = "Created database directory: {$dbDir}";
            } else {
                $results[] = "Database directory exists: {$dbDir}";
            }
            
            // Create SQLite database file if it doesn't exist
            if (!file_exists($dbPath)) {
                touch($dbPath);
                chmod($dbPath, 0644);
                $results[] = "Created SQLite database file: {$dbPath}";
            } else {
                $results[] = "Database file already exists: {$dbPath}";
            }
            
            $perms = substr(sprintf('%o', fileperms($dbPath)), -4);
            $results[] = "Database file permissions: {$perms}";
            
            // Try to override the database connection at runtime
            try {
                config(['database.connections.sqlite.database' => $dbPath]);
                DB::purge('sqlite');
                $results[] = "Overrode SQLite database path to: {$dbPath}";
            } catch (\Exception $e) {
                $results[] = "Failed to override database path: " . $e->getMessage();
            }
            
        } else if (config('database.default') === 'pgsql') {
            // PostgreSQL handling
            $results[] = "Using PostgreSQL database";
            $results[] = "Host: " . config('database.connections.pgsql.host');
            $results[] = "Port: " . config('database.connections.pgsql.port');
            $results[] = "Database: " . config('database.connections.pgsql.database');
            $results[] = "Username: " . config('database.connections.pgsql.username');
            
            // Test PostgreSQL connection
            try {
                DB::connection('pgsql')->getPdo();
                $results[] = "PostgreSQL connection test: SUCCESS";
            } catch (\Exception $e) {
                $results[] = "PostgreSQL connection test: FAILED - " . $e->getMessage();
                
                // Try to switch to SQLite as fallback
                try {
                    config(['database.default' => 'sqlite']);
                    $dbPath = database_path('database.sqlite');
                    config(['database.connections.sqlite.database' => $dbPath]);
                    
                    // Create SQLite file if it doesn't exist
                    if (!file_exists($dbPath)) {
                        touch($dbPath);
                        chmod($dbPath, 0644);
                    }
                    
                    DB::purge();
                    $results[] = "Switched to SQLite as fallback at: {$dbPath}";
                } catch (\Exception $e2) {
                    $results[] = "Failed to switch to SQLite: " . $e2->getMessage();
                }
            }
        } else {
            // MySQL/other handling
            $results[] = "Using " . config('database.default') . " database";
            $results[] = "Host: " . config('database.connections.' . config('database.default') . '.host');
            $results[] = "Database: " . config('database.connections.' . config('database.default') . '.database');
        }
        
        // Clear all caches first
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $results[] = "Cleared all caches";
        
        // Run migrations to create tables
        try {
            Artisan::call('migrate', ['--force' => true]);
            $results[] = "Ran database migrations successfully";
        } catch (\Exception $e) {
            $results[] = "Migration error: " . $e->getMessage();
        }
        
        // Test database connection
        try {
            $pdo = DB::connection()->getPdo();
            $results[] = "Database connection test: SUCCESS";
            $results[] = "PDO Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            // Test session table specifically
            try {
                $sessionCount = DB::table('sessions')->count();
                $results[] = "Sessions table accessible - current count: {$sessionCount}";
            } catch (\Exception $e) {
                $results[] = "Sessions table error: " . $e->getMessage();
                
                // Try to create sessions table if it doesn't exist
                try {
                    Artisan::call('session:table');
                    Artisan::call('migrate', ['--force' => true]);
                    $results[] = "Created sessions table migration and ran it";
                    
                    $sessionCount = DB::table('sessions')->count();
                    $results[] = "Sessions table now accessible - current count: {$sessionCount}";
                } catch (\Exception $e2) {
                    $results[] = "Failed to create sessions table: " . $e2->getMessage();
                }
            }
            
        } catch (\Exception $e) {
            $results[] = "Database connection test: FAILED - " . $e->getMessage();
        }
        
        // Check for the specific error path again
        $errorPath = '/var/www/database/database.sqlite';
        if (file_exists($errorPath)) {
            $results[] = "Final check: Error path database exists";
            $size = filesize($errorPath);
            $results[] = "Error path database size: {$size} bytes";
            $perms = substr(sprintf('%o', fileperms($errorPath)), -4);
            $results[] = "Error path database permissions: {$perms}";
        } else {
            $results[] = "Final check: Error path database still does not exist";
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Database setup completed!',
            'details' => $results,
            'timestamp' => now(),
            'warning' => 'Please remove this route after use for security!'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database setup failed: ' . $e->getMessage(),
            'details' => isset($results) ? $results : [],
            'timestamp' => now()
        ], 500);
    }
});

Route::get('/api/public-employees', function () {
    return Response::json(Employee::select('id', 'fullname', 'email', 'department_id')->get());
});

// Database cleanup route - REMOVE AFTER USE FOR SECURITY
Route::get('/database-cleanup', function() {
    try {
        $results = [];
        $preservedEmails = ['hr@example.com', 'developer@example.com'];
        
        // Get the IDs of employees to preserve
        $preservedEmployeeIds = DB::table('employees')
            ->whereIn('email', $preservedEmails)
            ->pluck('id')
            ->toArray();
            
        $results[] = "Preserving employees with emails: " . implode(', ', $preservedEmails);
        $results[] = "Preserved employee IDs: " . implode(', ', $preservedEmployeeIds);
        
        // Get the IDs of users to preserve
        $preservedUserIds = DB::table('users')
            ->whereIn('email', $preservedEmails)
            ->pluck('id')
            ->toArray();
            
        $results[] = "Preserved user IDs: " . implode(', ', $preservedUserIds);
        
        // Start transaction
        DB::beginTransaction();
        
        // Delete work_life_balance_metrics for non-preserved employees
        $count = DB::table('work_life_balance_metrics')
            ->whereNotIn('employee_id', $preservedEmployeeIds)
            ->delete();
        $results[] = "Deleted {$count} work_life_balance_metrics records";
        
        // Delete tasks for non-preserved employees
        $count = DB::table('tasks')
            ->whereNotIn('assigned_to', $preservedEmployeeIds)
            ->delete();
        $results[] = "Deleted {$count} tasks records";
        
        // Delete leave_requests for non-preserved employees
        $count = DB::table('leave_requests')
            ->whereNotIn('employee_id', $preservedEmployeeIds)
            ->delete();
        $results[] = "Deleted {$count} leave_requests records";
        
        // Delete presences for non-preserved employees
        $count = DB::table('presences')
            ->whereNotIn('employee_id', $preservedEmployeeIds)
            ->delete();
        $results[] = "Deleted {$count} presences records";
        
        // Delete payrolls for non-preserved employees
        $count = DB::table('payrolls')
            ->whereNotIn('employee_id', $preservedEmployeeIds)
            ->delete();
        $results[] = "Deleted {$count} payrolls records";
        
        // Delete employees (except preserved ones)
        // First, set manager_id to NULL for all employees to avoid foreign key constraints
        DB::table('employees')
            ->update(['manager_id' => null]);
        $results[] = "Set manager_id to NULL for all employees";
        
        // Now delete non-preserved employees
        $count = DB::table('employees')
            ->whereNotIn('id', $preservedEmployeeIds)
            ->delete();
        $results[] = "Deleted {$count} employees records";
        
        // Delete users (except preserved ones)
        $count = DB::table('users')
            ->whereNotIn('id', $preservedUserIds)
            ->delete();
        $results[] = "Deleted {$count} users records";
        
        // Commit transaction
        DB::commit();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Database cleanup completed successfully!',
            'details' => $results,
            'timestamp' => now(),
            'warning' => 'Please remove this route after use for security!'
        ]);
        
    } catch (\Exception $e) {
        // Rollback transaction on error
        DB::rollBack();
        
        return response()->json([
            'status' => 'error',
            'message' => 'Database cleanup failed: ' . $e->getMessage(),
            'details' => isset($results) ? $results : [],
            'timestamp' => now()
        ], 500);
    }
    /**
     * Work-Life Balance routes
     */
    Route::prefix('work-life-balance')->name('work-life-balance.')->group(function () {
        Route::get('/employee', [App\Http\Controllers\WorkLifeBalanceController::class, 'employeeDashboard'])
            ->name('employee');
        Route::get('/manager', [App\Http\Controllers\WorkLifeBalanceController::class, 'managerDashboard'])
            ->middleware(['role:Admin,HR Manager'])
            ->name('manager');
        Route::get('/admin', [App\Http\Controllers\WorkLifeBalanceController::class, 'adminDashboard'])
            ->middleware(['role:Admin,HR Manager'])
            ->name('admin');
    });
});

require __DIR__.'/auth.php';
