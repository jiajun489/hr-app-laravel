<?php
// app/Http/Controllers/EmployeeController.php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'role'])->latest()->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $roles = Role::all();

        return view('employees.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname'      => 'required|string|max:255',
            'email'         => 'required|email|unique:employees,email',
            'phone'         => 'required|string|max:20',
            'address'       => 'required|string',
            'birth_date'    => 'required|date',
            'hire_date'     => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'role_id'       => 'required|exists:roles,id',
            'status'        => 'required|in:active,inactive',
            'salary'        => 'required|numeric|min:0',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $roles = Role::all();

        return view('employees.edit', compact('employee', 'departments', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'fullname'      => 'required|string|max:255',
            'email'         => 'required|email|unique:employees,email,' . $employee->id,
            'phone'         => 'required|string|max:20',
            'address'       => 'required|string',
            'birth_date'    => 'required|date',
            'hire_date'     => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'role_id'       => 'required|exists:roles,id',
            'status'        => 'required|in:active,inactive',
            'salary'        => 'required|numeric|min:0',
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Misal, diasumsikan auth()->user()->email == $employee->email untuk user sendiri
        if (auth()->user()->email === $employee->email) {
            return redirect()->route('employees.index')->with('error', 'You cannot delete your own account.');
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

}
