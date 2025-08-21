<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSamplePresenceData extends Command
{
    protected $signature = 'generate:sample-presence {employee_id} {--days=60}';
    protected $description = 'Generate sample presence data for testing';

    public function handle()
    {
        $employeeId = $this->argument('employee_id');
        $days = $this->option('days');
        
        $employee = Employee::find($employeeId);
        if (!$employee) {
            $this->error("Employee with ID {$employeeId} not found");
            return 1;
        }
        
        $this->info("Generating {$days} days of sample presence data for {$employee->fullname}");
        
        $startDate = Carbon::now()->subDays($days);
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            // Skip weekends (optional)
            if ($date->isWeekend() && rand(1, 100) > 20) {
                continue;
            }
            
            // Generate realistic check-in time (8:30 AM - 9:30 AM)
            $checkInHour = rand(8, 9);
            $checkInMinute = rand(0, 59);
            if ($checkInHour == 9) {
                $checkInMinute = rand(0, 30);
            }
            
            $checkIn = $date->copy()->setTime($checkInHour, $checkInMinute);
            
            // Generate realistic check-out time (5:00 PM - 7:00 PM)
            $workHours = rand(8, 10); // 8-10 hours of work
            $checkOut = $checkIn->copy()->addHours($workHours)->addMinutes(rand(0, 59));
            
            Presence::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => 'present',
                ]
            );
        }
        
        $this->info("Sample presence data generated successfully!");
        return 0;
    }
}
