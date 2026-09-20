<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sample\StoreFreeSampleRequest;
use App\Models\Customer;
use App\Models\FreeSample;
use App\Models\Inquiry;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Services\InventoryStockBalanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class FreeSampleController extends Controller
{
    public function index(Request $request) : View
    {
        $warehouseFilter = $request->warehouse;

        $data['pageTitle']   = 'Free Sample';
        $data['inquiries']   = Inquiry::with(['customer'])
            ->whereNull('job_id')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();
        $data['customers']   = Customer::orderBy('code')->orderBy('name')->get();
        $data['suppliers']   = Supplier::all();
        $data['warehouses']  = Warehouse::all();
        $data['freeSamples'] = FreeSample::with([
            'Customer',
            'inquiry',
            'inventoryStock.product',
            'inventoryStock.unit',
            'inventoryStock.warehouse',
        ])->whereHas('inventoryStock', function ($query) use ($warehouseFilter) {
            if ($warehouseFilter && $warehouseFilter !== 'all') {
                $query->where('warehouse_id', $warehouseFilter);
            }
        })->get();

        return view('pages.free_sample.index', $data);
    }

    public function store(StoreFreeSampleRequest $request, InventoryStockBalanceService $balanceService)
    {
        $validated = $request->validated();
        $recipientType = $validated['recipient_type'];

        try {
            DB::transaction(function () use ($validated, $balanceService, $recipientType) {
                $inventoryStock = $balanceService->lockStock((int) $validated['inventory_stock_id']);
                $inquiry = null;
                $customerId = null;

                if ($recipientType === 'inquiry') {
                    $inquiry = Inquiry::query()
                        ->lockForUpdate()
                        ->with('customer')
                        ->whereKey($validated['inquiry'])
                        ->whereNull('job_id')
                        ->firstOrFail();
                    $customerId = is_null($inquiry->customer_id) ? null : (int) $inquiry->customer_id;
                } else {
                    $customerId = (int) $validated['customer'];
                }

                $unitCost = (float) $inventoryStock->getRawOriginal('purchase_cost_per_unit');
                $totalCost = $unitCost * (float) $validated['quantity'];

                $freeSample = new FreeSample();
                $freeSample->inventory_stock_id     = $validated['inventory_stock_id'];
                $freeSample->inquiry_id             = $inquiry?->id;
                $freeSample->quantity               = $validated['quantity'];
                $freeSample->purchase_cost          = round($totalCost, 2);
                $freeSample->purchase_cost_per_unit = round($unitCost, 2);
                $freeSample->customer_id            = $customerId;
                $freeSample->date                   = $validated['date'];
                $freeSample->save();

                $inventoryStock = $balanceService->subtractFromStock(
                    $inventoryStock,
                    (float) $validated['quantity'],
                    $totalCost
                );

                updateProductStock($inventoryStock->product, $inventoryStock);
            });
        } catch (QueryException $exception) {
            report($exception);

            return redirect()->back()
                ->withInput()
                ->with('error', $this->resolveStoreErrorMessage($exception));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Free Sample gagal disimpan. Silakan cek data recipient, inventory, dan coba lagi.');
        }

        return redirect()->back()->with('success', 'Free Sample stored successfully.');
    }

    private function resolveStoreErrorMessage(QueryException $exception): string
    {
        $message = Str::lower($exception->getMessage());

        $isNullableCustomerIssue = str_contains($message, 'customer_id')
            && (
                str_contains($message, 'cannot be null')
                || str_contains($message, 'not null')
                || str_contains($message, 'integrity constraint violation')
            );

        if ($isNullableCustomerIssue) {
            return 'Free Sample gagal disimpan karena database belum mendukung inquiry tanpa customer. Jalankan migration yang membuat `free_samples.customer_id` nullable.';
        }

        return 'Free Sample gagal disimpan karena terjadi kesalahan database. Silakan periksa data yang dipilih lalu coba lagi.';
    }
}
