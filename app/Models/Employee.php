<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
        protected $fillable = [
        'employee_id', 
        'name',
        'department_id',
        'address',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }
}
