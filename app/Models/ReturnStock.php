<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'inventory_stock_id',
        'qty',
        'total',
        'stock_cost',
        'stock_cost_per_unit',
        'other_income_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function inventoryStock()
    {
        return $this->belongsTo(InventoryStock::class, 'inventory_stock_id', 'id');
    }

    public function getSupplierAttribute()
    {
        if (!$this->inventoryStock) {
            return null;
        }

        return $this->inventoryStock->getPoStock()?->supplier;
    }
}
