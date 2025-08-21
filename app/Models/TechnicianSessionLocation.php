<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianSessionLocation extends Model
{
    use HasFactory;

    public $timestamps = false; // logged_at maneja la temporalidad

    protected $fillable = [
        'technician_session_id',
        'latitude',
        'longitude',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(TechnicianSession::class, 'technician_session_id');
    }
}
