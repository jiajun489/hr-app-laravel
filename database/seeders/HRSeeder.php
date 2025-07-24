<?php
// database/seeders/HRSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class HRSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Seed Departments
        $departments = [
            ['name' => 'Engineering', 'description' => 'Handles all technical tasks.', 'status' => 'active'],
            ['name' => 'HR', 'description' => 'Manages employee welfare.', 'status' => 'active'],
            ['name' => 'Finance', 'description' => 'Handles all financial matters.', 'status' => 'active'],
        ];
        
        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['name' => $dept['name']],
                $dept
            );
        }

        // Seed Roles
        $roles = [
            ['title' => 'Admin',            'description' => 'Administrator with full access.'],
            ['title' => 'HR Manager',       'description' => 'Manages HR policies and staff.'],
            ['title' => 'Animator',         'description' => 'Creates visual animations.'],
            ['title' => 'Data Entry',       'description' => 'Inputs and manages raw data.'],
            ['title' => 'Accountant',       'description' => 'Handles accounting tasks.'],
            ['title' => 'Marketer',        'description' => 'Develops and executes marketing plans.'],
            ['title' => 'Developer',        'description' => 'Develops and maintains software systems.'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['title' => $role['title']],
                array_merge($role, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Get existing department and role IDs
        $departmentIds = DB::table('departments')->pluck('id')->toArray();
        $roleIds = DB::table('roles')->pluck('id')->toArray();
        
        // Make sure we have departments and roles
        if (empty($departmentIds) || empty($roleIds)) {
            $this->command->info('No departments or roles found. Skipping employee creation.');
            return;
        }
        
        // Seed Employees
        for ($i = 1; $i <= 10; $i++) {
            // Generate a unique email to avoid conflicts
            $email = $faker->unique()->safeEmail();
            
            // Check if employee with this email already exists
            $existingEmployee = DB::table('employees')->where('email', $email)->first();
            if ($existingEmployee) {
                continue;
            }
            
            // Let the database auto-assign IDs
            DB::table('employees')->insert([
                'fullname' => $faker->name(),
                'email' => $email, // Use the already generated unique email
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'birth_date' => $faker->dateTimeBetween('-45 years', '-22 years'),
                'hire_date' => $faker->dateTimeBetween('-5 years', 'now'),
                'department_id' => $departmentIds[array_rand($departmentIds)],
                'role_id' => $roleIds[array_rand($roleIds)],
                'status' => 'active',
                'salary' => $faker->randomFloat(2, 3000, 10000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed Tasks
        for ($i = 0; $i < 20; $i++) {
            // First check if we have valid employee IDs
            $employeeId = rand(1, 10);
            $employeeExists = DB::table('employees')->where('id', $employeeId)->exists();
            
            // Only insert if the employee exists
            if ($employeeExists) {
                DB::table('tasks')->insert([
                    'title' => $faker->bs(),
                    'description' => $faker->paragraph(),
                    'assigned_to' => $employeeId,
                    'due_date' => $faker->dateTimeBetween('now', '+30 days'),
                    'status' => $faker->randomElement(['pending', 'in_progress', 'completed']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Seed Payrolls
        for ($i = 1; $i <= 10; $i++) {
            // Check if employee exists first
            $employeeExists = DB::table('employees')->where('id', $i)->exists();
            
            if ($employeeExists) {
                // Get the employee's salary with proper null check
                $salary = DB::table('employees')->where('id', $i)->value('salary');
                $salary = $salary ?? 0; // Default to 0 if null
                
                // Calculate bonus and deduction with proper null checks
                $bonus = $faker->randomFloat(2, 0, 0.2 * $salary);
                $bonus = $bonus ?? 0; // Default to 0 if null
                
                $deduction = $faker->randomFloat(2, 0, 0.1 * $salary);
                $deduction = $deduction ?? 0; // Default to 0 if null
                
                $netSalary = round($salary + $bonus - $deduction, 2);
                
                // Use insert instead of updateOrInsert
                DB::table('payrolls')->insert([
                    'employee_id' => $i,
                    'salary' => $salary,
                    'bonus' => $bonus,
                    'deduction' => $deduction,
                    'net_salary' => $netSalary,
                    'payment_date' => $faker->dateTimeThisYear(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Seed Presences
        for ($i = 0; $i < 100; $i++) {
            // Generate a random month and day
            $month = rand(1, 12);
            $day = rand(1, 28); // to avoid invalid dates like Feb 30
            $year = now()->year;

            // Random hour and minute between 08:00 to 10:00
            $hour = rand(8, 10);
            $minute = rand(0, 59);
            $second = rand(0, 59);

            // Build check-in datetime
            $checkIn = \Carbon\Carbon::create($year, $month, $day, $hour, $minute, $second);
            $checkOut = (clone $checkIn)->copy()->addHours(8);
            $dateOnly = $checkIn->format('Y-m-d');

            // Randomly decide whether to fill latitude/longitude (80% ada data, 20% null)
            $hasLocation = rand(1, 100) <= 80;
            $latitude = $hasLocation ? (-6.175 + (mt_rand(-1000, 1000) / 10000)) : null; // Jakarta ~-6.175
            $longitude = $hasLocation ? (106.827 + (mt_rand(-1000, 1000) / 10000)) : null; // Jakarta ~106.827

            // Use insert instead of updateOrInsert to avoid prepared statement errors
            // First check if we have valid employee IDs
            $employeeId = rand(1, 10);
            $employeeExists = DB::table('employees')->where('id', $employeeId)->exists();
            
            // Only insert if the employee exists
            if ($employeeExists) {
                DB::table('presences')->insert([
                    'employee_id' => $employeeId,
                    'check_in'    => $checkIn->format('Y-m-d H:i:s'),
                    'check_out'   => $checkOut->format('Y-m-d H:i:s'),
                    'date'        => $dateOnly,
                    'status'      => $faker->randomElement(['present', 'absent', 'late', 'leave']),
                    'latitude'    => $latitude,
                    'longitude'   => $longitude,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // Seed Leave Requests
        for ($i = 0; $i < 10; $i++) {
            $start = $faker->dateTimeBetween('-60 days', '-1 days');
            $end = Carbon::parse($start)->addDays(rand(1, 5));
            
            // First check if we have valid employee IDs
            $employeeId = rand(1, 10);
            $employeeExists = DB::table('employees')->where('id', $employeeId)->exists();
            
            // Only insert if the employee exists
            if ($employeeExists) {
                DB::table('leave_requests')->insert([
                    'employee_id' => $employeeId,
                    'leave_type' => $faker->randomElement(['sick', 'vacation', 'personal']),
                    'start_date' => $start,
                    'end_date' => $end,
                    'status' => $faker->randomElement(['approved', 'pending', 'rejected']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
