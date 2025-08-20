<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * App\Models\QrCode
 *
 * @property int $id
 * @property string $code
 * @property int $office_location_id
 * @property \Illuminate\Support\Carbon $expires_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OfficeLocation $officeLocation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendances
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereOfficeLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode active()
 * @method static \Illuminate\Database\Eloquent\Builder|QrCode valid()
 * @method static \Database\Factories\QrCodeFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class QrCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'office_location_id',
        'expires_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'qr_codes';

    /**
     * Get the office location that owns the QR code.
     */
    public function officeLocation(): BelongsTo
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    /**
     * Get the attendances for the QR code.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Scope a query to only include active QR codes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include valid QR codes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                     ->where('expires_at', '>', now());
    }

    /**
     * Generate a unique QR code.
     */
    public static function generateCode(): string
    {
        do {
            $code = Str::random(32);
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check if the QR code is valid.
     */
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at->isFuture();
    }
}