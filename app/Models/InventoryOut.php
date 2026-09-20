<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryOut extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_in_id',
        'original_stock_id',
        'destination_stock_id',
        'product_id',
        'product_sku',
        'status',
        'datetime',
        'unit_id',
        'amount',
        'weight',
        'warehouse_id',
        'original_warehouse_id',
        'notes',
        'purchase_cost',
        'purchase_cost_per_unit',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('inventory_out')
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

    protected function salesCost(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => currencyFormat($value, 'Rp '),
        );
    }

    protected function salesCostPerUnit(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => currencyFormat($value, 'Rp '),
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
    public function inventoryIn()
    {
        return $this->belongsTo(InventoryIn::class);
    }

    public function inventoryLosts()
    {
        return $this->hasMany(InventoryLost::class, 'inventory_id', 'id')->where('inventory_type', 'out');
    }

    public function inventoryStock()
    {
        return $this->belongsTo(InventoryStock::class, 'destination_stock_id', 'id');
    }

    public function originalStock()
    {
        return $this->belongsTo(InventoryStock::class, 'original_stock_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }


    // ===============
    // CUSTOM FUNCTION
    // ===============
    public function originalWarehouse()
    {
        return Warehouse::find($this->original_warehouse_id);
    }

    public function getPoStock()
    {
        return $this->originalStock?->getPoStock();
    }

    public function getDisplaySupplier()
    {
        return $this->getPoStock()?->supplier ?? $this->product?->supplier;
    }

    public function unitValue()
    {
        $unitValue = ($this->unit_id === 1) // unit = Kg
            ? $this->weight
            : $this->amount ;

        return $unitValue;
    }

    public function quantityValue()
    {
        $unitValue = ($this->unit_id === 1) // unit = Kg
            ? $this->weight
            : $this->amount ;

        return "$unitValue " .$this->unit->unit_name;
    }
}
