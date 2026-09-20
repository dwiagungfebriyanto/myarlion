<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Inquiry;
use App\Models\Job;
use App\Models\PoStock;
use App\Models\Product_Type;
use App\Models\SalesTarget;
use App\Models\SalesTargetMonthly;
use App\Models\User;
use App\Models\Website;
use App\Services\Dashboard\NetCostCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private $currentYear;
    private $period;

    public function __construct()
    {
        $this->currentYear = date('Y');
        $this->period      = request('month') ?? date('Y-m');
    }

    public function index(NetCostCalculator $netCostCalculator)
    {
        $costYear = $netCostCalculator->monthlyByYear((int) $this->currentYear);
        $createdAtMonthExpression = $netCostCalculator->monthExpression('created_at');
        $periodJobMonthExpression = $netCostCalculator->monthExpression('period_job');

        $estProfit = Job::selectRaw("$createdAtMonthExpression as month, SUM(est_profit) as total_est_profit")
            ->whereYear('created_at', $this->currentYear)
            ->groupByRaw($createdAtMonthExpression)
            ->pluck('total_est_profit', 'month');

        $actualProfit = Job::selectRaw("$periodJobMonthExpression as month, SUM(gross_profit) as total_actual_profit")
            ->where('status', 'closed')
            ->whereYear('period_job', $this->currentYear)
            ->groupByRaw($periodJobMonthExpression)
            ->pluck('total_actual_profit', 'month');

        $currentMonth = (int) date('m');

        $collectData = [];

        for ($i = 1; $i <= $currentMonth; $i++) {
            $estValue = (float) $estProfit->get($i, 0);
            $costValue = (float) $costYear->get($i, 0);
            $actualValue = (float) $actualProfit->get($i, 0);

            $collectData[$i] = [
                "cost"              => $costValue,

                "est_profit"        => $estValue,
                "est_net_profit"    => $estValue - $costValue,

                "actual_profit"     => $actualValue,
                "net_actual_profit" => $actualValue - $costValue,
            ];
        }


        $marketing_list = User::activeMarketing();
        $websites       = Website::all();

        $data = [
            // untuk EST NET PROFIT dan ACTUAL PROFIT
            'profit_charts'  => $collectData,
            'marketing_list' => $marketing_list,   // untuk filter INQUIRY BY PRODUCT
            'websites'       => $websites,         // untuk filter JUNK INQUIRY1

            // 'supplierReport' => $this->getSupplierReport(),
            // 'outstandings'   => $this->getOutstanding(),
        ];

        return view('pages.dashboard.index', $data);
    }

    public function getCostCategory(NetCostCalculator $netCostCalculator)
    {
        return response()->json($netCostCalculator->costCategoryChartData($this->period));
    }

    public function getCountryOrigin()
    {
        $month = $this->period;

        $origin_country = Country::with(['inquiries' => function ($query) use ($month) {
            $query->where('date', 'like', "$month%");
        }])->whereHas('inquiries')->get();

        $data = [];

        foreach ($origin_country as $origin) {
            $data[] = [
                $origin->country_name,
                $origin->inquiries->count(),
            ];
        }

        return response()->json($data);
    }

    public function getCustomerReport(Request $request)
    {
        $year = request('year') ?? $this->currentYear;

        $jobs = Job::select(
                'customer_id',
                DB::raw('SUM(est_profit) as total_profit')
            )
            ->whereYear('period_job', $year)
            ->groupBy('customer_id')
            ->orderBy('total_profit', 'desc')
            ->limit(10)
            ->with('customer')
            ->get();

        return response()->json(APIresponse(true, "Customer Report retrieved.", $jobs));
    }

    public function getInquiryByChannel(Request $request) : JsonResponse
    {
        $month = request('month') ?? date('Y-m');

        $inquiries = Inquiry::select(
                'channel_id',
                DB::raw('COUNT(*) as total_inquiry')
            )
            ->where('date', 'like', "$month%")
            ->groupBy('channel_id')
            ->with('channel')
            ->get();

        return response()->json(APIresponse(true, "Inquiry By Channel retrieved.", $inquiries));
    }

    public function getInquiryByProduct()
    {
        $marketing = request('marketing') ?? 'all';
        $month = $this->period;

        $product_inq = Product_Type::with(['inquiryProducts' => function ($query) use ($marketing, $month) {
            $query->join('inquiries', 'inquiries.id', '=', 'inquiries_has_products.inquiry_id')
                ->where('inquiries.date', 'like', "$month%");

                if ($marketing !== 'all') {
                    $query->where('inquiries.user_id', $marketing);
                }
        }, 'mainCategory'])
        ->whereHas('inquiryProducts')
        ->orderBy('id', 'desc')
        ->get();

        $data = [];

        foreach ($product_inq as $inquiry) {
            $data[] = [
                $inquiry->product_type_name,
                $inquiry->inquiryProducts->groupBy('inquiry_id')->count(),
                '<h6>Category :</h6>' . $inquiry->mainCategory->main_category_name
                    . "<br> <h6>Product :</h6> $inquiry->product_type_name"
                    . "<br> <h6>Inquiry :</h6> " . $inquiry->inquiryProducts->groupBy('inquiry_id')->count()
            ];
        }

        return response()->json($data);
    }

    public function getJunkInquiry() {
        $websiteID = request('website');

        $junkInquiries = Inquiry::selectRaw('website_id, COUNT(*) as total_junk')
                                ->whereHas('website')
                                ->where('date', 'like', "$this->period%")
                                ->where('status', 'junk')
                                ->groupBy('website_id')
                                ->with('website:id,web_domain');

        if ($websiteID) {
            $junkInquiries->where('website_id', $websiteID);
        }

        $data = [];
        foreach ($junkInquiries->get() as $value) {
            $data[] = [
                $value->website->web_domain,
                $value->total_junk,
            ];
        }

        return response()->json($data);
    }

    public function getProfitCountry() {
        $profitByCountry = Job::selectRaw('MONTH(period_job) as month, SUM(est_profit) as total_est_profit, country_id')
            ->where('period_job', 'like', "$this->period%")
            ->groupBy('month')
            ->groupBy('country_id')
            ->whereHas('country')
            ->get();

        $data = [];
        foreach ($profitByCountry as $estProfit) {
            $data[] = [
                $estProfit->country->country_name,
                $estProfit->total_est_profit
            ];
        }

        return response()->json($data);
    }

    public function getSalesInquiry()
    {
        $month = $this->period;

        $marketing_inqSales = User::with([
            'inquiries' => function ($query) use ($month)
            {
                $query->where('date', 'like', $month . '%');
                $query->orderBy('updated_at', 'desc');
            },
            'jobs' => function ($query) use ($month)
            {
                $query->where('period_job', 'like', $month . '%');
            },
        ])->where('role_id', 3)
        ->where('is_former_employee', false)
        ->get();

        return response()->json($marketing_inqSales);
    }

    public function getShipmentDestination() {
        $month = $this->period;

        $inq_destination = Country::with(['inquiries_destination' => function ($query) use ($month) {
            $query->where('date', 'like',  "$month%");
        }])->whereHas('inquiries_destination')->get();

        $data = [];

        foreach ($inq_destination as $destination) {
            $data[] = [
                $destination->country_name,
                $destination->inquiries_destination->count(),
            ];
        }

        return response()->json($data);
    }

    private function getSupplierReport()
    {
        $poStocks = PoStock::select(
                'supplier_id', 
                DB::raw('SUM(total) as total_amount')
            )
            ->where('unique_id', 'like', "%" .substr($this->currentYear, -2))
            ->groupBy('supplier_id')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->with('supplier')
            ->get();

        return $poStocks;
    }

    public function getTargetAchievement() {
        $year = request('year') ?? $this->currentYear;
        
        // Validasi tahun: hanya boleh 2022 sampai tahun saat ini
        if ($year < 2022 || $year > $this->currentYear) {
            return response()->json([
                'error' => 'Year must be between 2022 and ' . $this->currentYear
            ], 400);
        }
        
        $activeMarketings = User::activeMarketing();

        foreach ($activeMarketings as $marketing) {
            SalesTarget::updateMarketingSalestarget($marketing, $year);
        }

        $activeMarketingId = $activeMarketings->pluck('id')->all();

        $targetAchievement = SalesTarget::whereIn('user_id', $activeMarketingId)
            ->where('year', $year)
            ->with('user')
            ->get();

        return response()->json($targetAchievement);
    }

    public function getTargetAchievementMonthly() : JsonResponse
    {
        $month = request('month') 
            ? Str::limit(request('month'), 7, '') 
            : date('Y-m');
        
        // Validasi tahun dari bulan: hanya boleh 2022-01 sampai bulan saat ini
        $yearFromMonth = (int) substr($month, 0, 4);
        if ($yearFromMonth < 2022 || $yearFromMonth > $this->currentYear) {
            return response()->json([
                'error' => 'Year must be between 2022 and ' . $this->currentYear
            ], 400);
        }
        
        // Validasi bulan tidak boleh melebihi bulan saat ini
        if ($month > date('Y-m')) {
            return response()->json([
                'error' => 'Month cannot be in the future'
            ], 400);
        }

        $activeMarketings = User::activeMarketing();

        foreach ($activeMarketings as $marketing) {
            SalesTargetMonthly::updateMarketingSalestarget($marketing, $month);
        }

        $activeMarketingId = $activeMarketings->pluck('id')->toArray();
        
        $targetAchievement = SalesTargetMonthly::whereIn('user_id', $activeMarketingId)
            ->where('month', 'like', "$month%")
            ->with('user')
            ->get();

        return response()->json($targetAchievement);
    }
}
