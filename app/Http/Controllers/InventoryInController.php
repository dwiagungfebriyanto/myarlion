<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryInRequest;
use App\Http\Requests\UpdateInventoryInRequest;
use App\Models\InventoryIn;
use App\Models\InventoryStock;
use App\Models\JobStatement;
use App\Models\PoStock;
use App\Models\PoStockProduct;
use App\Models\Product;
use App\Services\InventoryStockBalanceService;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InventoryInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : View
    {
        $data['pageTitle']    = 'Inventory In';
        $data['products']     = Product::all();
        $data['units']        = Unit::orderBy('unit_name')->get();
        $data['warehouses']   = Warehouse::getActiveWarehouse();
        $data['inventoryIns'] = InventoryIn::orderByDesc('datetime')->get();

        $poStockController = new PurchaseOrderController();
        $data['poStocks']  = $poStockController->getPoOptions();

        return view('pages.inventory_in.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryInRequest $request, InventoryStockBalanceService $balanceService)
    {
        $request->validated();

        $purchaseOrder = PoStock::findOrFail($request->po_stock);

        // simpan inventory in untuk setiap product dari po
        foreach ($purchaseOrder->poStockDetail as $poDetail) {
            $productID = $poDetail->product_id;

            $amount = ($request->unit[$productID] == 2) ? $request->quantity[$productID] : 0;
            $weight = ($request->unit[$productID] == 1) ? $request->quantity[$productID] : 0;
            $qtyIn  = ($request->unit[$productID] == 2) ? (float) $amount : (float) $weight;

            if ($qtyIn != 0) {
                DB::transaction(function () use (
                    $balanceService,
                    $purchaseOrder,
                    $poDetail,
                    $request,
                    $amount,
                    $weight,
                    $qtyIn
                ) {
                    $purchaseCostPerUnit = $poDetail->purchase_cost;
                    $purchaseCost = ($qtyIn === $poDetail->qty) && count($purchaseOrder->poStockDetail) === 1
                        ? $purchaseOrder->total
                        : $poDetail->purchase_cost * $qtyIn;

                    $inventoryIn = new InventoryIn;
                    $inventoryIn->po_stock_id            = $poDetail->po_stock_id;
                    $inventoryIn->product_id             = $poDetail->product_id;
                    $inventoryIn->status                 = $request->status;
                    $inventoryIn->datetime               = $request->datetime;
                    $inventoryIn->unit_id                = $poDetail->unit_id;
                    $inventoryIn->amount                 = $amount;
                    $inventoryIn->weight                 = $weight;
                    $inventoryIn->warehouse_id           = $request->warehouse;
                    $inventoryIn->notes                  = $request->notes;
                    $inventoryIn->purchase_cost          = $purchaseCost;
                    $inventoryIn->purchase_cost_per_unit = $purchaseCostPerUnit;
                    $inventoryIn->save();

                    $poDetail->remaining_qty -= $qtyIn;
                    $poDetail->save();

                    $inventoryStock = $balanceService->addToActiveStock(
                        (int) $poDetail->product_id,
                        (int) $poDetail->po_stock_id,
                        (int) $request->warehouse,
                        (int) $poDetail->unit_id,
                        $purchaseOrder->po_type === 'sample' ? 'sample' : 'stock',
                        $qtyIn,
                        (float) $purchaseCost,
                        [
                            'inventory_id' => $inventoryIn->id,
                            'inventory_type' => $purchaseOrder->po_type === 'sample' ? 'sample' : 'in',
                            'reference_purchase_cost' => round(
                                (float) $poDetail->purchase_cost * (float) $poDetail->qty,
                                2
                            ),
                            'reference_purchase_cost_per_unit' => (float) $poDetail->purchase_cost,
                        ]
                    );

                    $inventoryIn->destination_stock_id = $inventoryStock->id;
                    $inventoryIn->save();

                    updateHighestPriceProduct($inventoryIn->product);
                    updateAvgOnAddInventory($inventoryIn->product, $purchaseCostPerUnit, $qtyIn);
                    updateProductStock($inventoryIn->product, $inventoryStock);
                });
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryIn $inventoryIn): JsonResponse
    {
        $data['inventoryIn']      = $inventoryIn;
        $data['poStock']          = $inventoryIn->poStock;
        $data['product']          = $inventoryIn->product;
        $data['productSkuFormat'] = $inventoryIn->product->skuFormat();
        $data['quantityValue']    = $inventoryIn->quantityValue();
        $data['warehouse']        = $inventoryIn->warehouse;

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryIn $inventoryIn)
    {
        $data['skuData']             = $inventoryIn->product->skuFormat();
        $data['warehouseOptions']    = selectGenerate('Warehouse', Warehouse::getActiveWarehouse(), 'id', 'warehouse_name', $inventoryIn->warehouse_id);
        $data['inventoryIn']         = $inventoryIn;
        $data['inventoryInQuantity'] = $inventoryIn->unitValue();
        $data['inventoryInDatetime'] = $inventoryIn->datetime;
        $data['unit']                = $inventoryIn->unit;
        $data['poStock']             = $inventoryIn->poStock;
        $data['updateActionUrl']     = route('product.inventory-in.update', $inventoryIn);

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateInventoryInRequest $request,
        InventoryIn $inventoryIn,
        InventoryStockBalanceService $balanceService
    )
    {
        $request->validated();

        $stockReduction = $inventoryIn->inventoryOutsSum() + $inventoryIn->inventoryLostsSum();
        $oldWarehouseId = $inventoryIn->warehouse_id;

        $amount = ($request->unit_name === 'Pcs')
            ? $request->quantity
            : 0;

        $weight = ($request->unit_name === 'Kg')
            ? $request->quantity
            : 0;

        $usedUnit = ($request->unit_name === 'Pcs')
            ? 'amount'
            : 'weight';

        $usedValue = ($request->unit_name === 'Pcs')
            ? $amount
            : $weight;

        $poDetail = PoStockProduct::where('po_stock_id', $inventoryIn->po_stock_id)
            ->where('product_id', $inventoryIn->product_id)
            ->first();

        // nilai untuk mengurangi remaining_qty po stock product (terdahulu)
        $oldQty = $inventoryIn->$usedUnit;

        $purchaseCost = (float) $request->purchase_cost;
        $purchaseCostPerUnit = $usedValue != 0 ? $purchaseCost / $usedValue : 0;
        $oldUsedValue = (float) $inventoryIn->$usedUnit;
        $oldPurchaseCost = (float) $inventoryIn->getRawOriginal('purchase_cost');
        $oldDestinationStockId = $inventoryIn->destination_stock_id;
        $bucket = $inventoryIn->poStock?->po_type === 'sample' ? 'sample' : 'stock';

        DB::transaction(function () use (
            $balanceService,
            $inventoryIn,
            $oldDestinationStockId,
            $oldUsedValue,
            $oldPurchaseCost,
            $request,
            $amount,
            $weight,
            $purchaseCost,
            $purchaseCostPerUnit,
            $poDetail,
            $oldQty,
            $usedValue,
            $bucket,
            $oldWarehouseId
        ) {
            if ($oldDestinationStockId) {
                $oldDestinationStock = $balanceService->lockStock((int) $oldDestinationStockId);
                $balanceService->subtractFromStock($oldDestinationStock, $oldUsedValue, $oldPurchaseCost);
            }

            $inventoryIn->status                 = $request->status;
            $inventoryIn->datetime               = $request->datetime;
            $inventoryIn->warehouse_id           = $request->warehouse;
            $inventoryIn->amount                 = $amount;
            $inventoryIn->weight                 = $weight;
            $inventoryIn->notes                  = $request->notes;
            $inventoryIn->purchase_cost          = $purchaseCost;
            $inventoryIn->purchase_cost_per_unit = $purchaseCostPerUnit;
            $inventoryIn->save();

            $poDetail->remaining_qty = ($poDetail->remaining_qty + $oldQty) - $usedValue;
            $poDetail->save();

            $inventoryStock = $balanceService->addToActiveStock(
                (int) $inventoryIn->product_id,
                (int) $inventoryIn->po_stock_id,
                (int) $request->warehouse,
                (int) $inventoryIn->unit_id,
                $bucket,
                (float) $usedValue,
                $purchaseCost,
                [
                    'inventory_id' => $inventoryIn->id,
                    'inventory_type' => $bucket === 'sample' ? 'sample' : 'in',
                    'reference_purchase_cost' => round(
                        (float) $poDetail->purchase_cost * (float) $poDetail->qty,
                        2
                    ),
                    'reference_purchase_cost_per_unit' => (float) $poDetail->purchase_cost,
                ]
            );

            $inventoryIn->destination_stock_id = $inventoryStock->id;
            $inventoryIn->save();

            if ($request->warehouse != $oldWarehouseId) {
                $inventoryIn->inventoryOuts()->update([
                    'original_warehouse_id' => $request->warehouse,
                ]);

                $inventoryIn->inventoryLosts()->update([
                    'warehouse_id' => $request->warehouse,
                ]);
            }

            updateHighestPriceProduct($inventoryIn->product);
            updateProductStock($inventoryIn->product, $inventoryStock);
            syncInventoryStockHistory($inventoryStock);
        });
    }

    public function history(InventoryIn $inventoryIn) : View
    {
        $destinationStock = $inventoryIn->inventoryStock;

        $data['pageTitle']               = 'Inventory In History';
        $data['inventoryIn']             = $inventoryIn;
        $data['inventoryToJobHistories'] = $destinationStock
            ? JobStatement::with(['job.customer', 'job.marketing'])
                ->where('inventory_stock_id', $destinationStock->id)
                ->get()
            : collect();

        return view('pages.inventory_in.history', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, InventoryIn $inventoryIn): RedirectResponse
    {
        if ($inventoryIn->inventoryOuts()->exists() || $inventoryIn->inventoryLosts()->exists()) {
            $request->session()->flash('danger', 'Inventory In with downstream transfers or losses cannot be deleted.');

            return redirect(route('product.inventory-in.index'));
        }

        $inventoryProduct = $inventoryIn->product;
        $unitField = $inventoryIn->unit_id === 1 ? 'weight' : 'amount';
        $quantity = (float) $inventoryIn->$unitField;
        $purchaseCost = (float) $inventoryIn->getRawOriginal('purchase_cost');

        DB::transaction(function () use ($inventoryIn, $inventoryProduct, $quantity, $purchaseCost) {
            $destinationStock = $inventoryIn->inventoryStock;
            if ($destinationStock) {
                app(InventoryStockBalanceService::class)
                    ->subtractFromStock($destinationStock, $quantity, $purchaseCost);
            }

            if ($inventoryIn->po_stock_id && $inventoryIn->product_id) {
                $poDetail = PoStockProduct::where('po_stock_id', $inventoryIn->po_stock_id)
                    ->where('product_id', $inventoryIn->product_id)
                    ->first();

                if ($poDetail) {
                    $poDetail->remaining_qty += $quantity;
                    $poDetail->save();
                }
            }

            $inventoryIn->delete();

            updateHighestPriceProduct($inventoryProduct);
            updateProductStock($inventoryProduct);
            updateAvgOnDeleteInventory($inventoryProduct);
        });

        $request->session()->flash('success', 'Inventory In data successfully deleted.');

        return redirect(route('product.inventory-in.index'));
    }
}
