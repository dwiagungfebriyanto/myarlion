<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryStock extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_id',
        'inventory_type',
        'product_id',
        'po_stock_id',
        'stock_bucket',
        'product_sku',
        'warehouse_id',
        'unit_id',
        'amount',
        'weight',
        'purchase_cost',
        'purchase_cost_per_unit',
        'history_overall_qty',
        'history_overall_avg',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('inventory_stock')
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
    public function inventoryIn()
    {
        return $this->belongsTo(InventoryIn::class, 'inventory_id', 'id');
    }

    public function inventoryInFromOut()
    {
        // Untuk sample yang berasal dari inventory out, ambil inventory_in dari inventory_out
        return $this->hasOneThrough(
            InventoryIn::class,
            InventoryOut::class,
            'id', // inventory_outs.id
            'id', // inventory_ins.id  
            'inventory_id', // inventory_stocks.inventory_id
            'inventory_in_id' // inventory_outs.inventory_in_id
        );
    }

    public function inventoryLosts()
    {
        return $this->hasMany(InventoryLost::class);
    }

    public function inventoryMutations()
    {
        // relasi ke inventoryStock()
        return $this->belongsTo(InventoryMutation::class, 'inventory_id', 'id');
    }

    public function inventoryOut()
    {
        return $this->belongsTo(InventoryOut::class, 'inventory_id', 'id');
    }

    public function inventoryOuts()
    {
        return $this->hasMany(InventoryOut::class, 'id', 'inventory_id');
    }

    public function mutations()
    {
        // relasi ke originalStock()
        return $this->hasMany(InventoryMutation::class, 'original_stock_id', 'id')->where('inventory_type', 'mutation');
    }

    public function offeredSamples()
    {
    // Relation disabled: OfferedSample model not found in codebase currently.
    // return $this->hasMany(OfferedSample::class);
    return $this->hasMany(\App\Models\InventoryStock::class)->whereRaw('1=0'); // safe no-op
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function poStock()
    {
        return $this->belongsTo(PoStock::class, 'po_stock_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function job(){
        return $this->belongsToMany(Job::class, 'jobs_has_products');
    }

    public function jobStatement()
    {
        return $this->hasMany(JobStatement::class);
    }


    // ===============
    // CUSTOM FUNCTION
    // ===============
    public function getInventory($type, $inventoryId)
    {
        switch ($type) {
            case 'in':
                $inventory = $this->inventoryIn;
                break;

            case 'out':
                $inventory = $this->inventoryOuts()->where('id', $inventoryId)->first();
                break;

            default:
                $inventory = $this->inventoryMutations()->where('id', $inventoryId)->first();
                break;
        }

        return $inventory;
    }

    public static function getReadyStock(bool|string $withSample = true, $supplierId = null, $warehouseId = null, $mainCategoryId = null)
    {
        $query = static::whereHas('product')
            ->where(function ($query) {
                $query->where('amount', '>', 0)
                    ->orWhere('weight', '>', 0);
            });

        if ($withSample === 'false' || $withSample === false) {
            $query->where('stock_bucket', '!=', 'sample');
        }

        // only sample
        if ($withSample === 'only') {
            $query->where('stock_bucket', 'sample');
        }

        if (!is_null($supplierId) || !is_null($mainCategoryId)) {
            $query->join('products', 'inventory_stocks.product_id', '=', 'products.id');

            if (!is_null($supplierId)) {
                $query->where('products.supplier_id', $supplierId);
            }

            if (!is_null($mainCategoryId)) {
                $query->where('products.main_category_id', $mainCategoryId);
            }
        }

        if (!is_null($warehouseId)) {
            $query->where('inventory_stocks.warehouse_id', $warehouseId);
        }

        return $query->select('inventory_stocks.*')->get();
    }

    public function getStock()
    {
        $stock = ($this->unit_id === 1) // unit_id = Kg
            ? $this->weight
            : $this->amount ;

        return number_format($stock,1, ',', '.') . " {$this->unit->unit_name}";
    }

    public function inventoryFormat()
    {
        $label = '';
        
        $poStock = $this->getPoStock();
        if (!is_null($poStock)) {
            $poUniqueID = $poStock->unique_id ?? '- ';
            $poType     = $poStock->po_type ?? '';

            $label .= "[PO " .ucfirst($poType) .": $poUniqueID] ";
        }
        
        return $label . $this->product?->skuFormat()
        ." | Stock: " .$this->getStock()
        ." | Warehouse: " .$this->warehouse->warehouse_name ;
    }

    public function unitValue()
    {
        $unitValue = ($this->unit_id === 1) // unit = Kg
            ? $this->weight
            : $this->amount ;

        return $unitValue;
    }

    public function getStockBucket(): string
    {
        return $this->stock_bucket ?: ($this->inventory_type === 'sample' ? 'sample' : 'stock');
    }

    public function getPoStock()
    {
        if (!is_null($this->po_stock_id)) {
            return $this->poStock ?: PoStock::find($this->po_stock_id);
        }

        $visitedIds = [];
        $current = $this; // InventoryStock instance
        $safety = 0;
        while ($current && $safety < 15) { // safety guard
            $safety++;
            $visitedIds[] = $current->id;

            // Root condition
            if ($current->inventory_type === 'in' && $current->inventoryIn) {
                return $current->inventoryIn->poStock;
            }
            if ($current->inventory_type === 'sample' && $current->inventoryIn) {
                return $current->inventoryIn->poStock;
            }

            switch ($current->inventory_type) {
                case 'out':
                case 'sample':
                    $invOut = InventoryOut::find($current->inventory_id);
                    if (!$invOut) {
                        return null; 
                    }
                    $current = $invOut->originalStock; // bisa null
                    break;

                case 'mutation':
                    $mutation = InventoryMutation::find($current->inventory_id);
                    if (!$mutation) {
                        return null;
                    }
                    $current = $mutation->originalStock; // bisa null
                    break;

                case 'in': // sudah ditangani root condition; jika tidak punya inventoryIn -> data anomali
                default:
                    return null;
            }

            if ($current && in_array($current->id, $visitedIds, true)) {
                // loop terdeteksi
                return null;
            }
        }

        return null; // fallback
    }

    public function getRootInventoryInId(): ?int
    {
        $visitedIds = [];
        $current = $this;
        $safety = 0;

        while ($current && $safety < 15) {
            $safety++;
            $visitedIds[] = $current->id;

            if (in_array($current->inventory_type, ['in', 'sample'], true) && $current->inventoryIn) {
                return (int) $current->inventoryIn->id;
            }

            switch ($current->inventory_type) {
                case 'out':
                case 'sample':
                    $inventoryOut = InventoryOut::find($current->inventory_id);
                    $current = $inventoryOut?->originalStock;
                    break;

                case 'mutation':
                    $mutation = InventoryMutation::find($current->inventory_id);
                    $current = $mutation?->originalStock;
                    break;

                default:
                    return null;
            }

            if ($current && in_array($current->id, $visitedIds, true)) {
                return null;
            }
        }

        return null;
    }

    public function quantityValue()
    {
        $unitValue = ($this->unit_id === 1) // unit = Kg
            ? $this->weight
            : $this->amount ;

        return "$unitValue " .$this->unit->unit_name;
    }

    public static function samples() {
    return self::where('stock_bucket', 'sample');
    }

    public static function stocks() {
        return self::where('stock_bucket', '!=', 'sample');
    }
}
