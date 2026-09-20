<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'currency_code',
        'currency_name',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'currency_code', 'currency_code');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
