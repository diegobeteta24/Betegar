<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_latitude',
        'start_longitude',
        'started_at',
        'started_on_date',
        'end_latitude',
        'end_longitude',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'started_on_date' => 'date',
        'ended_at' => 'datetime',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->hasMany(TechnicianSessionLocation::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('ended_at');
    }

    // Accessor: started_at_local (America/Guatemala)
    public function getStartedAtLocalAttribute()
    {
        $tz = config('app.tz_guatemala', 'America/Guatemala');
        return $this->started_at?->copy()->setTimezone($tz);
    }

    protected function startedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn($value) => $value ? $this->asDateTime($value)->setTimezone(config('app.tz_guatemala','America/Guatemala')) : null,
            set: fn($value) => $value
        );
    }

    protected function endedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn($value) => $value ? $this->asDateTime($value)->setTimezone(config('app.tz_guatemala','America/Guatemala')) : null,
            set: fn($value) => $value
        );
    }
}
