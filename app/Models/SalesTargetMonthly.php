<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SalesTargetMonthly extends Model
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
        'month', 
        'percentage', 
        'estimate_profit', 
        'commission'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('sales_target')
            ->logFillable();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function updateMarketingSalestarget(User $marketing, $yearMonth)
    {
        $yearMonth = Str::limit($yearMonth, 7, '');

        $estProfit = $marketing->jobs()
            ->where('jobs.period_job', 'like', "$yearMonth%")
            ->sum('est_profit');

        // First, get the existing target or use 0 as default
        $existingRecord = static::where([
            'user_id' => $marketing->id,
            'month'   => "$yearMonth-01"
        ])->first();

        $target = $existingRecord ? $existingRecord->target : 0;
        
        $percentage = !empty($target)
            ? ($estProfit / $target) * 100
            : 0;

        return static::updateOrCreate(
            [
                'user_id' => $marketing->id,
                'month'   => "$yearMonth-01"
            ],
            [
                'estimate_profit' => $estProfit,
                'percentage'      => $percentage,
            ]
        );
    }
}
