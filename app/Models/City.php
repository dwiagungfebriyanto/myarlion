<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'province_id',
        'city_name',
    ];

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
