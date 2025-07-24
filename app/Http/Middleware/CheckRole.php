<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Employee;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Set default values following code review rules
        $user = auth()->user();
        $employeeID = $user ? $user->employee_id : null;
        
        // Check if employee_id exists
        if ($employeeID === null) {
            abort(403, 'No employee record associated with this user.');
        }
        
        $employee = Employee::find($employeeID);
        
        // Check if employee exists
        if ($employee === null) {
            abort(403, 'Employee record not found.');
        }
        
        // Check if employee has a role
        $role = $employee->role;
        if ($role === null) {
            abort(403, 'Employee has no assigned role.');
        }
        
        // Get role title with proper null check
        $roleTitle = $role->title;
        if ($roleTitle === null) {
            abort(403, 'Role title is missing.');
        }
        
        $request->session()->put('role', $roleTitle);
        $request->session()->put('employee_id', $employee->id);
        
        if (!in_array($roleTitle, $roles)) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
