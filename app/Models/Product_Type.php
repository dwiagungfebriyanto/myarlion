<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product_Type extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'product_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'main_category_id',
        'code',
        'product_type_name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('product_type')
            ->logFillable();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function mainCategory()
    {
        return $this->belongsTo(Main_Category::class);
    }

    public function inquiryProducts()
    {
        return $this->hasMany(InquiryProduct::class, 'product_type_id', 'id');
    }
}
