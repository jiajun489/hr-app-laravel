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
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            abort(401, 'Authentication required.');
        }

        // Get employee through the relationship (email-based)
        $employee = $user->employee;
        
        // Check if employee exists
        if (!$employee) {
            abort(403, 'No employee record associated with this user.');
        }
        
        // Check if employee has a role
        $role = $employee->role;
        if (!$role) {
            abort(403, 'Employee has no assigned role.');
        }
        
        // Get role title
        $roleTitle = $role->title;
        if (!$roleTitle) {
            abort(403, 'Role title is missing.');
        }
        
        // Store role and employee info in session
        $request->session()->put('role', $roleTitle);
        $request->session()->put('employee_id', $employee->id);
        
        // Check if user has required role
        if (!in_array($roleTitle, $roles)) {
            abort(403, "Unauthorized action. Required roles: " . implode(', ', $roles) . ". Your role: {$roleTitle}");
        }
        
        return $next($request);
    }
}
