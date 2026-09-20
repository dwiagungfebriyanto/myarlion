<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryMutation extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_product_id',
        'original_stock_id',
        'new_product_id',
        'destination_stock_id',
        'qty',
        'unit_id',
        'price',
        'source_purchase_cost',
        'source_purchase_cost_per_unit',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('inventory_mutation')
            ->logFillable();
    }

    // =========
    // ACCESSORS
    // =========
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('d-m-Y H:i:s', strtotime($value)),
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('d-m-Y H:i:s', strtotime($value)),
        );
    }

    // =============
    // RELATIONSHIPS
    // =============
    public function product()
    {
        return $this->belongsTo(Product::class, 'new_product_id', 'id');
    }

    public function inventoryStock()
    {
        return $this->belongsTo(InventoryStock::class, 'destination_stock_id', 'id');
    }

    public function originalSku()
    {
        return $this->belongsTo(Product::class, 'original_product_id', 'id');
    }

    public function originalStock()
    {
        // relasi ke mutations()
        return $this->belongsTo(InventoryStock::class, 'original_stock_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public function quantityValue()
    {
        return "$this->qty " .$this->unit->unit_name;
    }
    public function quantityMutationValue()
    {
        return "$this->qty_mutation " .$this->originaUnit()->unit_name;
    }

    public function originaUnit()
    {
        return Unit::find($this->originalStock->unit_id);
    }
}
