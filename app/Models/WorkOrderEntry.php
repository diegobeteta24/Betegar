<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderEntry extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'user_id',
        'work_date',
        'progress',
        'requests',
        'signed_at',
        'signature_by',
    ];

    protected $casts = [
        'work_date' => 'date',
        'signed_at' => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function signature()
    {
        return $this->morphOne(Image::class, 'imageable')->where('tag', 'signature');
    }
}
