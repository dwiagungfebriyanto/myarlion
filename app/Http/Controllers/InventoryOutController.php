<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryOutRequest;
use App\Http\Requests\UpdateInventoryOutRequest;
use App\Models\InventoryOut;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryStockBalanceService;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InventoryOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['pageTitle']     = 'Inventory Out';
        $data['products']      = Product::all();
        $data['units']         = Unit::orderBy('unit_name')->get();
        $data['warehouses']    = Warehouse::getActiveWarehouse();
        $data['suppliers']     = Supplier::all();
        $data['inventoryOuts'] = InventoryOut::orderByDesc('datetime')->get();

        return view('pages.inventory_out.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->noContent();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryOutRequest $request, InventoryStockBalanceService $balanceService)
    {
        $request->validated();

        DB::transaction(function () use ($request, $balanceService) {
            $inventoryStock = $balanceService->lockStock((int) $request->inventory_stock);
            $amount = ($inventoryStock->unit_id == 2) ? (float) $request->quantity : 0;
            $weight = ($inventoryStock->unit_id == 1) ? (float) $request->quantity : 0;
            $usedValue = ($inventoryStock->unit_id == 2) ? $amount : $weight;
            $costPerUnit = (float) $inventoryStock->getRawOriginal('purchase_cost_per_unit');
            $movedCost = $costPerUnit * $usedValue;

            $inventoryOut                         = new InventoryOut;
            $inventoryOut->inventory_in_id        = $inventoryStock->getRootInventoryInId() ?? $inventoryStock->inventory_id;
            $inventoryOut->original_stock_id      = $inventoryStock->id;
            $inventoryOut->product_id             = $inventoryStock->product_id;
            $inventoryOut->status                 = $request->status;
            $inventoryOut->datetime               = $request->datetime;
            $inventoryOut->unit_id                = $inventoryStock->unit_id;
            $inventoryOut->amount                 = $amount;
            $inventoryOut->weight                 = $weight;
            $inventoryOut->warehouse_id           = $request->destination_warehouse;
            $inventoryOut->original_warehouse_id  = $inventoryStock->warehouse_id;
            $inventoryOut->notes                  = $request->notes;
            $inventoryOut->purchase_cost          = $movedCost;
            $inventoryOut->purchase_cost_per_unit = $costPerUnit;
            $inventoryOut->save();

            $transfer = $balanceService->transferStock(
                $inventoryStock,
                (int) $request->destination_warehouse,
                (float) $usedValue,
                [
                    'inventory_id' => $inventoryOut->id,
                    'inventory_type' => $inventoryStock->getStockBucket() === 'sample' ? 'sample' : 'out',
                ]
            );

            $inventoryOut->destination_stock_id = $transfer['destination']->id;
            $inventoryOut->save();

            updateHighestPriceProduct($inventoryStock->product);
            updateProductStock($inventoryStock->product, $transfer['destination']);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryOut $inventoryOut): JsonResponse
    {
        $data['inventoryOut']        = $inventoryOut;
        $data['product']             = $inventoryOut->product;
        $data['productSkuFormat']    = $inventoryOut->product?->skuFormat();
        $data['quantityValue']       = $inventoryOut->quantityValue();
        $data['warehouse']           = $inventoryOut->warehouse;
        $data['originalStock']       = $inventoryOut->originalStock;
        $data['originalWarehouse']   = $inventoryOut->originalWarehouse();
        $data['purchaseCost']        = currencyFormat($inventoryOut->purchase_cost);
        $data['purchaseCostPerUnit'] = currencyFormat($inventoryOut->purchase_cost_per_unit);

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryOut $inventoryOut)
    {
        $warehouseController = new WarehouseController();

        $data['inventoryOut']         = $inventoryOut;
        $data['inventoryOutQuantity'] = $inventoryOut->unitValue();
        $data['inventoryOutDatetime'] = $inventoryOut->getRawOriginal('datetime');
        $data['inventoryStockSku']    = $inventoryOut->originalStock->inventoryFormat();
        $data['inventoryUnit']        = $inventoryOut->unit;
        $data['purchaseCostPerUnit']  = currencyFormat($inventoryOut->purchase_cost_per_unit) ." /" .$inventoryOut->unit->unit_name;
        $data['originalWarehouse']    = $inventoryOut->originalWarehouse();
        $data['warehouseOptions']     = $warehouseController->getWarehouseOptions([$inventoryOut->original_warehouse_id], $inventoryOut->warehouse_id);

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateInventoryOutRequest $request,
        InventoryOut $inventoryOut,
        InventoryStockBalanceService $balanceService
    )
    {
        $request->validated();

        if ($inventoryOut->inventoryLosts()->exists()) {
            session()->flash('danger', 'Inventory Out with downstream losses cannot be updated.');

            return redirect(route('product.inventory-out.index'));
        }

        DB::transaction(function () use ($request, $inventoryOut, $balanceService) {
            $oldOriginalStock = $balanceService->lockStock((int) $inventoryOut->original_stock_id);
            $oldDestinationStock = $inventoryOut->destination_stock_id
                ? $balanceService->lockStock((int) $inventoryOut->destination_stock_id)
                : null;
            $oldQuantity = (float) $inventoryOut->unitValue();
            $oldCost = (float) $inventoryOut->getRawOriginal('purchase_cost');

            if (!$oldDestinationStock || $oldDestinationStock->id !== $oldOriginalStock->id) {
                $balanceService->restoreToStock($oldOriginalStock, $oldQuantity, $oldCost);

                if ($oldDestinationStock) {
                    $balanceService->subtractFromStock($oldDestinationStock, $oldQuantity, $oldCost);
                }
            }

            $newOriginalStock = $balanceService->lockStock((int) $request->inventory_stock);
            $amount = ($newOriginalStock->unit_id === 2) ? (float) $request->quantity : 0;
            $weight = ($newOriginalStock->unit_id === 1) ? (float) $request->quantity : 0;
            $newQuantity = $newOriginalStock->unit_id === 2 ? $amount : $weight;
            $costPerUnit = (float) $newOriginalStock->getRawOriginal('purchase_cost_per_unit');
            $movedCost = $costPerUnit * $newQuantity;

            $inventoryOut->inventory_in_id        = $newOriginalStock->getRootInventoryInId() ?? $newOriginalStock->inventory_id;
            $inventoryOut->original_stock_id      = $newOriginalStock->id;
            $inventoryOut->product_id             = $newOriginalStock->product_id;
            $inventoryOut->status                 = $request->status;
            $inventoryOut->datetime               = $request->datetime;
            $inventoryOut->unit_id                = $newOriginalStock->unit_id;
            $inventoryOut->amount                 = $amount;
            $inventoryOut->weight                 = $weight;
            $inventoryOut->warehouse_id           = $request->destination_warehouse;
            $inventoryOut->original_warehouse_id  = $newOriginalStock->warehouse_id;
            $inventoryOut->notes                  = $request->notes;
            $inventoryOut->purchase_cost          = $movedCost;
            $inventoryOut->purchase_cost_per_unit = $costPerUnit;
            $inventoryOut->save();

            $transfer = $balanceService->transferStock(
                $newOriginalStock,
                (int) $request->destination_warehouse,
                (float) $newQuantity,
                [
                    'inventory_id' => $inventoryOut->id,
                    'inventory_type' => $newOriginalStock->getStockBucket() === 'sample' ? 'sample' : 'out',
                ]
            );

            $inventoryOut->destination_stock_id = $transfer['destination']->id;
            $inventoryOut->save();

            updateHighestPriceProduct($newOriginalStock->product);
            updateProductStock($newOriginalStock->product, $transfer['destination']);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, InventoryOut $inventoryOut)
    {
        DB::transaction(function () use ($inventoryOut) {
            $balanceService = app(InventoryStockBalanceService::class);
            $originalStock = $balanceService->lockStock((int) $inventoryOut->original_stock_id);
            $destinationStock = $inventoryOut->destination_stock_id
                ? $balanceService->lockStock((int) $inventoryOut->destination_stock_id)
                : null;

            foreach ($inventoryOut->inventoryLosts as $inventoryLost) {
                if ($destinationStock) {
                    $lostQty = (float) $inventoryLost->unitValue();
                    $lostCost = (float) ($inventoryLost->purchase_cost ?? 0);
                    $balanceService->restoreToStock($destinationStock, $lostQty, $lostCost);
                }

                $inventoryLost->delete();
            }

            $outQuantity = (float) $inventoryOut->unitValue();
            $outCost = (float) $inventoryOut->getRawOriginal('purchase_cost');

            if (!$destinationStock || $destinationStock->id !== $originalStock->id) {
                $balanceService->restoreToStock($originalStock, $outQuantity, $outCost);

                if ($destinationStock) {
                    $balanceService->subtractFromStock($destinationStock, $outQuantity, $outCost);
                }
            }

            $inventoryOut->delete();

            updateHighestPriceProduct($originalStock->product);
            updateProductStock($originalStock->product, $originalStock);
        });

        session()->flash('success', 'Inventory Out has been deleted.');

        return redirect(route('product.inventory-out.index'));
    }
}
