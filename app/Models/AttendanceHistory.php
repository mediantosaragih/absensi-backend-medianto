<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceHistory extends Model
{
    protected $fillable = [
        'attendance_id',
        'employee_id',
        'date_attendance',
        'attendance_type', // 1 = in, 2 = out
        'description'
    ];
    
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id', 'attendance_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
