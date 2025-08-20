<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\QrCode;
use Carbon\Carbon;

class AttendanceSystemSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create office locations
        $mainOffice = OfficeLocation::create([
            'name' => 'Kantor Pusat Jakarta',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'radius_meters' => 100,
            'is_active' => true,
        ]);

        $branchOffice = OfficeLocation::create([
            'name' => 'Kantor Cabang Surabaya',
            'address' => 'Jl. Pemuda No. 45, Surabaya',
            'latitude' => -7.257472,
            'longitude' => 112.752090,
            'radius_meters' => 100,
            'is_active' => true,
        ]);

        // Create users and employees
        $admin = User::create([
            'name' => 'Admin System',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $admin->id,
            'employee_id' => 'EMP001',
            'name' => 'Admin System',
            'email' => 'admin@company.com',
            'phone' => '081234567890',
            'department' => 'IT',
            'position' => 'System Administrator',
            'role' => 'admin',
            'status' => 'active',
            'hire_date' => '2024-01-01',
        ]);

        $hrd = User::create([
            'name' => 'HRD Manager',
            'email' => 'hrd@company.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $hrd->id,
            'employee_id' => 'EMP002',
            'name' => 'HRD Manager',
            'email' => 'hrd@company.com',
            'phone' => '081234567891',
            'department' => 'Human Resources',
            'position' => 'HRD Manager',
            'role' => 'hrd',
            'status' => 'active',
            'hire_date' => '2024-01-01',
        ]);

        // Create sample employees
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'john@company.com',
                'employee_id' => 'EMP003',
                'department' => 'Engineering',
                'position' => 'Software Developer',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@company.com',
                'employee_id' => 'EMP004',
                'department' => 'Marketing',
                'position' => 'Marketing Specialist',
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@company.com',
                'employee_id' => 'EMP005',
                'department' => 'Sales',
                'position' => 'Sales Executive',
            ],
        ];

        foreach ($employees as $employeeData) {
            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_id' => $employeeData['employee_id'],
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'phone' => '0812345678' . random_int(10, 99),
                'department' => $employeeData['department'],
                'position' => $employeeData['position'],
                'role' => 'employee',
                'status' => 'active',
                'hire_date' => Carbon::now()->subMonths(random_int(1, 12))->format('Y-m-d'),
            ]);
        }

        // Create QR codes for each office location
        QrCode::create([
            'code' => 'OFFICE_JAKARTA_' . now()->format('Ymd'),
            'office_location_id' => $mainOffice->id,
            'expires_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        QrCode::create([
            'code' => 'OFFICE_SURABAYA_' . now()->format('Ymd'),
            'office_location_id' => $branchOffice->id,
            'expires_at' => now()->addDays(30),
            'is_active' => true,
        ]);
    }
}