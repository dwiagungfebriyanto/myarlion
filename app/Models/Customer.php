<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'code',
        'tax',
        'no_rekening',
        'address',
        'country_id',
        'telp',
        'fax',
        'email',
        'contact',
        'note'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('customer')
            ->logFillable();
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
