<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SalesTarget extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'target',
        'year',
        'percentage',
        'estimate_profit',
        'commission',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('sales_target')
            ->logFillable();
    }

    /**
     * Get the sales target estimate profit.
     */
    protected function estimateProfit(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => !empty($value) ? $value : 0,
        );
    }

    /**
     * Get the sales target percentage.
     */
    protected function percentage(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => !empty($value) ? $value : 0,
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function updateMarketingSalestarget(User $marketing, $year)
    {
        $year = Str::limit($year, 4, '');
        
        $marketingSalesTarget = static::firstOrCreate([
            'user_id' => $marketing->id,
            'year'    => $year
        ]);

        $estProfit = $marketing->jobs()
            ->where('jobs.period_job', 'like', "$year%")
            ->sum('est_profit');

        $percentage = !empty($marketingSalesTarget->target)
            ? ($estProfit / $marketingSalesTarget->target) * 100
            : 0;

        $data = [
            'estimate_profit' => $estProfit,
            'percentage'      => $percentage,
        ];

        return $marketingSalesTarget->update($data);
    }

}
