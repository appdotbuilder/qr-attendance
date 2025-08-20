<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\QrCode;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create office location
        $this->officeLocation = OfficeLocation::factory()->create([
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'radius_meters' => 100,
        ]);

        // Create QR code
        $this->qrCode = QrCode::factory()->create([
            'office_location_id' => $this->officeLocation->id,
            'code' => 'TEST_QR_CODE',
        ]);

        // Create user and employee
        $this->user = User::factory()->create();
        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_attendance_scanner_page_loads()
    {
        $response = $this->actingAs($this->user)
            ->get('/attendance');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('attendance/scanner')
            ->has('employee')
            ->has('canCheckIn')
            ->has('canCheckOut')
        );
    }

    public function test_employee_can_check_in_with_valid_qr_and_location()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/attendance', [
                'qr_code' => $this->qrCode->code,
                'type' => 'check_in',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
        ]);
    }

    public function test_employee_cannot_check_in_with_invalid_location()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/attendance', [
                'qr_code' => $this->qrCode->code,
                'type' => 'check_in',
                'latitude' => -6.300000, // Too far from office
                'longitude' => 106.916666,
            ]);

        $response->assertStatus(400)
            ->assertJsonStructure(['error']);

        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $this->employee->id,
        ]);
    }

    public function test_employee_cannot_check_in_with_invalid_qr_code()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/attendance', [
                'qr_code' => 'INVALID_QR_CODE',
                'type' => 'check_in',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);

        $response->assertStatus(400)
            ->assertJsonStructure(['error']);
    }

    public function test_employee_can_check_out_after_check_in()
    {
        // First check in
        Attendance::create([
            'employee_id' => $this->employee->id,
            'qr_code_id' => $this->qrCode->id,
            'office_location_id' => $this->officeLocation->id,
            'date' => today(),
            'check_in_time' => now(),
            'check_in_latitude' => -6.200000,
            'check_in_longitude' => 106.816666,
            'status' => 'present',
        ]);

        // Then check out
        $response = $this->actingAs($this->user)
            ->postJson('/attendance', [
                'qr_code' => $this->qrCode->code,
                'type' => 'check_out',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
        ]);

        $attendance = Attendance::where('employee_id', $this->employee->id)
            ->whereDate('date', today())
            ->first();
            
        $this->assertNotNull($attendance->check_out_time);
    }

    public function test_unauthenticated_user_cannot_access_attendance()
    {
        $response = $this->get('/attendance');
        $response->assertRedirect('/login');

        $response = $this->postJson('/attendance', [
            'qr_code' => $this->qrCode->code,
            'type' => 'check_in',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);
        $response->assertStatus(401);
    }
}