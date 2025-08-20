<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\OfficeLocation
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property float $latitude
 * @property float $longitude
 * @property int $radius_meters
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QrCode[] $qrCodes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendances
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereRadiusMeters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeLocation active()
 * @method static \Database\Factories\OfficeLocationFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class OfficeLocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'office_locations';

    /**
     * Get the QR codes for the office location.
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * Get the attendances for the office location.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Scope a query to only include active locations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate distance between two coordinates in meters.
     */
    public function calculateDistance(float $lat, float $lng): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

    /**
     * Check if coordinates are within allowed radius.
     */
    public function isWithinRadius(float $lat, float $lng): bool
    {
        return $this->calculateDistance($lat, $lng) <= $this->radius_meters;
    }
}