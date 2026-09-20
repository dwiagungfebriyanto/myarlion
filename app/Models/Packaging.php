<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Packaging extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'main_category_id',
        'code',
        'packaging_name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('packaging')
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
}
