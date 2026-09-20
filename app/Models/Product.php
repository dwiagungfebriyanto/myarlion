<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'main_category_id',
        'sub_category_id',
        'product_type_id',
        'brand_id',
        'specification_id',
        'packaging_id',
        'supplier_id',
        'unit_id',
        'image',
        'barcode',
        'qty',
        'note',
        'harga_rata_rata',
        'harga_tertinggi',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('product')
            ->logFillable();
    }

    public function main_category()
    {
        return $this->belongsTo(Main_Category::class);
    }

    public function sub_category()
    {
        return $this->belongsTo(Sub_Category::class);
    }

    public function product_type()
    {
        return $this->belongsTo(Product_Type::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function packaging()
    {
        return $this->belongsTo(Packaging::class);
    }

    public function specification()
    {
        return $this->belongsTo(Specification::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function inventoryIns()
    {
        return $this->hasMany(InventoryIn::class);
    }

    public function inventoryLosts()
    {
        return $this->hasMany(InventoryLost::class);
    }

    public function inventoryMutations()
    {
        return $this->hasMany(InventoryMutation::class, 'original_sku', 'sku');
    }

    public function inventoryOuts()
    {
        return $this->hasMany(InventoryOut::class);
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function poStocks()
    {
        return $this->belongsToMany(PoStock::class);
    }

    public function poStockProducts()
    {
        return $this->hasMany(PoStockProduct::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'jobs_has_products')
            ->withPivot(['id', 'quantity', 'price', 'note'])
            ->withTimestamps();
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public static function readyStock()
    {
        return static::where('qty', '>', 0)->get();
    }

    public function skuFormat()
    {
        return "$this->sku | "
            . $this->product_type->product_type_name . " | "
            . $this->specification->specification_name . " | "
            . $this->packaging->packaging_name;
    }
}
