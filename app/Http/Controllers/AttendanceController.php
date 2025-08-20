<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\QrCode;
use App\Models\OfficeLocation;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the attendance scanner page.
     */
    public function index()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();
        
        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $today = today();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        $recentAttendances = Attendance::where('employee_id', $employee->id)
            ->with(['officeLocation'])
            ->latest('date')
            ->take(5)
            ->get();

        return Inertia::render('attendance/scanner', [
            'employee' => $employee,
            'todayAttendance' => $todayAttendance,
            'recentAttendances' => $recentAttendances,
            'canCheckIn' => !$todayAttendance || !$todayAttendance->check_in_time,
            'canCheckOut' => $todayAttendance && $todayAttendance->check_in_time && !$todayAttendance->check_out_time,
        ]);
    }

    /**
     * Process QR code scan and attendance.
     */
    public function store(StoreAttendanceRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found.'], 404);
        }

        // Validate QR code
        $qrCode = QrCode::where('code', $validated['qr_code'])
            ->with('officeLocation')
            ->valid()
            ->first();

        if (!$qrCode) {
            return response()->json(['error' => 'Invalid or expired QR code.'], 400);
        }

        $officeLocation = $qrCode->officeLocation;

        // Verify GPS location
        if (!$officeLocation->isWithinRadius($validated['latitude'], $validated['longitude'])) {
            $distance = $officeLocation->calculateDistance($validated['latitude'], $validated['longitude']);
            return response()->json([
                'error' => 'You are too far from the office location.',
                'distance' => round($distance),
                'allowed_radius' => $officeLocation->radius_meters
            ], 400);
        }

        $today = today();
        $now = now();

        // Get or create today's attendance record
        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today,
            ],
            [
                'qr_code_id' => $qrCode->id,
                'office_location_id' => $officeLocation->id,
                'status' => 'present',
            ]
        );

        $type = $validated['type']; // 'check_in' or 'check_out'
        $distance = $officeLocation->calculateDistance($validated['latitude'], $validated['longitude']);

        if ($type === 'check_in') {
            if ($attendance->check_in_time) {
                return response()->json(['error' => 'Already checked in today.'], 400);
            }

            $attendance->update([
                'check_in_time' => $now,
                'check_in_latitude' => $validated['latitude'],
                'check_in_longitude' => $validated['longitude'],
                'status' => $now->hour >= 8 ? 'late' : 'present',
            ]);

            $message = 'Successfully checked in!';
        } else {
            if (!$attendance->check_in_time) {
                return response()->json(['error' => 'Must check in first.'], 400);
            }

            if ($attendance->check_out_time) {
                return response()->json(['error' => 'Already checked out today.'], 400);
            }

            $attendance->update([
                'check_out_time' => $now,
                'check_out_latitude' => $validated['latitude'],
                'check_out_longitude' => $validated['longitude'],
            ]);

            $message = 'Successfully checked out!';
        }

        // Log the attendance action
        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'type' => $type,
            'logged_at' => $now,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'distance_meters' => (int) $distance,
            'device_info' => $request->header('User-Agent'),
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'attendance' => $attendance->fresh(),
            'working_hours' => $attendance->getWorkingHours(),
        ]);
    }

    /**
     * Display attendance reports.
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $period = $request->get('period', 'week'); // day, week, month
        $date = $request->get('date', now()->format('Y-m-d'));

        $targetDate = Carbon::parse($date);
        $attendances = collect();
        
        switch ($period) {
            case 'day':
                $attendances = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $targetDate)
                    ->with(['officeLocation'])
                    ->get();
                break;
            case 'week':
                $attendances = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [
                        $targetDate->startOfWeek()->format('Y-m-d'),
                        $targetDate->endOfWeek()->format('Y-m-d')
                    ])
                    ->with(['officeLocation'])
                    ->orderBy('date')
                    ->get();
                break;
            case 'month':
                $attendances = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [
                        $targetDate->startOfMonth()->format('Y-m-d'),
                        $targetDate->endOfMonth()->format('Y-m-d')
                    ])
                    ->with(['officeLocation'])
                    ->orderBy('date')
                    ->get();
                break;
        }

        // Calculate statistics
        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $totalWorkingHours = $attendances->sum(function ($attendance) {
            return $attendance->getWorkingHours() ?? 0;
        });

        return Inertia::render('attendance/report', [
            'employee' => $employee,
            'attendances' => $attendances,
            'period' => $period,
            'date' => $date,
            'statistics' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'total_working_hours' => round($totalWorkingHours, 1),
                'average_working_hours' => $totalDays > 0 ? round($totalWorkingHours / $totalDays, 1) : 0,
            ],
        ]);
    }
}