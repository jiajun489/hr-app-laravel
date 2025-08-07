<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkLifeBalanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'week_start',
        'overtime_hours',
        'consecutive_work_days',
        'leave_balance_ratio',
    ];

    protected $casts = [
        'week_start' => 'date',
        'overtime_hours' => 'decimal:2',
        'leave_balance_ratio' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}