<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'customer_id',
		'user_id', // technician
		'address',
		'objective',
		'status',
	];

	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
	];

	// Relationships
	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function technician()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function technicians()
	{
		// Explicit pivot table to match migration name
		return $this->belongsToMany(User::class, 'work_order_user')->withTimestamps();
	}

	public function entries()
	{
		return $this->hasMany(WorkOrderEntry::class);
	}

	// Scopes
	public function scopePending($query)
	{
		return $query->where('status', 'pending');
	}

	public function scopeForTechnician($query, int $userId)
	{
		// Support both legacy single user_id and new pivot assignment
		return $query->where(function($q) use ($userId) {
			$q->where('user_id', $userId)
			  ->orWhereExists(function($sq) use ($userId) {
					$sq->selectRaw(1)
					   ->from('work_order_user as wou')
					   ->whereColumn('wou.work_order_id', 'work_orders.id')
					   ->where('wou.user_id', $userId);
			  });
		});
	}
}

