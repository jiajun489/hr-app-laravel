<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\WorkLifeBalanceMetric;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateWorkLifeMetrics extends Command
{
    protected $signature = 'employees:calculate-work-life-metrics {--employee_id=}';
    protected $description = 'Calculate work-life balance metrics for employees';

    public function handle()
    {
        $employeeId = $this->option('employee_id');
        
        $query = Employee::query()->where('status', 'active');
        
        if ($employeeId) {
            $query->where('id', $employeeId);
        }
        
        $employees = $query->get();
        $count = $employees->count();
        
        $this->info("Calculating work-life balance metrics for {$count} employees...");
        $bar = $this->output->createProgressBar($count);
        
        $weekStart = Carbon::now()->startOfWeek();
        
        foreach ($employees as $employee) {
            try {
                // Calculate overtime hours for the week
                $overtimeHours = $employee->getWeeklyOvertimeHours($weekStart);
                
                // Calculate consecutive work days
                $consecutiveWorkDays = $this->calculateConsecutiveWorkDays($employee);
                
                // Calculate leave balance ratio
                $leaveBalanceRatio = $employee->getLeaveBalanceRatio();
                
                // Create or update work-life balance metrics
                WorkLifeBalanceMetric::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'week_start' => $weekStart,
                    ],
                    [
                        'overtime_hours' => $overtimeHours,
                        'consecutive_work_days' => $consecutiveWorkDays,
                        'leave_balance_ratio' => $leaveBalanceRatio,
                    ]
                );
            } catch (\Exception $e) {
                Log::error("Error calculating metrics for employee {$employee->id}: " . $e->getMessage());
                $this->error("Error for employee {$employee->fullname}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Work-life balance metrics calculation completed!');
        
        return 0;
    }
    
    /**
     * Calculate the number of consecutive days an employee has worked
     */
    private function calculateConsecutiveWorkDays(Employee $employee): int
    {
        $today = Carbon::today();
        $consecutiveDays = 0;
        
        // Check up to 14 days back
        for ($i = 0; $i < 14; $i++) {
            $date = $today->copy()->subDays($i);
            
            $presence = $employee->presences()
                ->whereDate('date', $date)
                ->where(function ($query) {
                    $query->whereNotNull('check_in')
                          ->whereNotNull('check_out');
                })
                ->first();
                
            if ($presence) {
                $consecutiveDays++;
            } else {
                // Break the streak if no presence found
                break;
            }
        }
        
        return $consecutiveDays;
    }
}
