<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugDateQueries extends Command
{
    protected $signature = 'debug:date-queries {employee_id}';
    protected $description = 'Debug date queries for presence data';

    public function handle()
    {
        $employeeId = $this->argument('employee_id');
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            $this->error("Employee with ID {$employeeId} not found");
            return 1;
        }
        
        $this->info("Debugging date queries for: {$employee->fullname}");
        $this->newLine();
        
        // Show current date and time
        $now = Carbon::now();
        $this->info("Current date/time: {$now->format('Y-m-d H:i:s')}");
        $this->info("Current timezone: {$now->getTimezone()->getName()}");
        $this->newLine();
        
        // Test different date ranges
        $daysToAnalyze = 30;
        $baselineDays = 90;
        
        $today = Carbon::now()->startOfDay();
        $recentStartDate = $today->copy()->subDays($daysToAnalyze);
        $baselineEndDate = $today->copy()->subDays($daysToAnalyze);
        $baselineStartDate = $today->copy()->subDays($daysToAnalyze + $baselineDays);
        
        $this->info("Date ranges:");
        $this->line("Today: {$today->format('Y-m-d')}");
        $this->line("Recent start (30 days ago): {$recentStartDate->format('Y-m-d')}");
        $this->line("Baseline end (30 days ago): {$baselineEndDate->format('Y-m-d')}");
        $this->line("Baseline start (120 days ago): {$baselineStartDate->format('Y-m-d')}");
        $this->newLine();
        
        // Check total presence records
        $totalRecords = $employee->presences()->count();
        $this->info("Total presence records: {$totalRecords}");
        
        // Check date range of all data
        $firstRecord = $employee->presences()->orderBy('date', 'asc')->first();
        $lastRecord = $employee->presences()->orderBy('date', 'desc')->first();
        
        if ($firstRecord && $lastRecord) {
            $this->line("Data range: {$firstRecord->date->format('Y-m-d')} to {$lastRecord->date->format('Y-m-d')}");
        }
        $this->newLine();
        
        // Test recent query
        $this->info("Testing recent query (last 30 days):");
        $recentCount = $employee->presences()
            ->whereDate('date', '>=', $recentStartDate)
            ->count();
        $this->line("Records found: {$recentCount}");
        
        // Test baseline query (original)
        $this->info("Testing baseline query (31-120 days ago):");
        $baselineCount = $employee->presences()
            ->whereDate('date', '<', $baselineEndDate)
            ->whereDate('date', '>=', $baselineStartDate)
            ->count();
        $this->line("Records found: {$baselineCount}");
        
        // Test fallback query
        $this->info("Testing fallback query (all historical data):");
        $fallbackCount = $employee->presences()
            ->whereDate('date', '<', $baselineEndDate)
            ->count();
        $this->line("Records found: {$fallbackCount}");
        $this->newLine();
        
        // Show raw SQL queries
        $this->info("Raw SQL queries:");
        
        // Recent query
        $recentQuery = $employee->presences()
            ->whereDate('date', '>=', $recentStartDate)
            ->toSql();
        $this->line("Recent: {$recentQuery}");
        
        // Baseline query
        $baselineQuery = $employee->presences()
            ->whereDate('date', '<', $baselineEndDate)
            ->whereDate('date', '>=', $baselineStartDate)
            ->toSql();
        $this->line("Baseline: {$baselineQuery}");
        
        // Show sample data with different query methods
        $this->newLine();
        $this->info("Testing different query methods:");
        
        // Method 1: whereDate
        $method1Count = $employee->presences()
            ->whereDate('date', '<', $baselineEndDate->format('Y-m-d'))
            ->count();
        $this->line("Method 1 (whereDate with string): {$method1Count}");
        
        // Method 2: where with date string
        $method2Count = $employee->presences()
            ->where('date', '<', $baselineEndDate->format('Y-m-d'))
            ->count();
        $this->line("Method 2 (where with string): {$method2Count}");
        
        // Method 3: raw query
        $method3Count = DB::table('presences')
            ->where('employee_id', $employeeId)
            ->where('date', '<', $baselineEndDate->format('Y-m-d'))
            ->count();
        $this->line("Method 3 (raw query): {$method3Count}");
        
        // Show sample records
        $this->newLine();
        $this->info("Sample records (last 5):");
        $sampleRecords = $employee->presences()
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get(['date', 'check_in', 'check_out']);
            
        foreach ($sampleRecords as $record) {
            $this->line("Date: {$record->date->format('Y-m-d')}, Check-in: " . 
                      ($record->check_in ? $record->check_in->format('H:i') : 'NULL') . 
                      ", Check-out: " . 
                      ($record->check_out ? $record->check_out->format('H:i') : 'NULL'));
        }
        
        return 0;
    }
}
