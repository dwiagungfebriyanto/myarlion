<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Inquiry extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'name',
        'customer_category',
        'customer_id',
        'country_code',
        'city',
        'channel_id',
        'website_id',
        'user_id',
        'phone',
        'email',
        'note',
        'destination_id',
        'platform',
        'job_id',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('inquiry')
            ->logFillable();
    }

    public function job(){
        return $this->belongsTo(Job::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'id');
    }

    public function destination()
    {
        return $this->belongsTo(Country::class, 'destination_id', 'id');
    }

    public function salesPerson()
    {
        return $this->belongsTo(User::class);
    }

    public function inquiryProducts()
    {
        return $this->hasMany(InquiryProduct::class);
    }

    // public function product ()
    // {
    //     return $this->belongsToMany(Product::class, 'inquiries_has_products', 'inquiry_id', 'product_sku');
    // }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
