<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OtherIncome extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'other_income_category_id', 
        'outcome_type_id',
        'code',
        'code_type', 
        'bank_account_id',
        'date', 
        'amount', 
        'description',
        'recipient_type',
        'recipient_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('other_income')
            ->logFillable()
            ->logOnlyDirty();
    }

    // =============
    // ACCESSORS
    // =============
    public function getRecipientTypeAliasAttribute(): ?string
    {
        if (!$this->recipient_type) return null;
        return strtolower(class_basename($this->recipient_type)); // "vendor"/"supplier"
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

    public function category()
    {
        return $this->belongsTo(OtherIncomeCategory::class, 'other_income_category_id', 'id');
    }

    public function outcomeType()
    {
        return $this->belongsTo(OutcomeType::class);
    }

    public function recipient()
    {
        return $this->morphTo();
    }
}
