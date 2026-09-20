<?php

namespace App\Models;

use App\Models\Scopes\OfferedSampleScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FreeSample extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'inventory_stock_id',
        'customer_id',
        'inquiry_id',
        'quantity',
        'purchase_cost',
        'purchase_cost_per_unit',
        'date',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new OfferedSampleScope);
    }

    /**
     * Get the activity log options.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('cost')
            ->logFillable();
    }

    #### ACCESSORS ####
    
    /**
     * Get the OfferedSample date.
     */
    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => date_format(date_create($value), 'Y F d'),
        );
    }
    
    /**
     * Get the OfferedSample date.
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->diffForHumans(),
        );
    }
    
    /**
     * Get the OfferedSample date.
     */
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->diffForHumans(),
        );
    }

    #### RELATIONS ####

    /**
     * The customer that received this sample.
     */
    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * The inventory stock that is associated with this offered sample.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventoryStock()
    {
        return $this->belongsTo(InventoryStock::class, 'inventory_stock_id', 'id');
    }

    public function inventorySample()
    {
        return $this->inventoryStock()->where('stock_bucket', 'sample');
    }

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function recipientName() : string
    {
        return $this->Customer?->name
            ?? $this->inquiry?->name
            ?? 'Prospect';
    }

    public function product() : Product
    {
        return $this->inventoryStock->product;
    }

    public function unit() : Unit
    {
        return $this->inventoryStock->unit;
    }

    public function unitFormatted() : string
    {
        return number_format($this->quantity, 1) .' ' .$this->unit()->unit_name;
    }

    public function warehouse() : Warehouse
    {
        return $this->inventoryStock->warehouse;
        
    }
}
