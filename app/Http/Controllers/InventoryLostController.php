<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryLostRequest;
use App\Http\Requests\UpdateInventoryLostRequest;
use App\Models\InventoryLost;
use App\Models\InventoryStock;
use App\Models\Supplier;
use App\Services\InventoryStockBalanceService;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryLostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['pageTitle']       = 'Inventory Lost';
        $data['inventoryStocks'] = InventoryStock::getReadyStock();
        $data['inventoryLosts']  = InventoryLost::orderByDesc('datetime')->get();
        $data['suppliers']       = Supplier::all();
        $data['warehouses']      = Warehouse::all();

        return view('pages.inventory_lost.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryLostRequest $request, InventoryStockBalanceService $balanceService)
    {
        $request->validated();

        DB::transaction(function () use ($request, $balanceService) {
            $inventoryStock = $balanceService->lockStock((int) $request->inventory_stock);
            $unitId = $inventoryStock->unit_id;
            $amount = $unitId === 2 ? (float) $request->quantity : 0;
            $weight = $unitId === 1 ? (float) $request->quantity : 0;
            $quantity = $unitId === 2 ? $amount : $weight;
            $unitCost = (float) $inventoryStock->getRawOriginal('purchase_cost_per_unit');
            $lostCost = $unitCost * $quantity;

            $inventoryLost = new InventoryLost;
            $inventoryLost->inventory_stock_id     = $inventoryStock->id;
            $inventoryLost->inventory_id           = $inventoryStock->inventory_id;
            $inventoryLost->inventory_type         = $inventoryStock->getStockBucket() === 'sample'
                ? 'sample'
                : $inventoryStock->inventory_type;
            $inventoryLost->product_id             = $inventoryStock->product_id;
            $inventoryLost->status                 = $request->status;
            $inventoryLost->datetime               = $request->datetime;
            $inventoryLost->unit_id                = $inventoryStock->unit_id;
            $inventoryLost->amount                 = $amount;
            $inventoryLost->weight                 = $weight;
            $inventoryLost->purchase_cost          = $lostCost;
            $inventoryLost->purchase_cost_per_unit = $unitCost;
            $inventoryLost->warehouse_id           = $inventoryStock->warehouse_id;
            $inventoryLost->notes                  = $request->notes;
            $inventoryLost->save();

            $balanceService->subtractFromStock($inventoryStock, $quantity, $lostCost);

            updateProductStock($inventoryStock->product, $inventoryStock);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryLost $inventoryLost): JsonResponse
    {
        $data['inventoryLost']    = $inventoryLost;
        $data['product']          = $inventoryLost->product;
        $data['productSkuFormat'] = $inventoryLost->product->skuFormat();
        $data['quantityValue']    = $inventoryLost->quantityValue();
        $data['sourceInventory']  = $inventoryLost->inventoryStock;
        $data['warehouse']        = $inventoryLost->warehouse;

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryLost $inventoryLost)
    {
        $data['inventoryLost']            = $inventoryLost;
        $data['inventoryLostDatetime']    = $inventoryLost->getRawOriginal('datetime');
        $data['inventoryLostCostPerUnit'] = currencyFormat($inventoryLost->purchase_cost_per_unit)
            .'/'
            .($inventoryLost->amount !== 0 ? 'Pcs' : 'Kg');
        $data['inventoryLostUnitValue']   = $inventoryLost->unitValue();
        $data['warehouse']                = $inventoryLost->warehouse;
        $data['unit']                     = $inventoryLost->unit;

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateInventoryLostRequest $request,
        InventoryLost $inventoryLost,
        InventoryStockBalanceService $balanceService
    )
    {
        $request->validated();

        DB::transaction(function () use ($request, $inventoryLost, $balanceService) {
            $oldStock = $balanceService->lockStock((int) $inventoryLost->inventory_stock_id);
            $oldQuantity = (float) $inventoryLost->unitValue();
            $oldCost = (float) $inventoryLost->purchase_cost;
            $balanceService->restoreToStock($oldStock, $oldQuantity, $oldCost);

            $inventoryStock = $balanceService->lockStock((int) $request->inventory_stock);
            $unitId = $inventoryStock->unit_id;
            $amount = $unitId === 2 ? (float) $request->quantity : 0;
            $weight = $unitId === 1 ? (float) $request->quantity : 0;
            $newQuantity = $unitId === 2 ? $amount : $weight;
            $unitCost = (float) $inventoryStock->getRawOriginal('purchase_cost_per_unit');
            $lostCost = $unitCost * $newQuantity;

            $inventoryLost->inventory_stock_id     = $inventoryStock->id;
            $inventoryLost->inventory_id           = $inventoryStock->inventory_id;
            $inventoryLost->inventory_type         = $inventoryStock->getStockBucket() === 'sample'
                ? 'sample'
                : $inventoryStock->inventory_type;
            $inventoryLost->product_id             = $inventoryStock->product_id;
            $inventoryLost->status                 = $request->status;
            $inventoryLost->datetime               = $request->datetime;
            $inventoryLost->unit_id                = $inventoryStock->unit_id;
            $inventoryLost->amount                 = $amount;
            $inventoryLost->weight                 = $weight;
            $inventoryLost->purchase_cost          = $lostCost;
            $inventoryLost->purchase_cost_per_unit = $unitCost;
            $inventoryLost->warehouse_id           = $inventoryStock->warehouse_id;
            $inventoryLost->notes                  = $request->notes;
            $inventoryLost->save();

            $balanceService->subtractFromStock($inventoryStock, $newQuantity, $lostCost);

            updateProductStock($oldStock->product, $oldStock);
            updateProductStock($inventoryStock->product, $inventoryStock);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, InventoryLost $inventoryLost)
    {
        DB::transaction(function () use ($inventoryLost) {
            $balanceService = app(InventoryStockBalanceService::class);
            $inventoryStock = $balanceService->lockStock((int) $inventoryLost->inventory_stock_id);
            $balanceService->restoreToStock(
                $inventoryStock,
                (float) $inventoryLost->unitValue(),
                (float) $inventoryLost->purchase_cost
            );

            $inventoryLost->delete();

            updateProductStock($inventoryStock->product, $inventoryStock);
        });
    }
}
