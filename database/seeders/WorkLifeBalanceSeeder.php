<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class WorkLifeBalanceSeeder extends Seeder
{
    public function run(): void
    {
        // Get all employees
        $employees = Employee::all();
        
        if ($employees->count() > 0) {
            // Set the first employee as a manager (no manager_id)
            $manager = $employees->first();
            
            // Assign other employees to this manager
            $employees->skip(1)->each(function ($employee) use ($manager) {
                $employee->update([
                    'manager_id' => $manager->id,
                    'annual_leave_days' => 21
                ]);
            });
            
            // Update the manager's annual leave days
            $manager->update(['annual_leave_days' => 25]);
        }
    }
}