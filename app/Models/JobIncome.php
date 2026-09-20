<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JobIncome extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id',
        'bank_account_id',
        'date',
        'currency_id',
        'payment',
        'outstanding',
        'nominal',
        'to_idr',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('job_income')
            ->logFillable();
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
