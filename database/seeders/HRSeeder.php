<?php

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
            ['id' => 1, 'title' => 'Software Engineer', 'description' => 'Develops systems.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'title' => 'HR Manager', 'description' => 'Manages HR policies.', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'title' => 'Accountant', 'description' => 'Handles accounting.', 'created_at' => now(), 'updated_at' => now()],
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
            DB::table('payrolls')->insert([
                'employee_id' => $i,
                'salary' => $salary,
                'bonus' => $bonus,
                'deduction' => $deduction,
                'net_salary' => $salary + $bonus - $deduction,
                'payment_date' => $faker->dateTimeThisYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed Presences
        for ($i = 0; $i < 30; $i++) {
            $date = $faker->dateTimeBetween('-30 days', 'now');
            DB::table('presences')->insert([
                'employee_id' => rand(1, 10),
                'check_in' => $date,
                'check_out' => $date,
                'date' => $date,
                'status' => $faker->randomElement(['present', 'sick', 'leave']),
                'created_at' => now(),
                'updated_at' => now(),
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
