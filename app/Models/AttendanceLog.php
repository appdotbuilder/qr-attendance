<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\AttendanceLog
 *
 * @property int $id
 * @property int $attendance_id
 * @property string $type
 * @property \Illuminate\Support\Carbon $logged_at
 * @property float $latitude
 * @property float $longitude
 * @property int $distance_meters
 * @property string|null $device_info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Attendance $attendance
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereAttendanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereLoggedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereDistanceMeters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereDeviceInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceLog whereUpdatedAt($value)
 * @method static \Database\Factories\AttendanceLogFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class AttendanceLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'attendance_id',
        'type',
        'logged_at',
        'latitude',
        'longitude',
        'distance_meters',
        'device_info',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'logged_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'distance_meters' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendance_logs';

    /**
     * Get the attendance that owns the log.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}