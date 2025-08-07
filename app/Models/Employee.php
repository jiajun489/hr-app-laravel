<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
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
}
