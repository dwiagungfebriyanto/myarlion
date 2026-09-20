<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'country_code',
        'country_name',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function jobs()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'country_code', 'id');
    }

    public function ports()
    {
        return $this->hasMany(Port::class);
    }

    public function inquiries_destination()
    {
        return $this->hasMany(Inquiry::class, 'destination_id', 'id');
    }
}
