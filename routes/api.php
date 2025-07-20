<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;

// Cek API
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
 
// Endpoint Absensi
Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
Route::put('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
Route::get('/attendance/logs', [AttendanceController::class, 'logAbsensi']);
