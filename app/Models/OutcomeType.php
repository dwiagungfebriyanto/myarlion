<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'outcome_group_id',
        'name',
    ];

    public function otherIncomes()
    {
        return $this->hasMany(OtherIncome::class);
    }

    public function outcomeCheques()
    {
        return $this->hasMany(OutcomeCheque::class);
    }

    public function outcomeGroup()
    {
        return $this->belongsTo(OutcomeGroup::class);
    }
}
