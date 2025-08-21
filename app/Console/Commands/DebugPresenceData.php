<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DebugPresenceData extends Command
{
    protected $signature = 'debug:presence-data {employee_id}';
    protected $description = 'Debug presence data for a specific employee';

    public function handle()
    {
        $employeeId = $this->argument('employee_id');
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            $this->error("Employee with ID {$employeeId} not found");
            return 1;
        }
        
        $this->info("Debugging presence data for: {$employee->fullname}");
        $this->newLine();
        
        // Get all presence data
        $allPresences = $employee->presences()->orderBy('date', 'desc')->get();
        $this->info("Total presence records: {$allPresences->count()}");
        
        // Get recent data (last 30 days)
        $recentPresences = $employee->presences()
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();
        $this->info("Recent presence records (last 30 days): {$recentPresences->count()}");
        
        // Get baseline data (31-120 days ago)
        $baselinePresences = $employee->presences()
            ->where('date', '<', Carbon::now()->subDays(30))
            ->where('date', '>=', Carbon::now()->subDays(120))
            ->orderBy('date', 'desc')
            ->get();
        $this->info("Baseline presence records (31-120 days ago): {$baselinePresences->count()}");
        
        $this->newLine();
        
        // Show sample data
        if ($allPresences->count() > 0) {
            $this->info("Sample presence records:");
            $this->table(
                ['Date', 'Check In', 'Check Out', 'Working Hours'],
                $allPresences->take(10)->map(function($presence) {
                    return [
                        $presence->date->format('Y-m-d'),
                        $presence->check_in ? $presence->check_in->format('H:i') : 'NULL',
                        $presence->check_out ? $presence->check_out->format('H:i') : 'NULL',
                        $presence->working_hours . ' hours'
                    ];
                })->toArray()
            );
        }
        
        // Check for data quality issues
        $this->newLine();
        $this->info("Data Quality Check:");
        
        $withCheckIn = $allPresences->filter(fn($p) => $p->check_in !== null)->count();
        $withCheckOut = $allPresences->filter(fn($p) => $p->check_out !== null)->count();
        $withBoth = $allPresences->filter(fn($p) => $p->check_in !== null && $p->check_out !== null)->count();
        
        $this->line("Records with check-in: {$withCheckIn}");
        $this->line("Records with check-out: {$withCheckOut}");
        $this->line("Records with both: {$withBoth}");
        
        if ($withBoth > 0) {
            $avgWorkingHours = $allPresences
                ->filter(fn($p) => $p->check_in !== null && $p->check_out !== null)
                ->avg(fn($p) => $p->working_hours);
            $this->line("Average working hours: " . round($avgWorkingHours, 2));
        }
        
        return 0;
    }
}
