<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConvertJobIncomeRequest;
use App\Http\Requests\SaveJobCommissionRequest;
use App\Http\Requests\SaveJobStockRequest;
use App\Http\Requests\StoreJobPoStockRequest;
use App\Http\Requests\UpdateJobPoStockRequest;
use App\Http\Requests\UpdateJobStockRequest;
use App\Models\InventoryStock;
use App\Models\Job;
use App\Models\JobCommission;
use App\Models\JobIncome;
use App\Models\JobPoStock;
use App\Models\JobStatement;
use App\Models\OutcomeCheque;
use App\Models\PoStockProduct;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryStockBalanceService;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobStatementController extends Controller
{
    public function index(Job $job)
    {
        $totalExpenses = 0;
        $expensesTotalByCategory = [];

        foreach ($job->groupedExpenses() as $key => $expenses) {
            $sumExpenses = array_sum(array_column($expenses, 'amount'));
            $totalExpenses += $sumExpenses;

            $expensesTotalByCategory[$key] = $sumExpenses;
        }

        $totalSample     = $job->samples()->sum('total');
        $totalJobPoStock = JobPoStock::where('job_id', $job->id)->sum('amount');
        $totalStock      = $job->stocks()->sum('total');
        $overallExpenses = $totalExpenses + $totalJobPoStock + $totalStock + $totalSample;

        $grossProfit     = $job->totalIncome() - $overallExpenses;
        $totalCommission = $job->commissions->sum('nominal');
        $netProfit       = $grossProfit - $totalCommission;

        $PoStockController = new PurchaseOrderController;

        $data['job']                     = $job;
        $data['expensesTotalByCategory'] = $expensesTotalByCategory;
        $data['totalExpenses']           = $totalExpenses;
        $data['totalJobPoStock']         = $totalJobPoStock;
        $data['overallExpenses']         = $overallExpenses;

        $data['grossProfit']     = $grossProfit;
        $data['totalCommission'] = $totalCommission;
        $data['netProfit']       = $netProfit;

        $data['samples']        = $job->samples;
        $data['stocks']         = $job->stocks;
        $data['jobPoStocks']    = JobPoStock::where('job_id', $job->id)->get();
        $data['suppliers']      = Supplier::all();
        $data['warehouses']     = Warehouse::all();
        $data['poStockOptions'] = $PoStockController->getPoOptions();

        $data['commissions']     = $job->commissions;
        $data['jobTeams']        = $job->teams;

        return view('pages.job.edit', $data);
    }

    public function convertIncome(ConvertJobIncomeRequest $request, Job $job)
    {
        $request->validated();

        activity('convert_currency')
            ->causedBy(auth()->user())
            ->performedOn($job)
            ->withProperties($request->all())
            ->event('currency_converted')
            ->log('currency_converted');

        foreach ($request->rate as $key => $rate) {
            $jobIncome          = JobIncome::find($key);
            $jobIncome->nominal = $jobIncome->payment * $rate;
            $jobIncome->to_idr  = $rate;
            $jobIncome->save();
        }

        return redirect(route('job_statement.index', $job));
    }

    public function storeJobStock(
        SaveJobStockRequest $request,
        Job $job,
        InventoryStockBalanceService $balanceService
    )
    {
        $request->validated();

        DB::beginTransaction();
        try {
            $jobStatement    = new JobStatement();
            $inventory_stock = $balanceService->lockStock((int) $request->inventory_stock_id);
            $stockCostPerUnit = (float) $inventory_stock->getRawOriginal('purchase_cost_per_unit');
            $stockCost = $stockCostPerUnit * (float) $request->quantity;

            $jobStatement->job_id                 = $job->id;
            $jobStatement->is_sample              = $request->is_sample ? 1 : 0;
            $jobStatement->inventory_stock_id     = $request->inventory_stock_id;
            $jobStatement->quantity               = $request->quantity;
            $jobStatement->price_per_unit         = $request->price;
            $jobStatement->total                  = $request->total;
            $jobStatement->stock_cost            = $stockCost;
            $jobStatement->stock_cost_per_unit   = $stockCostPerUnit;
            $jobStatement->save();

            $balanceService->subtractFromStock($inventory_stock, (float) $request->quantity, $stockCost);
            updateProductStock($inventory_stock->product, $inventory_stock);

            # Simpan cost dari PO Sample
            if ($request->is_sample) {
                $poStock = $inventory_stock->getPoStock();
                $cost                   = new OutcomeCheque;
                $cost->job_statement_id = $jobStatement->id;
                $cost->code             = $job->id;
                $cost->code_type        = 'job';
                $cost->outcome_type_id  = 208001;
                $cost->po_stock_id      = $poStock?->id;
                $cost->amount           = $request->total;
                $cost->recipient_type   = 'supplier';
                $cost->recipient_id     = $poStock?->supplier_id;
                $cost->date             = date('Y-m-d');
                $cost->save();
            }
            
            $request->session()->flash('success', 'Data stock has been added to Job. ');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $request->session()->flash('danger', $th->getMessage());
        }
    
        return redirect(route('job_statement.index', $job));
    }

    public function updateJobStock(
        UpdateJobStockRequest $request,
        JobStatement $jobStatement,
        InventoryStockBalanceService $balanceService
    )
    {
        $request->validated();

        DB::transaction(function () use ($request, $jobStatement, $balanceService) {
            $oldStock = $balanceService->lockStock((int) $jobStatement->inventory_stock_id);
            $balanceService->restoreToStock(
                $oldStock,
                (float) $jobStatement->quantity,
                (float) $jobStatement->stock_cost
            );

            $inventory_stock = $balanceService->lockStock((int) $request->inventory_stock_id);
            $stockCostPerUnit = (float) $inventory_stock->getRawOriginal('purchase_cost_per_unit');
            $stockCost = $stockCostPerUnit * (float) $request->quantity;

            $jobStatement->job_id               = $request->job_id;
            $jobStatement->inventory_stock_id   = $request->inventory_stock_id;
            $jobStatement->quantity             = $request->quantity;
            $jobStatement->price_per_unit       = $request->price;
            $jobStatement->total                = $request->total;
            $jobStatement->stock_cost          = $stockCost;
            $jobStatement->stock_cost_per_unit = $stockCostPerUnit;
            $jobStatement->save();

            $balanceService->subtractFromStock($inventory_stock, (float) $request->quantity, $stockCost);

            if ($jobStatement->is_sample) {
                $poStock = $inventory_stock->getPoStock();

                foreach ($jobStatement->sampleCosts as $sampleCost) {
                    $sampleCost->po_stock_id = $poStock?->id;
                    $sampleCost->amount = $request->total;
                    $sampleCost->recipient_id = $poStock?->supplier_id;
                    $sampleCost->save();
                }
            }

            updateProductStock($oldStock->product, $oldStock);
            updateProductStock($inventory_stock->product, $inventory_stock);
        });

        $request->session()->flash('success', 'Data stock has been updated. ');

        return redirect(route('job_statement.index', $request->job_id));
    }

    public function destroyJobStock(
        Request $request,
        JobStatement $jobStatement,
        InventoryStockBalanceService $balanceService
    )
    {
        $jobId = $jobStatement->job_id;

        DB::beginTransaction();
        try {
            $inventory_stock = $balanceService->lockStock((int) $jobStatement->inventory_stock_id);
            $balanceService->restoreToStock(
                $inventory_stock,
                (float) $jobStatement->quantity,
                (float) $jobStatement->stock_cost
            );

            foreach ($jobStatement->sampleCosts as $sampleCost) {
                $sampleCost->delete();
            }

            $jobStatement->delete();
            updateProductStock($inventory_stock->product, $inventory_stock);

            $request->session()->flash('success', 'Data stock has been deleted.');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $request->session()->flash('danger', $th->getMessage());
        }

        return redirect(route('job_statement.index', $jobId));
    }

    public function storeJobPoStock(StoreJobPoStockRequest $request, Job $job)
    {
        $request->validated();

        $jobPoStock              = new JobPoStock;
        $jobPoStock->job_id      = $job->id;
        $jobPoStock->po_stock_id = $request->po_stock;
        $jobPoStock->product_id  = $request->product;
        $jobPoStock->qty         = $request->quantity;
        $jobPoStock->amount      = $request->amount;
        $jobPoStock->note        = $request->note;

        if ($jobPoStock->save()) {
            $poDetail = PoStockProduct::findOrFail($request->po_detail_id);
            $poDetail->remaining_qty -= $request->quantity;
            $poDetail->save();
        }

        $request->session()->flash('success', 'Data PO Stock has been added to Job. ');

        return redirect(route('job_statement.index', $job));
    }

    public function updateJobPoStock(UpdateJobPoStockRequest $request, JobPoStock $jobPoStock)
    {
        $request->validated();

        $oldQty = $jobPoStock->qty;

        $jobPoStock->qty    = $request->quantity;
        $jobPoStock->amount = $request->amount;
        $jobPoStock->note   = $request->note;

        if ($jobPoStock->save()) {
            $poDetail = PoStockProduct::findOrFail($request->po_detail_id);
            $poDetail->remaining_qty = ($poDetail->remaining_qty + $oldQty) - $request->quantity;
            $poDetail->save();
        }

        $request->session()->flash('success', 'Data PO Stock has been updated.');

        return redirect(route('job_statement.index', $jobPoStock->job));
    }

    public function destroyJobPoStock(Request $request, JobPoStock $jobPoStock)
    {
        $job = $jobPoStock->job;
        $poProductDetail = PoStockProduct::where([
            'po_stock_id' => $jobPoStock->po_stock_id,
            'product_id'  => $jobPoStock->product_id,
        ])->firstOrFail();

        $poProductDetail->remaining_qty += $jobPoStock->qty;

        if ($poProductDetail->save()) {
            $jobPoStock->delete();
        }

        $request->session()->flash('success', 'Data PO Stock has been deleted.');

        return redirect(route('job_statement.index', $job));
    }

    public function storeJobCommission(SaveJobCommissionRequest $request, Job $job)
    {
        $request->validated();

        $data = [
            'user_id'      => $request->marketing,
            'percentage'   => $request->percentage,
            'nominal'      => $request->nominal,
            'release_date' => $request->release_date,
            'note'         => $request->note,
        ];

        if ($job->commissions()->create($data)) {
            $request->session()->flash('success', 'Data commission has been added.');
        } else {
            $request->session()->flash('danger', 'Something went wrong. Please try again.');
        }

        return redirect(route('job_statement.index', $job));
    }

    public function updateJobCommission(SaveJobCommissionRequest $request, JobCommission $jobCommission)
    {
        $request->validated();

        $data = [
            'user_id'      => $request->marketing,
            'percentage'   => $request->percentage,
            'nominal'      => $request->nominal,
            'release_date' => $request->release_date,
            'note'         => $request->note,
        ];

        if ($jobCommission->update($data)) {
            $request->session()->flash('success', 'Data commission has been updated.');
        } else {
            $request->session()->flash('danger', 'Something went wrong. Please try again.');
        }

        return redirect(route('job_statement.index', $jobCommission->job));
    }

    public function destroyJobCommission(Request $request, JobCommission $jobCommission)
    {
        $job = $jobCommission->job;

        if ($jobCommission->delete()) {
            $request->session()->flash('success', 'Data commission has been deleted.');
        } else {
            $request->session()->flash('danger', 'Something went wrong. Please try again.');
        }

        return redirect(route('job_statement.index', $job));
    }

    public function changeStatus(Request $request, Job $job)
    {
        $openOrCloseStatus = $job->statusIsOpen() ? 'closed' : 'open';

        $data = [
            'total_expenses'   => !$job->statusIsOpen() ? 0 : $request->total_expenses,
            'gross_profit'     => !$job->statusIsOpen() ? 0 : $request->gross_profit,
            'sales_commission' => !$job->statusIsOpen() ? 0 : $request->sales_commission,
            'net_profit'       => !$job->statusIsOpen() ? 0 : $request->net_profit,

            'status_payment' => $openOrCloseStatus,
            'status'         => $openOrCloseStatus,
            'closing_date'   => !$job->statusIsOpen() ? null : date("Y-m-d"),
        ];

        $job->update($data);

        activity('job')
            ->causedBy(auth()->user())
            ->performedOn($job)
            ->withProperties($data)
            ->event("status_$openOrCloseStatus")
            ->log("status_$openOrCloseStatus");

        return redirect(route('job_statement.index', $job))->with('success', "Job status successfully changed to $openOrCloseStatus.");
    }
}
