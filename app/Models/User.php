<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use HasPushSubscriptions;
    use TwoFactorAuthenticatable;
     use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    'technician_balance',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships for work orders / sessions / expenses / funds
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'user_id');
    }

    public function technicianSessions()
    {
        return $this->hasMany(TechnicianSession::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'technician_id');
    }

    public function sentFundTransfers()
    {
        return $this->hasMany(FundTransfer::class, 'admin_id');
    }

    public function receivedFundTransfers()
    {
        return $this->hasMany(FundTransfer::class, 'technician_id');
    }

    // Balance: sum(fund_transfers.amount) - sum(expenses.amount)
    public function getTechnicianBalanceAttribute(): string
    {
        if (! $this->hasRole('technician')) return '0.00';
        $in = (float) $this->receivedFundTransfers()->sum('amount');
        $out = (float) $this->expenses()->sum('amount');
        return number_format($in - $out, 2, '.', '');
    }
}
