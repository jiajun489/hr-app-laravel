<?php
// app/Models/Presence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Presence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'check_in',
        'check_out',
        'date',
        'status',
        'latitude',     
        'longitude',    
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date',
    ];

    /**
     * Get the employee associated with this presence.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get check_in time in local timezone (GMT+8)
     */
    public function getCheckInLocalAttribute()
    {
        return $this->check_in ? $this->check_in->setTimezone('Asia/Kuala_Lumpur') : null;
    }

    /**
     * Get check_out time in local timezone (GMT+8)
     */
    public function getCheckOutLocalAttribute()
    {
        return $this->check_out ? $this->check_out->setTimezone('Asia/Kuala_Lumpur') : null;
    }

    /**
     * Get formatted check_in time for display
     */
    public function getFormattedCheckInAttribute()
    {
        return $this->check_in_local ? $this->check_in_local->format('H:i') : '-';
    }

    /**
     * Get formatted check_out time for display
     */
    public function getFormattedCheckOutAttribute()
    {
        return $this->check_out_local ? $this->check_out_local->format('H:i') : '-';
    }

    /**
     * Calculate working hours
     */
    public function getWorkingHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        return $this->check_out->diffInHours($this->check_in);
    }
}
