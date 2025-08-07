<?php

namespace App\Console\Commands;

use App\Jobs\SendOvertimeAlert;
use App\Models\Employee;
use App\Models\WorkLifeBalanceMetric;
use Illuminate\Console\Command;

class CheckOvertimeAlerts extends Command
{
    protected $signature = 'worklife:check-overtime';
    protected $description = 'Check for employees with excessive overtime and send alerts';

    public function handle()
    {
        $twoWeeksAgo = now()->subWeeks(2)->startOfWeek();
        
        $employees = Employee::whereHas('workLifeBalanceMetrics', function($query) use ($twoWeeksAgo) {
            $query->where('week_start', '>=', $twoWeeksAgo)
                ->havingRaw('SUM(overtime_hours) > 20');
        })->with('manager')->get();

        foreach ($employees as $employee) {
            $totalOvertime = $employee->workLifeBalanceMetrics()
                ->where('week_start', '>=', $twoWeeksAgo)
                ->sum('overtime_hours');

            if ($totalOvertime > 20 && $employee->manager) {
                SendOvertimeAlert::dispatch($employee, $totalOvertime);
                $this->info("Alert sent for {$employee->fullname} ({$totalOvertime} hours)");
            }
        }

        $this->info('Overtime check completed.');
    }
}