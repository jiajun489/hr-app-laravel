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
        'work_life_score',
    ];

    protected $casts = [
        'week_start' => 'date',
        'overtime_hours' => 'decimal:2',
        'leave_balance_ratio' => 'decimal:2',
        'work_life_score' => 'decimal:1',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getWorkLifeScoreAttribute($value)
    {
        return round($value, 1);
    }

    public function getScoreColorAttribute()
    {
        if ($this->work_life_score >= 8.0) return 'success';
        if ($this->work_life_score >= 6.0) return 'warning';
        return 'danger';
    }

    public function getScoreStatusAttribute()
    {
        if ($this->work_life_score >= 8.0) return 'Excellent';
        if ($this->work_life_score >= 6.0) return 'Good';
        if ($this->work_life_score >= 4.0) return 'Fair';
        return 'Poor';
    }
}