<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use SoftDeletes;
    protected $fillable = ['quote_id','user_id','type','notes','remind_at','completed'];

    protected $casts = [
        'remind_at' => 'datetime',
        'completed' => 'boolean',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
