<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'currency_code');
    }

    public function jobIncomes()
    {
        return $this->hasMany(JobIncome::class);
    }

    public function outcomeCheques()
    {
        return $this->hasMany(OutcomeCheque::class);
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public function bankAccountLabel()
    {
        return '[ ' .$this->bank->bank_name ." - $this->currency_code ] "
                ."$this->account_number - $this->account_name";
    }
}
