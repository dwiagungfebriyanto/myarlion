<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JobStatement extends Model
{
    use HasFactory, LogsActivity;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'job_id',
        'inventory_stock_id',
        'quantity',
        'price_per_unit',
        'total',
        'stock_cost',
        'stock_cost_per_unit',
    ];

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('cost')
            ->logFillable();
    }

    public function job(){
        return $this->belongsTo(Job::class);
    }

    public function inventoryStock(){
        return $this->belongsTo(InventoryStock::class);
    }

    public function sampleCosts()
    {
        return $this->hasMany(OutcomeCheque::class, 'job_statement_id', 'id')
            ->where('code_type', 'job')
            ->where('outcome_type_id', 208001);
    }

    public function productType()
    {
        $inventoryStock  = $this->inventoryStock;
        $product = $inventoryStock->product;
        $productType = $product->product_type;

        return $productType->product_type_name;
    }

    public function productSKu()
    {
        $inventoryStock  = $this->inventoryStock;
        $product = $inventoryStock->product;

        return $product->sku;
    }

    public function supplier()
    {
        $inventoryStock  = $this->inventoryStock;
        $product = $inventoryStock->product;

        return $product->supplier;
    }
}
