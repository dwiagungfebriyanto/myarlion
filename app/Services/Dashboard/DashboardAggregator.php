<?php

namespace App\Services\Dashboard;

use App\Models\Job;
use App\Models\Inquiry;
use App\Models\Product_Type;
use App\Models\Country;
use App\Models\User;
use App\Models\SalesTarget;
use App\Models\SalesTargetMonthly;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardAggregator
{
    public function __construct(private NetCostCalculator $netCostCalculator)
    {
    }

    public const SUPPORTED_MODULES = [
        'profit',
        'cost_category',
        'inquiry_by_product',
        'inquiry_by_channel',
        'junk_inquiry',
        'customer_report',
        'target_achievement',
        'target_achievement_monthly',
        'country_origin',
        'shipment_destination',
        'profit_by_country',
    ];

    public function aggregate(int $year, string $month, string $marketing = 'all', ?int $websiteId = null, array $modules = self::SUPPORTED_MODULES): array
    {
        $data = [];

        foreach ($modules as $module) {
            switch ($module) {
                case 'profit':
                    $data['profit'] = $this->profitSummary($year);
                    break;
                case 'cost_category':
                    $data['cost_category'] = $this->costCategory($month);
                    break;
                case 'inquiry_by_product':
                    $data['inquiry_by_product'] = $this->inquiryByProduct($month, $marketing);
                    break;
                case 'inquiry_by_channel':
                    $data['inquiry_by_channel'] = $this->inquiryByChannel($month);
                    break;
                case 'junk_inquiry':
                    $data['junk_inquiry'] = $this->junkInquiry($month, $websiteId);
                    break;
                case 'customer_report':
                    $data['customer_report'] = $this->customerReport($year);
                    break;
                case 'target_achievement':
                    $data['target_achievement'] = $this->targetAchievement($year);
                    break;
                case 'target_achievement_monthly':
                    $data['target_achievement_monthly'] = $this->targetAchievementMonthly($month);
                    break;
                case 'country_origin':
                    $data['country_origin'] = $this->countryOrigin($month);
                    break;
                case 'shipment_destination':
                    $data['shipment_destination'] = $this->shipmentDestination($month);
                    break;
                case 'profit_by_country':
                    $data['profit_by_country'] = $this->profitByCountry($month);
                    break;
            }
        }

        return $data;
    }

    public function profitSummary(int $year): array
    {
        $cost = $this->netCostCalculator->monthlyByYear($year);
        $createdAtMonthExpression = $this->netCostCalculator->monthExpression('created_at');
        $periodJobMonthExpression = $this->netCostCalculator->monthExpression('period_job');

        $est = Job::selectRaw("$createdAtMonthExpression as month, SUM(est_profit) as total")
            ->whereYear('created_at', $year)
            ->groupByRaw($createdAtMonthExpression)
            ->pluck('total', 'month');

        $actual = Job::selectRaw("$periodJobMonthExpression as month, SUM(net_profit) as total")
            ->where('status', 'closed')
            ->whereYear('period_job', $year)
            ->groupByRaw($periodJobMonthExpression)
            ->pluck('total', 'month');

        $rows = [];
        $currentMonth = (int) date('n');
        $end = $year == (int)date('Y') ? $currentMonth : 12;

        for ($m = 1; $m <= $end; $m++) {
            $c = (float) $cost->get($m, 0);
            $e = (float) ($est[$m] ?? 0);
            $a = (float) ($actual[$m] ?? 0);

            $rows[] = [
                'month' => $m,
                'cost' => $c,
                'est_profit' => $e,
                'est_net_profit' => $e - $c,
                'actual_profit' => $a,
                'net_actual_profit' => $a - $c,
            ];
        }

        return $rows;
    }

    public function costCategory(string $month): array
    {
        return $this->netCostCalculator->costCategoryByGroup($month)
            ->map(fn (float $amount, string $group) => [
                'group' => $group,
                'amount' => $amount,
            ])
            ->values()
            ->all();
    }

    public function inquiryByProduct(string $month, string $marketing = 'all'): array
    {
        $productTypes = Product_Type::with(['inquiryProducts' => function ($query) use ($month, $marketing) {
            $query->join('inquiries', 'inquiries.id', '=', 'inquiries_has_products.inquiry_id')
                ->where('inquiries.date', 'like', "$month%");

            if ($marketing !== 'all') {
                $query->where('inquiries.user_id', $marketing);
            }
        }, 'mainCategory'])
            ->whereHas('inquiryProducts')
            ->orderBy('id', 'desc')
            ->get();

        return $productTypes->map(function ($pt) {
            return [
                'product_type_id' => $pt->id,
                'product_type' => $pt->product_type_name,
                'category' => optional($pt->mainCategory)->main_category_name,
                'inquiry_count' => $pt->inquiryProducts->groupBy('inquiry_id')->count(),
            ];
        })->all();
    }

    public function inquiryByChannel(?string $month): array
    {
        $query = Inquiry::select('channel_id', DB::raw('COUNT(*) as total_inquiry'));
        
        if ($month) {
            $query->where('date', 'like', "$month%");
        }
        
        return $query->groupBy('channel_id')
            ->with('channel:id,channel_name')
            ->get()
            ->map(fn($r) => [
                'channel_id' => $r->channel_id,
                'channel' => optional($r->channel)->channel_name,
                'total' => (int) $r->total_inquiry,
            ])->all();
    }

    public function junkInquiry(string $month, ?int $websiteId = null): array
    {
        $query = Inquiry::selectRaw('website_id, COUNT(*) as total_junk')
            ->whereHas('website')
            ->where('date', 'like', "$month%")
            ->where('status', 'junk')
            ->groupBy('website_id')
            ->with('website:id,web_domain');

        if ($websiteId) {
            $query->where('website_id', $websiteId);
        }

        return $query->get()->map(fn($r) => [
            'website_id' => $r->website_id,
            'website' => $r->website->web_domain,
            'junk_total' => (int) $r->total_junk,
        ])->all();
    }

    public function customerReport(int $year): array
    {
        return Job::select('customer_id', DB::raw('SUM(est_profit) as total_profit'))
            ->whereYear('period_job', $year)
            ->groupBy('customer_id')
            ->orderBy('total_profit', 'desc')
            ->limit(10)
            ->with('customer:id,name')
            ->get()
            ->map(fn($r) => [
                'customer_id' => $r->customer_id,
                'customer_name' => optional($r->customer)->name,
                'total_profit' => (int) $r->total_profit,
            ])->all();
    }

    public function targetAchievement(int $year): array
    {
        $marketings = User::activeMarketing();
        foreach ($marketings as $m) {
            SalesTarget::updateMarketingSalestarget($m, $year);
        }

        return SalesTarget::whereIn('user_id', $marketings->pluck('id'))
            ->where('year', $year)
            ->with('user:id,name')
            ->get()
            ->map(fn($t) => [
                'marketing_id' => $t->user_id,
                'marketing' => optional($t->user)->name,
                'year' => (int) $t->year,
                'percentage' => $t->percentage ?? null,
                'estimate_profit' => (int) ($t->estimate_profit ?? 0),
            ])->all();
    }

    public function targetAchievementMonthly(string $month): array
    {
        $monthKey = substr($month, 0, 7);
        $marketings = User::activeMarketing();
        foreach ($marketings as $m) {
            SalesTargetMonthly::updateMarketingSalestarget($m, $monthKey);
        }

        return SalesTargetMonthly::whereIn('user_id', $marketings->pluck('id'))
            ->where('month', 'like', "$monthKey%")
            ->with('user:id,name')
            ->get()
            ->map(fn($t) => [
                'marketing_id' => $t->user_id,
                'marketing' => optional($t->user)->name,
                'month' => $t->month,
                'percentage' => $t->percentage ?? null,
                'estimate_profit' => (int) ($t->estimate_profit ?? 0),
            ])->all();
    }

    public function countryOrigin(string $month): array
    {
        $countries = Country::with(['inquiries' => function ($q) use ($month) {
            $q->where('date', 'like', "$month%");
        }])->whereHas('inquiries')->get();

        return $countries->map(fn($c) => [
            'country' => $c->country_name,
            'inquiry_count' => $c->inquiries->count(),
        ])->all();
    }

    public function shipmentDestination(string $month): array
    {
        $countries = Country::with(['inquiries_destination' => function ($q) use ($month) {
            $q->where('date', 'like', "$month%");
        }])->whereHas('inquiries_destination')->get();

        return $countries->map(fn($c) => [
            'country' => $c->country_name,
            'inquiry_count' => $c->inquiries_destination->count(),
        ])->all();
    }

    public function profitByCountry(string $month): array
    {
        return Job::selectRaw('country_id, SUM(est_profit) as total_est_profit')
            ->where('period_job', 'like', "$month%")
            ->whereHas('country')
            ->groupBy('country_id')
            ->with('country:id,country_name')
            ->get()
            ->map(fn($r) => [
                'country_id' => $r->country_id,
                'country' => optional($r->country)->country_name,
                'est_profit' => (int) $r->total_est_profit,
            ])->all();
    }
}
