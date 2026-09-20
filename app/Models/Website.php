<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'web_domain'
    ];

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }
}
