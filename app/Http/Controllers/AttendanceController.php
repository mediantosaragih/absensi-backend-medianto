<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceHistory;
use Illuminate\Support\Str;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,employee_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Employee::where('employee_id', $request->employee_id)->first();

        $already = Attendance::where('employee_id', $request->employee_id)
                    ->whereDate('clock_in', today())
                    ->first();

        if ($already) {
            return response()->json(['message' => 'Sudah absen masuk hari ini'], 409);
        }

        $attendanceId = Str::uuid();

        $attendance = Attendance::create([
            'attendance_id' => $attendanceId,
            'employee_id'   => $employee->employee_id,
            'clock_in'      => now(),
        ]);

        AttendanceHistory::create([
            'attendance_id'     => $attendanceId,
            'employee_id'       => $employee->employee_id,
            'date_attendance'   => now(),
            'attendance_type'   => 1,
            'description'       => 'Clock In',
        ]);

        return response()->json([
            'message'        => 'Absen masuk berhasil',
            'employee_id'    => $employee->employee_id,
            'employee_name'  => $employee->name,
            'clock_in_time'  => $attendance->clock_in->format('H:i:s'),
            'date'           => $attendance->clock_in->toDateString(),
        ]);
    }

   public function clockOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,employee_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Employee::where('employee_id', $request->employee_id)->first();
        $employeeId = $request->employee_id;

        // Cek apakah ada clock in hari ini
        $attendance = Attendance::where('employee_id', $employeeId)
                        ->whereDate('clock_in', today())
                        ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Belum melakukan clock in hari ini'], 404);
        }

        // Jika sudah clock out
        if ($attendance->clock_out) {
                return response()->json([
                    'message'        => 'Berhasil Clock Out hair ini',
                    'employee_id'    => $employee->employee_id,
                    'employee_name'  => $employee->name,
                    'clock_out_time'  => Carbon::parse($attendance->clock_in)->format('H:i:s'),
                    'date'           => Carbon::parse($attendance->clock_in)->toDateString(),
                ]);
        }

        // Update clock out
        $attendance->update([
            'clock_out' => now()
        ]);

        // Catat ke riwayat absensi
        AttendanceHistory::create([
            'employee_id'       => $employeeId,
            'attendance_id'     => $attendance->attendance_id,
            'date_attendance'   => today(),
            'attendance_type'   => 2,
            'description'       => 'Clock Out'
        ]);

        return response()->json([
                    'message'        => 'Berhasil Clock Out hair ini',
                    'employee_id'    => $employee->employee_id,
                    'employee_name'  => $employee->name,
                    'clock_out_time'  => Carbon::parse($attendance->clock_in)->format('H:i:s'),
                    'date'           => Carbon::parse($attendance->clock_in)->toDateString(),
                ]);
    }

}
