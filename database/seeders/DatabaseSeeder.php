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
            $role = Role::where('title', $data['role'])->first();

            $employee = Employee::create([
                'fullname'      => $data['name'],
                'email'         => $data['email'],
                'phone'         => '0812345678' . $index,
                'address'       => 'Reltroner HQ',
                'birth_date'    => now()->subYears(25)->subDays($index),
                'hire_date'     => now()->subMonths(3),
                'department_id' => $defaultDept->id,
                'role_id'       => $role->id,
                'status'        => 'active',
                'salary'        => 5000 + ($index * 100),
            ]);

            User::create([
                'name'        => $data['name'],
                'email'       => $data['email'],
                'password'    => Hash::make('password'), // default password
                'employee_id' => $employee->id,
            ]);
        }
    }
}
