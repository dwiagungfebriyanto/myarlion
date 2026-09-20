<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'main_category_id',
        'code',
        'supplier_name',
        'pkp',
        'no_rekening',
        'address',
        'telp',
        'fax',
        'email',
        'contact',
        'note',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('supplier')
            ->logFillable();
    }

    public function mainCategory()
    {
        return $this->belongsTo(Main_Category::class);
    }

    public function assetPos()
    {
        return $this->hasMany(AssetPo::class);
    }

    public function otherIncomes()
    {
        return $this->morphMany(OtherIncome::class, 'recipient');
    }

    public function poStocks()
    {
        return $this->hasMany(PoStock::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function stockPos()
    {
        return $this->hasMany(StockPo::class);
    }

    # Custom functions
    public function label($separator = '|')
    {
        $category = $this->mainCategory ? " ({$this->mainCategory->main_category_name})" : '';
        return "$category $this->code $separator $this->supplier_name";
    }
}
