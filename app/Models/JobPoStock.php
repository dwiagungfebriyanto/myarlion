<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JobPoStock extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id',
        'po_stock_id',
        'product_id',
        'qty',
        'amount',
        'note',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('job_po_stock')
            ->logFillable();
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function poStock()
    {
        return $this->belongsTo(PoStock::class);
    }
}
