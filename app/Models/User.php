<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, UserNotify;

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'is_staff',
        'role',
        'manager_id',
        'image',
        'dial_code',
        'mobile',
        'balance',
        'password',
        'country_name',
        'country_code',
        'city',
        'state',
        'zip',
        'address',
        'status',
        'kyc_data',
        'kyc_rejection_reason',
        'kv',
        'ev',
        'sv',
        'profile_complete',
        'ver_code',
        'ver_code_send_at',
        'ts',
        'tv',
        'tsc',
        'ban_reason',
        'remember_token',
        'provider',
        'provider_id',
        'date_of_birth',
        'id_number',
        'id_issue_date',
        'id_issue_place',
        'bank_account_number',
        'bank_name',
        'bank_branch',
        'bank_account_holder',
        'tax_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'ver_code', 'balance', 'kyc_data'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime',
        'is_staff' => 'boolean',
        'id_issue_date' => 'date',
        'date_of_birth' => 'date',
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function invests()
    {
        return $this->hasMany(Invest::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function staffMembers()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn () => $this->dial_code . $this->mobile,
        );
    }

    public function isSalesManager(): bool
    {
        return $this->is_staff && $this->role === 'sales_manager';
    }

    public function isSalesStaff(): bool
    {
        return $this->is_staff && $this->role === 'sales_staff';
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function scopeSalesManagers($query)
    {
        return $query->where('is_staff', true)->where('role', 'sales_manager');
    }

    public function scopeSalesStaff($query)
    {
        return $query->where('is_staff', true)->where('role', 'sales_staff');
    }
    
    public function isAdmin(): bool
    {
        return false; // Regular users can't be admins
    }
    
    public function isManager(): bool
    {
        return $this->is_staff && (
            $this->role === 'sales_manager' || 
            $this->role === 'manager' || 
            str_contains($this->role, 'manager')
        );
    }
    
    public function isStaff(): bool
    {
        return $this->is_staff && !$this->isManager();
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function notifications()
    {
        return $this->hasMany(NotificationLog::class);
    }

    // Staff Salary & KPI Relationships
    public function staffSalaries()
    {
        return $this->hasMany(StaffSalary::class, 'staff_id');
    }

    public function managedSalaries()
    {
        return $this->hasMany(StaffSalary::class, 'manager_id');
    }

    public function staffKpis()
    {
        return $this->hasMany(StaffKPI::class, 'staff_id');
    }

    public function managedKpis()
    {
        return $this->hasMany(StaffKPI::class, 'manager_id');
    }

}
