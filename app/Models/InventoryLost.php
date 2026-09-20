<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryLost extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_stock_id',
        'inventory_id',
        'inventory_type',
        'product_id',
        'status',
        'datetime',
        'unit_id',
        'amount',
        'weight',
        'purchase_cost',
        'purchase_cost_per_unit',
        'warehouse_id',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('inventory_lost')
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

    protected function datetime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('d-m-Y H:i', strtotime($value)),
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
        return $this->belongsTo(InventoryIn::class, 'id', 'inventory_id')->where('inventory_type', 'in');
    }

    public function inventoryOut()
    {
        return $this->belongsTo(InventoryOut::class, 'id', 'inventory_id')->where('inventory_type', 'out');
    }

    public function inventoryStock()
    {
        return $this->belongsTo(InventoryStock::class);
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
