<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $department = $request->get('department');
        $role = $request->get('role');
        $status = $request->get('status');

        $employees = Employee::query()
            ->with('user')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('employee_id', 'like', "%{$search}%");
                });
            })
            ->when($department, function ($query, $department) {
                return $query->where('department', $department);
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $departments = Employee::distinct()->pluck('department')->filter();
        
        return Inertia::render('admin/employees/index', [
            'employees' => $employees,
            'departments' => $departments,
            'filters' => $request->only(['search', 'department', 'role', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $departments = Employee::distinct()->pluck('department')->filter();
        
        return Inertia::render('admin/employees/create', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created employee.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        // Create user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Create employee profile
        Employee::create([
            'user_id' => $user->id,
            'employee_id' => $validated['employee_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'],
            'position' => $validated['position'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'hire_date' => $validated['hire_date'],
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'attendances' => function ($query) {
            $query->with('officeLocation')->latest('date')->take(10);
        }]);

        // Calculate attendance statistics
        $currentMonth = now()->startOfMonth();
        $monthlyAttendances = $employee->attendances()
            ->whereBetween('date', [$currentMonth, now()])
            ->get();

        $statistics = [
            'total_days' => $monthlyAttendances->count(),
            'present_days' => $monthlyAttendances->where('status', 'present')->count(),
            'late_days' => $monthlyAttendances->where('status', 'late')->count(),
            'absent_days' => now()->diffInDaysFiltered(function ($date) use ($monthlyAttendances) {
                return $date->isWeekday() && !$monthlyAttendances->contains('date', $date->format('Y-m-d'));
            }, $currentMonth),
        ];

        return Inertia::render('admin/employees/show', [
            'employee' => $employee,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $employee->load('user');
        $departments = Employee::distinct()->pluck('department')->filter();
        
        return Inertia::render('admin/employees/edit', [
            'employee' => $employee,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified employee.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $validated = $request->validated();

        // Update user account
        $employee->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update employee profile
        $employee->update($validated);

        return redirect()->route('admin.employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        $employee->user->delete(); // This will cascade delete the employee
        
        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}