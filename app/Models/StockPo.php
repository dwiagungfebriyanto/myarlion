<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stock_po';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplier_id',
        'note',
    ];

    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }

    public function supplier()
    {
        return $this->belongsToMany(Supplier::class);
    }
}
