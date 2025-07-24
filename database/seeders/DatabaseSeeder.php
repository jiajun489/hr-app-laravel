<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call HR Seeder for departments & roles
        $this->call([
            HRSeeder::class,
        ]);

        // Create a default department (if needed)
        $defaultDept = Department::firstOrCreate(
            ['name' => 'General'],
            ['description' => 'Default department', 'status' => 'active']
        );

        // Define users with role title
        $users = [
            ['name' => 'Alice Admin',     'email' => 'admin@example.com',     'role' => 'Admin'],
            ['name' => 'Eddy Entry',      'email' => 'entry@example.com',     'role' => 'Data Entry'],
            ['name' => 'Maya Marketer',  'email' => 'marketer@example.com', 'role' => 'Marketer'],
            ['name' => 'Andy Animator',   'email' => 'animator@example.com',  'role' => 'Animator'],
            ['name' => 'Helen HR',        'email' => 'hr@example.com',        'role' => 'HR Manager'],
            ['name' => 'Sofie Developer',  'email' => 'developer@example.com',  'role' => 'Developer'],
            ['name' => 'Cathy Cash',      'email' => 'accounting@example.com','role' => 'Accountant'],
        ];

        foreach ($users as $index => $data) {
            // Check if user with this email already exists
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                continue; // Skip if user already exists
            }
            
            // Check if employee with this email already exists
            $existingEmployee = Employee::where('email', $data['email'])->first();
            if ($existingEmployee) {
                continue; // Skip if employee already exists
            }
            
            $role = Role::where('title', $data['role'])->first();
            if (!$role) {
                continue; // Skip if role doesn't exist
            }

            // Create employee with firstOrCreate to avoid duplicates
            $employee = Employee::firstOrCreate(
                ['email' => $data['email']],
                [
                    'fullname'      => $data['name'],
                    'phone'         => '0812345678' . $index,
                    'address'       => 'Reltroner HQ',
                    'birth_date'    => now()->subYears(25)->subDays($index),
                    'hire_date'     => now()->subMonths(3),
                    'department_id' => $defaultDept->id,
                    'role_id'       => $role->id,
                    'status'        => 'active',
                    'salary'        => 5000 + ($index * 100),
                ]
            );

            // Create user with firstOrCreate to avoid duplicates
            // Ensure employee ID is properly set following variable declaration checks
            $employeeId = $employee ? $employee->id : null;
            
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'        => $data['name'],
                    'password'    => Hash::make('password'), // default password
                    'employee_id' => $employeeId,
                ]
            );
        }
    }
}
