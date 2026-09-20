<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OutcomeCheque extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_account_id',
        'code',
        'code_type',
        'outcome_type_id',
        'po_stock_id',
        'amount',
        'recipient_type',
        'recipient_id',
        'note',
        'date',
        'receipt',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('cost')
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

    protected function note(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?: '-',
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('d-m-Y H:i:s', strtotime($value)),
        );
    }

    // =============
    // SCOPES
    // =============
    public function scopeForPeriod($q, $period)
    {
        return $q->where('date', 'like', "$period%");
    }

    // =============
    // RELATIONSHIPS
    // =============
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'code', 'id');
    }

    public function outcomeType()
    {
        return $this->belongsTo(OutcomeType::class);
    }

    public function poAsset()
    {
        return $this->belongsTo(PoAsset::class, 'code', 'id');
    }

    public function poStock()
    {
        return $this->belongsTo(PoStock::class, 'code', 'id');
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public static function getByPeriode($period, $typeID)
    {
        return static::where('date', 'like', "$period%")
            ->whereIn('outcome_type_id', $typeID)
            ->orderBy('date')
            ->get();
    }

    public function recipientLabel()
    {
        $recipient = ($this->recipient_type == 'supplier')
            ? Supplier::find($this->recipient_id)
            : Vendor::find($this->recipient_id);

        $label = ($this->recipient_type == 'supplier')
            ? $recipient->mainCategory->main_category_name . " | $recipient->code | $recipient->supplier_name"
            : "$recipient->code | $recipient->vendor_name";

        return $label;
    }
}
