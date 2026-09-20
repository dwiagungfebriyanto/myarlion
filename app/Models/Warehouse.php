<?php

namespace App\Models;

use App\Models\Scopes\WarehouseScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Warehouse extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_name',
        'address',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new WarehouseScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('warehouse')
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
    public function inventoryIns()
    {
        return $this->hasMany(InventoryIn::class);
    }

    public function inventoryLosts()
    {
        return $this->hasMany(InventoryLost::class);
    }

    public function inventoryOuts()
    {
        return $this->hasMany(InventoryOut::class);
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public static function getActiveWarehouse($except=null)
    {
        $warehouse = static::where('status', 'Active');

        if ($except !== null) {
            $warehouse->whereNotIn('id', $except);
        }

        return $warehouse->orderBy('warehouse_name')->get();
    }
}
