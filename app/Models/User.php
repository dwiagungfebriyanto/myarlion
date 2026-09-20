<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'position',
        'username',
        'email',
        'password',
        'signature',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logFillable();
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'employee_id', 'id');
    }

    public function salesTargets()
    {
        return $this->hasMany(SalesTarget::class);
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public static function activeMarketing()
    {
        return self::where('role_id', 3)
            ->where('is_former_employee', false)
            ->orderBy('name')
            ->get();
    }

    public static function marketing()
    {
        return self::where('role_id', 3)->orderBy('name')->get();
    }
}
