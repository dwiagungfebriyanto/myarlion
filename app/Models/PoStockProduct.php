<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PoStockProduct extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'po_stock_product';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_stock_id',
        'product_id',
        'qty',
        'remaining_qty',
        'unit_id',
        'price',
        'purchase_cost',
    ];

    // ============
    // RELATIONSHIP
    // ============
    public function poStock()
    {
        return $this->belongsTo(PoStock::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public function quantityFormat()
    {
        return $this->qty . ' ' . $this->unit->unit_name;
    }

    public function quantityRemainingFormat()
    {
        return $this->remaining_qty . ' ' . $this->unit->unit_name;
    }
}
