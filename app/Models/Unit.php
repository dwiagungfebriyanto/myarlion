<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_name'
    ];

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
        return $this->hasMany(InventoryMutation::class);
    }

    public function inventoryOuts()
    {
        return $this->hasMany(InventoryOut::class);
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function poStockProduct()
    {
        return $this->hasMany(PoStockProduct::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
