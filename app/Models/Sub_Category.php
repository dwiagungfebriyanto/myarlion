<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sub_Category extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'sub_categories';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'main_category_id',
        'code',
        'category_name',
        'category_icon',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('sub_category')
            ->logFillable();
    }

    public function main_category()
    {
        return $this->belongsTo(Main_Category::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function products()
    {
        // return $this->hasMany(Product::class, 'category_id', 'id');
        return $this->hasMany(Product::class);
    }

}
