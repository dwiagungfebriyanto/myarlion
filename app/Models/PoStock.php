<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PoStock extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'main_category_id',
        'supplier_id',
        'shipping_cost',
        'note',
        'additional_expenses',
        'total',
        'status',
        'arrived_warehouse',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('po_stock')
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

    // ============
    // RELATIONSHIP
    // ============
    public function inventoryIns()
    {
        return $this->hasMany(InventoryIn::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_po_stocks');
    }

    public function mainCategory()
    {
        return $this->belongsTo(Main_Category::class, 'main_category_id', 'id');
    }

    public function outcomeCheques()
    {
        return $this->hasMany(OutcomeCheque::class);
    }

    public function poStockDetail()
    {
        return $this->hasMany(PoStockProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public static function incomplete()
    {

        return self::where('status', 'incomplete');
    }

    public function iscomplete()
    {
        return ($this->status === 'complete');
    }

    public static function onDelivery()
    {

        return self::where('arrived_warehouse', 0)->get();
    }

    public function poStockFormat()
    {
        $mainCategory = $this->mainCategory ? '| ' . $this->mainCategory->main_category_name : '';
        $supplier = $this->supplier ? '| ' . $this->supplier->supplier_name : '';

        return "[PO " .ucfirst($this->po_type) ."] $this->unique_id $mainCategory $supplier";
    }
}
