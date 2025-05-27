<?php
// app/Models/Presence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'check_in' => 'datetime:Y-m-d H:i',
        'check_out' => 'datetime:Y-m-d H:i',
        'date' => 'date:Y-m-d',
    ];

    /**
     * Get the employee associated with this presence.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
