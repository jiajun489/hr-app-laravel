<?php
// app/Models/Department.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Get the employees that belong to this department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
