<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryIn extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_stock_id',
        'destination_stock_id',
        'product_id',
        'product_sku',
        'status',
        'datetime',
        'unit_id',
        'amount',
        'weight',
        'warehouse_id',
        'notes',
        'purchase_cost',
        'purchase_cost_per_unit',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('inventory_in')
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

    protected function notes(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?: '-',
        );
    }

    protected function purchaseCost(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => currencyFormat($value, 'Rp '),
        );
    }

    protected function purchaseCostPerUnit(): Attribute
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
    public function inventoryLosts()
    {
        return $this->hasMany(InventoryLost::class, 'inventory_id', 'id')->where('inventory_type', 'in');
    }

    public function inventoryOuts()
    {
        return $this->hasMany(InventoryOut::class);
    }

    public function inventoryStock()
    {
        return $this->belongsTo(InventoryStock::class, 'destination_stock_id', 'id');
    }

    public function poStock()
    {
        return $this->belongsTo(PoStock::class, 'po_stock_id', 'id');
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
    public function inventoryFormat()
    {
        return "[$this->datetime] "
                .$this->product->skuFormat()
                ." | Stock: " .$this->getStock() ;
    }

    public function inventoryLostsSum()
    {
        $unit = ($this->unit_id === 1) // unit = Kg
            ? 'weight'
            : 'amount' ;

        return $this->inventoryLosts()->sum($unit);
    }

    public function inventoryOutsSum()
    {
        $unit = ($this->unit_id === 1) // unit = Kg
            ? 'weight'
            : 'amount' ;

        return $this->inventoryOuts()->sum($unit);
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
