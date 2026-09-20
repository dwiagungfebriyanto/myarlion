<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Vendor extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'code',
        'vendor_name',
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
            ->useLogName('vendor')
            ->logFillable();
    }

    public static function getNewCode() : int {
        $vendors = self::orderBy('code', 'asc')->get()->toArray();

        return generateCode($vendors, 1);
    }

    public function otherIncomes()
    {
        return $this->morphMany(OtherIncome::class, 'recipient');
    }

    # Custom functions
    public function label($separator = '|')
    {
        return "$this->code $separator $this->vendor_name";
    }
}
