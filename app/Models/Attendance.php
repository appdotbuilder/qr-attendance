<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Attendance
 *
 * @property int $id
 * @property int $employee_id
 * @property int $qr_code_id
 * @property int $office_location_id
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $check_in_time
 * @property string|null $check_out_time
 * @property float|null $check_in_latitude
 * @property float|null $check_in_longitude
 * @property float|null $check_out_latitude
 * @property float|null $check_out_longitude
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @property-read \App\Models\QrCode $qrCode
 * @property-read \App\Models\OfficeLocation $officeLocation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AttendanceLog[] $logs
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereQrCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereOfficeLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCheckInTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCheckOutTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCheckInLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCheckInLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCheckOutLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCheckOutLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance today()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance thisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance thisMonth()
 * @method static \Database\Factories\AttendanceFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'qr_code_id',
        'office_location_id',
        'date',
        'check_in_time',
        'check_out_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'check_in_latitude' => 'float',
        'check_in_longitude' => 'float',
        'check_out_latitude' => 'float',
        'check_out_longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendances';

    /**
     * Get the employee that owns the attendance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the QR code that was used for the attendance.
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class);
    }

    /**
     * Get the office location for the attendance.
     */
    public function officeLocation(): BelongsTo
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    /**
     * Get the logs for the attendance.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    /**
     * Scope a query to only include today's attendances.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope a query to only include this week's attendances.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope a query to only include this month's attendances.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    /**
     * Calculate working hours.
     */
    public function getWorkingHours(): ?float
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return null;
        }

        $checkIn = \Carbon\Carbon::parse($this->check_in_time);
        $checkOut = \Carbon\Carbon::parse($this->check_out_time);

        return $checkOut->diffInHours($checkIn);
    }

    /**
     * Check if employee is late.
     */
    public function isLate(): bool
    {
        if (!$this->check_in_time) {
            return false;
        }

        $workStartTime = today()->setTime(8, 0); // 8:00 AM
        return $this->check_in_time > $workStartTime;
    }
}