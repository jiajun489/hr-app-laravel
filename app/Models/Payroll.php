<?php
// app/Models/Payroll.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'salary',
        'bonus',
        'deduction',
        'net_salary',
        'payment_date',
    ];

    protected $casts = [
        'salary'       => 'decimal:2',
        'bonus'        => 'decimal:2',
        'deduction'    => 'decimal:2',
        'net_salary'   => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the employee that this payroll belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getNetSalaryAttribute()
    {
        return ($this->salary ?? 0) + ($this->bonus ?? 0) - ($this->deduction ?? 0);
    }

}
