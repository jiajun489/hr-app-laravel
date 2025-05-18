<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'status',
        'salary',
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
}
