<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkLifeBalanceMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkLifeBalanceController extends Controller
{
    public function adminDashboard()
    {
        $this->updateMetrics();
        
        $topOvertimeEmployees = WorkLifeBalanceMetric::with('employee')
            ->where('week_start', '>=', now()->subWeeks(2)->startOfWeek())
            ->selectRaw('employee_id, SUM(overtime_hours) as total_overtime')
            ->groupBy('employee_id')
            ->orderBy('total_overtime', 'desc')
            ->limit(10)
            ->get();

        $lowLeaveUsageEmployees = Employee::whereHas('workLifeBalanceMetrics', function($query) {
            $query->where('leave_balance_ratio', '<', 0.3);
        })->with('department', 'role')->get();

        return view('work-life-balance.admin-dashboard', compact(
            'topOvertimeEmployees',
            'lowLeaveUsageEmployees'
        ));
    }

    public function employeeDashboard()
    {
        $employee = Auth::user()->employee ?? Employee::where('email', Auth::user()->email)->first();
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        $this->updateEmployeeMetrics($employee);

        $weeklyMetrics = $employee->workLifeBalanceMetrics()
            ->where('week_start', '>=', now()->subWeeks(12)->startOfWeek())
            ->orderBy('week_start', 'desc')
            ->get();

        $remainingLeave = $employee->annual_leave_days - ($employee->annual_leave_days * $employee->getLeaveBalanceRatio());

        return view('work-life-balance.employee-dashboard', compact(
            'employee',
            'weeklyMetrics',
            'remainingLeave'
        ));
    }

    public function managerDashboard()
    {
        $manager = Auth::user()->employee ?? Employee::where('email', Auth::user()->email)->first();
        
        if (!$manager) {
            return redirect()->back()->with('error', 'Manager profile not found.');
        }

        $directReports = $manager->directReports()->with('workLifeBalanceMetrics')->get();
        
        foreach ($directReports as $employee) {
            $this->updateEmployeeMetrics($employee);
        }

        $alertEmployees = $directReports->filter(function ($employee) {
            $recentOvertime = $employee->workLifeBalanceMetrics()
                ->where('week_start', '>=', now()->subWeeks(2)->startOfWeek())
                ->sum('overtime_hours');
            return $recentOvertime > 20;
        });

        return view('work-life-balance.manager-dashboard', compact(
            'directReports',
            'alertEmployees'
        ));
    }

    private function updateMetrics()
    {
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $this->updateEmployeeMetrics($employee);
        }
    }

    private function updateEmployeeMetrics(Employee $employee)
    {
        $weekStart = now()->startOfWeek();
        
        $metric = WorkLifeBalanceMetric::firstOrCreate(
            ['employee_id' => $employee->id, 'week_start' => $weekStart],
            [
                'overtime_hours' => $employee->getWeeklyOvertimeHours($weekStart),
                'consecutive_work_days' => $employee->getConsecutiveWorkDays(),
                'leave_balance_ratio' => $employee->getLeaveBalanceRatio(),
            ]
        );

        $metric->update([
            'overtime_hours' => $employee->getWeeklyOvertimeHours($weekStart),
            'consecutive_work_days' => $employee->getConsecutiveWorkDays(),
            'leave_balance_ratio' => $employee->getLeaveBalanceRatio(),
        ]);
    }
}