<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Main_Category extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'main_categories';

    protected $fillable = [
        'code',
        'main_category_name',
        'main_category_icon',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('main_category')
            ->logFillable();
    }

    public function sub_category()
    {
        return $this->hasMany(Sub_Category::class);
    }

    public function poStocks()
    {
        return $this->hasMany(PoStock::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function supplier()
    {
        return $this->hasMany(Supplier::class);
    }

    public function specification()
    {
        return $this->hasMany(Specification::class);
    }

    public function product_type()
    {
        return $this->hasMany(Product_Type::class);
    }

    public function packaging()
    {
        return $this->hasMany(Packaging::class);
    }

    public function brand()
    {
        return $this->hasMany(Brand::class);
    }
}
