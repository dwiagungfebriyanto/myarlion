<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['channel_name'];

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
