<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fullname',
        'email',
        'phone',
        'address',
        'birth_date',
        'hire_date',
        'department_id',
        'role_id',
        'manager_id',
        'status',
        'salary',
        'annual_leave_days',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
    ];

    /**
     * Get the department this employee belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the role this employee belongs to.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function directReports()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function workLifeBalanceMetrics()
    {
        return $this->hasMany(WorkLifeBalanceMetric::class);
    }

    public function getWeeklyOvertimeHours($weekStart)
    {
        return $this->presences()
            ->whereBetween('date', [$weekStart, $weekStart->copy()->addDays(6)])
            ->get()
            ->sum(function ($presence) {
                if (!$presence->check_in || !$presence->check_out) return 0;
                $hours = $presence->check_in->diffInHours($presence->check_out);
                return max(0, $hours - 8);
            });
    }

    public function getLeaveBalanceRatio()
    {
        $usedDays = $this->leaveRequests()
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->get()
            ->sum(function ($leave) {
                return $leave->start_date->diffInDays($leave->end_date) + 1;
            });
        
        return $this->annual_leave_days > 0 ? $usedDays / $this->annual_leave_days : 0;
    }

    public function getConsecutiveWorkDays()
    {
        $recentPresences = $this->presences()
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();
        
        $consecutive = 0;
        $currentDate = now()->startOfDay();
        
        foreach ($recentPresences as $presence) {
            if ($presence->date->eq($currentDate)) {
                $consecutive++;
                $currentDate = $currentDate->subDay();
            } else {
                break;
            }
        }
        
        return $consecutive;
    }

    public function calculateWorkLifeScore()
    {
        $score = 10.0;
        
        // Overtime penalty (max -3 points)
        $weeklyOvertime = $this->getWeeklyOvertimeHours(now()->startOfWeek());
        if ($weeklyOvertime > 20) $score -= 3;
        elseif ($weeklyOvertime > 10) $score -= 2;
        elseif ($weeklyOvertime > 5) $score -= 1;
        
        // Consecutive work days penalty (max -2 points)
        $consecutiveDays = $this->getConsecutiveWorkDays();
        if ($consecutiveDays > 10) $score -= 2;
        elseif ($consecutiveDays > 7) $score -= 1;
        
        // Leave usage bonus/penalty (max -2 points)
        $leaveRatio = $this->getLeaveBalanceRatio();
        if ($leaveRatio < 0.1) $score -= 2; // Not taking enough leave
        elseif ($leaveRatio < 0.3) $score -= 1;
        elseif ($leaveRatio > 0.8) $score += 0.5; // Good leave usage
        
        // Weekend work penalty
        $weekendWork = $this->presences()
            ->where('date', '>=', now()->subWeeks(4))
            ->whereRaw('EXTRACT(DOW FROM date) IN (0, 6)') // Sunday = 0, Saturday = 6 in PostgreSQL
            ->count();
        if ($weekendWork > 4) $score -= 1.5;
        elseif ($weekendWork > 2) $score -= 0.5;
        
        return max(0, min(10, $score));
    }

    public function getAverageWorkHoursPerDay()
    {
        return $this->presences()
            ->where('date', '>=', now()->subWeeks(4))
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->get()
            ->avg(function ($presence) {
                return $presence->check_in->diffInHours($presence->check_out);
            }) ?? 0;
    }

    public function getWorkLifeBalanceStatus()
    {
        $score = $this->calculateWorkLifeScore();
        
        if ($score >= 8.0) return ['status' => 'Excellent', 'color' => 'success'];
        if ($score >= 6.0) return ['status' => 'Good', 'color' => 'warning'];
        if ($score >= 4.0) return ['status' => 'Fair', 'color' => 'info'];
        return ['status' => 'Poor', 'color' => 'danger'];
    }
}
