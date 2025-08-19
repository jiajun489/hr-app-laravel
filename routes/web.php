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
        
        // File system checks
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
            DB::connection()->getPdo();
            $info['connection_status'] = 'SUCCESS';
            
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
        
        // Handle SQLite database creation for production
        if (config('database.default') === 'sqlite') {
            // Get the configured database path
            $configPath = config('database.connections.sqlite.database');
            $results[] = "Configured DB path: {$configPath}";
            
            // For production, ensure we use the correct path
            $dbPath = $configPath;
            
            // If path doesn't start with /, make it absolute from base path
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
            
            // Verify file permissions
            $perms = substr(sprintf('%o', fileperms($dbPath)), -4);
            $results[] = "Database file permissions: {$perms}";
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
            DB::connection()->getPdo();
            $results[] = "Database connection test: SUCCESS";
            
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
        
        // Handle SQLite database creation
        if (config('database.default') === 'sqlite') {
            $dbPath = database_path('database.sqlite');
            $dbDir = dirname($dbPath);
            
            // Create database directory if it doesn't exist
            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0755, true);
                $results[] = "Created database directory: {$dbDir}";
            }
            
            // Create SQLite database file if it doesn't exist
            if (!file_exists($dbPath)) {
                touch($dbPath);
                chmod($dbPath, 0644);
                $results[] = "Created SQLite database file: {$dbPath}";
            }
        }
        
        // Clear all caches
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $results[] = "Cleared all caches";
        
        // Run migrations to ensure all tables exist
        Artisan::call('migrate', ['--force' => true]);
        $results[] = "Ran database migrations";
        
        // Recache config for production
        if (app()->environment('production')) {
            Artisan::call('config:cache');
            $results[] = "Cached configuration for production";
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Database and cache fixed successfully!',
            'details' => $results,
            'timestamp' => now(),
            'warning' => 'Please remove this route after use for security!'
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Fix failed: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

Route::get('/api/public-employees', function () {
    return Response::json(Employee::select('id', 'fullname', 'email', 'department_id')->get());
});

require __DIR__.'/auth.php';
