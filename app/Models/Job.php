<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Job extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'job_id',
        'source',
        'customer_id',
        'currency_id',
        'country_id',
        'employee_id',
        'channel_id',
        'amount',
        'kurs_amount',
        'sisa',
        'refund',
        'est_profit',
        'total_expenses',
        'gross_profit',
        'net_profit',
        'period_job',
        'date',
        'status_payment',
        'status',
        'closing_date',
        'status_konversi',
        'sales_commission',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('job')
            ->logFillable();
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'integer',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function commissions()
    {
        return $this->hasMany(JobCommission::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function jobIncomes()
    {
        return $this->hasMany(JobIncome::class)->orderBy('date');
    }

    public function marketing()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function costs()
    {
        return $this->hasMany(OutcomeCheque::class, 'code', 'id')->where('code_type', 'job');
    }

    public function otherIncomes()
    {
        return $this->hasMany(OtherIncome::class, 'code', 'id')->where('code_type', 'job');
    }

    public function poStocks()
    {
        return $this->belongsToMany(PoStock::class, 'job_po_stocks');
    }

    public function editAmountRequests()
    {
        return $this->hasMany(JobEditAmountApproval::class)->latest();
    }

    public function waitingEditAmountRequest()
    {
        return $this->hasOne(JobEditAmountApproval::class)
            ->where('status', 'waiting');
    }

    public function samples()
    {
        return $this->hasMany(JobStatement::class)
            ->where('is_sample', true)
            ->with('inventoryStock.poStock');
    }

    public function stocks()
    {
        return $this->hasMany(JobStatement::class)->where('is_sample', false);
    }

    public function stockPos()
    {
        return $this->belongsToMany(StockPo::class);
    }

    public function inquiry()
    {
        return $this->hasOne(Inquiry::class);
    }

    public function jobProducts()
    {
        return $this->belongsToMany(Product::class, 'jobs_has_products')
            ->withPivot(['id', 'quantity', 'price', 'note'])
            ->withTimestamps();
    }

    public function jobHasInventoryStock()
    {
        // Backward-compatible alias to the new product-based relation.
        return $this->jobProducts();
    }

    public function jobStatement()
    {
        return $this->hasMany(JobStatement::class);
    }

    public function teams()
    {
        return $this->hasMany(JobTeam::class);
    }

    // ===============
    // CUSTOM FUNCTION
    // ===============
    public function currentOutstanding()
    {
        return $this->amount - $this->jobIncomes->sum('payment');
    }

    public function groupedExpenses()
    {
        $costs = $this->costs()
            ->where('outcome_type_id', '!=', 208001) // pengecualian untuk sample, karena sudah ada section tersendiri
            ->orderBy('date', 'desc')
            ->get()
            ->each->setAttribute('source', 'cost');

        $otherIncomes = $this->otherIncomes()
            ->where('outcome_type_id', '!=', 208001) // pengecualian untuk sample, karena sudah ada section tersendiri
            ->orderBy('date', 'desc')
            ->get()
            ->each->setAttribute('source', 'other_income');

        // combine cost with other incomes
        $expenses = $costs->concat($otherIncomes);

        $data = [];

        foreach ($expenses as $item) {
            $groupID = $item->outcomeType->outcome_group_id;

            if (!array_key_exists($groupID, $data)) {
                $data[$groupID] = [];
            }

            if ($item->source === 'other_income') {
                $item->amount = -abs((float) $item->amount);
            }
            
            $data[$groupID][] = $item;
        }

        return $data;
    }

    public function jobDesc()
    {
        $customerCode = $this->customer?->code ?? '-';
        $customerName = $this->customer?->name ?? 'Unknown Customer';
        $marketingName = $this->marketing?->name ?? 'Unknown Marketing';

        return "$this->code / $customerCode - $customerName ($marketingName)";
    }

    public function statusIsOpen()
    {
        return ($this->status === 'open');
    }

    public function statusPaymentIsOpen()
    {
        return ($this->status_payment === 'open');
    }

    public static function teamDetail($jobId)
    {
        $jobteam = self::find($jobId)->teams;

        $teams = [];

        foreach ($jobteam as $team) {
            $teams[] = $team->marketing;
        }

        return $teams;
    }

    public function totalIncome()
    {
        return $this->jobIncomes()->sum('nominal');
    }
}
