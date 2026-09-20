<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnStockRequest;
use App\Models\InventoryStock;
use App\Models\OtherIncome;
use App\Models\ReturnStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryStockBalanceService;

class ReturnStockController extends Controller
{
    public function index()
    {
        $pageTitle = 'Return Stock';

        $returnStocks = ReturnStock::with([
            'warehouse',
            'inventoryStock.product',
            'inventoryStock.unit'
        ])->get();

        return view('pages.return_stock.index', compact('returnStocks', 'pageTitle'));
    }

    public function store(StoreReturnStockRequest $request)
    {
        $qty   = floatval($request->qty);
        $total = $request->total;

        try {
            $inventoryStock = InventoryStock::findOrFail($request->inventory_stock_id);
            $currentStock   = (float) $inventoryStock->unitValue();
            $unitCost       = (float) $inventoryStock->getRawOriginal('purchase_cost_per_unit');
            $stockCost      = $unitCost * $qty;


            if ($currentStock < $qty) {
                $unitName = $inventoryStock->product?->unit?->unit_name ?? '';

                $formattedStock = strpos($currentStock, '.') !== false ? rtrim(rtrim(number_format($currentStock, 3, '.', ''), '0'), '.') : $currentStock;

                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $formattedStock . ' ' . $unitName
                ], 422);
            }

            session([
                'pending_return_stock' => [
                    'warehouse_id'       => $request->warehouse_id,
                    'inventory_stock_id' => $request->inventory_stock_id,
                    'unit_id'            => $request->unit_id,
                    'qty'                => $qty,
                    'total'              => $total,
                    'stock_cost'         => $stockCost,
                    'stock_cost_per_unit'=> $unitCost,
                ]
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Return stock data ready to process',
                'redirect' => '/accounting/other-income'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process return stock',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(ReturnStock $returnStock)
    {
        try {
            $otherIncomeId = $returnStock->other_income_id;

            $deleteOtherIncomeParam = request()->input('delete_other_income');

            $shouldDeleteOtherIncome = $deleteOtherIncomeParam  === true ||
                                         $deleteOtherIncomeParam  === "true" ||
                                         $deleteOtherIncomeParam  === 1 ||
                                         $deleteOtherIncomeParam  === "1";

            DB::transaction(function () use ($returnStock) {
                $inventoryStock = InventoryStock::find($returnStock->inventory_stock_id);
                if ($inventoryStock) {
                    app(InventoryStockBalanceService::class)->restoreToStock(
                        $inventoryStock,
                        floatval($returnStock->qty),
                        (float) $returnStock->stock_cost
                    );

                    updateProductStock($inventoryStock->product, $inventoryStock);
                }
            });

            if ($otherIncomeId) {
                $returnStock->other_income_id = null;
                $returnStock->save();
            }

            $returnStock->delete();

            if ($shouldDeleteOtherIncome && $otherIncomeId) {
                $otherIncome = OtherIncome::find($otherIncomeId);
                if ($otherIncome) {
                    $otherIncome->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Return stock deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete return stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveFromSession(Request $request)
    {
        if (!session()->has('pending_return_stock')) {
            return response()->json([
                'success' => false,
                'message' => 'No pending return stock data'
            ]);
        }

        $returnData = session('pending_return_stock');

        try {
            $inventoryStock = InventoryStock::findOrFail($returnData['inventory_stock_id']);
            $currentStock   = (float) $inventoryStock->unitValue();
            $returnQty      = floatval($returnData['qty']);

            if ($currentStock < $returnQty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . number_format($currentStock, 3)
                ]);
            }

            DB::transaction(function () use ($returnData, $returnQty, $inventoryStock) {
                ReturnStock::create([
                    'warehouse_id'         => $returnData['warehouse_id'],
                    'inventory_stock_id'   => $returnData['inventory_stock_id'],
                    'other_income_id'      => $returnData['other_income_id'],
                    'qty'                  => $returnQty,
                    'total'                => $returnData['total'],
                    'stock_cost'           => $returnData['stock_cost'] ?? 0,
                    'stock_cost_per_unit'  => $returnData['stock_cost_per_unit'] ?? 0,
                ]);

                $this->updateInventoryStock(
                    $inventoryStock,
                    $returnQty,
                    (float) ($returnData['stock_cost'] ?? 0)
                );
            });

            $this->clearSessionData();

            return response()->json([
                'success' => true,
                'message' => 'Return stock data saved'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearSession(Request $request = null)
    {
        $this->clearSessionData();

        if ($request && $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    private function clearSessionData()
    {
        session()->forget('pending_return_stock');
    }

    // ini buat mengembalikan nilai qty ke inventory stock mas
    private function updateInventoryStock($inventoryStock, $qty, $cost)
    {
        app(InventoryStockBalanceService::class)
            ->subtractFromStock($inventoryStock, floatval($qty), (float) $cost);

        updateProductStock($inventoryStock->product, $inventoryStock);
    }
}
