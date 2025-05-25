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
            ['id' => 1, 'name' => 'Engineering', 'description' => 'Handles all technical tasks.', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'HR', 'description' => 'Manages employee welfare.', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Finance', 'description' => 'Handles all financial matters.', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('departments')->insert($departments);

        // Seed Roles
        $roles = [
            ['id' => 1, 'title' => 'Accountant',       'description' => 'Handles accounting tasks.',           'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'title' => 'Admin',            'description' => 'Administrator with full access.',     'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'title' => 'Animator',         'description' => 'Creates visual animations.',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'title' => 'Data Entry',       'description' => 'Inputs and manages raw data.',        'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'title' => 'HR Manager',       'description' => 'Manages HR policies and staff.',      'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'title' => 'Marketer',        'description' => 'Develops and executes marketing plans.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'title' => 'Developer',        'description' => 'Develops and maintains software systems.', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insert($roles);

        // Seed Employees
        for ($i = 1; $i <= 10; $i++) {
            DB::table('employees')->insert([
                'id' => $i,
                'fullname' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'birth_date' => $faker->dateTimeBetween('-45 years', '-22 years'),
                'hire_date' => $faker->dateTimeBetween('-5 years', 'now'),
                'department_id' => rand(1, 3),
                'role_id' => rand(1, 3),
                'status' => 'active',
                'salary' => $faker->randomFloat(2, 3000, 10000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed Tasks
        for ($i = 0; $i < 20; $i++) {
            DB::table('tasks')->insert([
                'title' => $faker->bs(),
                'description' => $faker->paragraph(),
                'assigned_to' => rand(1, 10),
                'due_date' => $faker->dateTimeBetween('now', '+30 days'),
                'status' => $faker->randomElement(['pending', 'in_progress', 'completed']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed Payrolls
        for ($i = 1; $i <= 10; $i++) {
            $salary = DB::table('employees')->where('id', $i)->value('salary');
            $bonus = $faker->randomFloat(2, 0, 0.2 * $salary);
            $deduction = $faker->randomFloat(2, 0, 0.1 * $salary);
            $netSalary = round(($salary ?? 0) + ($bonus ?? 0) - ($deduction ?? 0), 2);
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

            DB::table('presences')->insert([
                'employee_id' => rand(1, 10),
                'check_in'    => $checkIn->format('Y-m-d H:i:s'),
                'check_out'   => $checkOut->format('Y-m-d H:i:s'),
                'date'        => $dateOnly,
                'status'      => $faker->randomElement(['present', 'absent', 'late', 'leave']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Seed Leave Requests
        for ($i = 0; $i < 10; $i++) {
            $start = $faker->dateTimeBetween('-60 days', '-1 days');
            $end = Carbon::parse($start)->addDays(rand(1, 5));
            DB::table('leave_requests')->insert([
                'employee_id' => rand(1, 10),
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
