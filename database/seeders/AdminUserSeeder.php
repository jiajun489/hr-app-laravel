<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin role
        $adminRole = Role::firstOrCreate(
            ['title' => 'Admin'],
            [
                'description' => 'Administrator with full access',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create HR Manager role
        $hrRole = Role::firstOrCreate(
            ['title' => 'HR Manager'],
            [
                'description' => 'Human Resources Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create HR department
        $hrDepartment = Department::firstOrCreate(
            ['name' => 'Human Resources'],
            [
                'description' => 'HR Department',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create Admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create Admin employee record
        Employee::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'fullname' => 'Admin User',
                'phone' => '1234567890',
                'address' => '123 Admin St',
                'status' => 'active',
                'hire_date' => '2024-01-01',
                'salary' => 100000,
                'department_id' => $hrDepartment->id,
                'role_id' => $adminRole->id,
                'annual_leave_days' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create HR Manager user
        $hrUser = User::firstOrCreate(
            ['email' => 'hr@example.com'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Create HR Manager employee record
        Employee::firstOrCreate(
            ['email' => 'hr@example.com'],
            [
                'fullname' => 'HR Manager',
                'phone' => '0987654321',
                'address' => '456 HR St',
                'status' => 'active',
                'hire_date' => '2024-01-01',
                'salary' => 90000,
                'department_id' => $hrDepartment->id,
                'role_id' => $hrRole->id,
                'annual_leave_days' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $this->command->info('Admin and HR Manager users created successfully!');
    }
}
