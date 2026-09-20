<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLogs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_id',
        'sku',
        'product',
        'brand',
        'supplier',
        'type',
        'warehouse_id',
        'warehouse_name',
        'warehouse_address',
        'qty',
        'price',
        'date',
        'username',
        'role',
        'action',
    ];
}
