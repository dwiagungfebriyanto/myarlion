<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryMutationRequest;
use App\Http\Requests\UpdateInventoryMutationRequest;
use App\Models\InventoryMutation;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryStockBalanceService;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryMutationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['pageTitle']          = 'Inventory Mutation';
        $data['products']           = Product::all();
        $data['readyProducts']      = Product::readyStock();
        $data['inventoryMutations'] = InventoryMutation::latest()->get();
        $data['suppliers']          = Supplier::all();
        $data['warehouses']         = Warehouse::getActiveWarehouse();

        return view('pages.inventory_mutation.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryMutationRequest $request, InventoryStockBalanceService $balanceService)
    {
        $request->validated();

        DB::transaction(function () use ($request, $balanceService) {
            $newSku        = Product::findOrFail((int) $request->new_sku);
            $originalStock = $balanceService->lockStock((int) $request->stock);
            $sourceCostPerUnit = (float) $originalStock->getRawOriginal('purchase_cost_per_unit');
            $sourceCost = $sourceCostPerUnit * (float) $request->quantity;

            $inventoryMutation                            = new InventoryMutation();
            $inventoryMutation->original_product_id       = $request->sku;
            $inventoryMutation->original_stock_id         = $request->stock;
            $inventoryMutation->original_stock_type       = $originalStock->getStockBucket();
            $inventoryMutation->qty_mutation              = $request->quantity;
            $inventoryMutation->new_product_id            = $request->new_sku;
            $inventoryMutation->qty                       = $request->new_quantity;
            $inventoryMutation->unit_id                   = $newSku->unit_id;
            $inventoryMutation->price                     = $request->price;
            $inventoryMutation->source_purchase_cost      = $sourceCost;
            $inventoryMutation->source_purchase_cost_per_unit = $sourceCostPerUnit;
            $inventoryMutation->save();

            $balanceService->subtractFromStock($originalStock, (float) $request->quantity, $sourceCost);

            $destinationStock = $balanceService->addToActiveStock(
                (int) $inventoryMutation->new_product_id,
                $balanceService->resolvePoStockIdFromStock($originalStock),
                (int) $request->warehouse_destination,
                (int) $inventoryMutation->unit_id,
                $originalStock->getStockBucket(),
                (float) $inventoryMutation->qty,
                (float) $inventoryMutation->price,
                [
                    'inventory_id' => $inventoryMutation->id,
                    'inventory_type' => 'mutation',
                    'reference_purchase_cost' => (float) $inventoryMutation->price,
                    'reference_purchase_cost_per_unit' => (float) $inventoryMutation->qty !== 0.0
                        ? (float) $inventoryMutation->price / (float) $inventoryMutation->qty
                        : 0.0,
                ]
            );

            $inventoryMutation->destination_stock_id = $destinationStock->id;
            $inventoryMutation->save();

            updateProductStock(Product::find($request->sku), $originalStock);
            updateProductStock(Product::find($request->new_sku), $destinationStock);
        });
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryMutation $inventoryMutation)
    {
        $data['actionUrl']           = route('product.inventory-mutation.update', $inventoryMutation);
        $data['inventoryMutation']   = $inventoryMutation;
        $data['inventoryStock']      = $inventoryMutation->inventoryStock;
        $data['warehouse']           = $inventoryMutation->inventoryStock?->warehouse;
        $data['originalSku']         = $inventoryMutation->originalSku?->skuFormat();
        $data['originalStock']       = $inventoryMutation->originalStock;
        $data['originalStockFormat'] = $inventoryMutation->originalStock->inventoryFormat();
        $data['stockUnit']           = $inventoryMutation->originalStock->unit;
        $data['mutationUnit']        = $inventoryMutation->unit;
        $data['productOptions']      = '<option disabled selected>-- Select SKU --</option>';

        foreach (Product::all() as $product) {
            $unit       = ($product->unit_id === 1) ? 'Kg' : 'Pcs' ;
            $isSelected = ($product->id === $inventoryMutation->new_product_id)
                            ? 'selected' : '' ;

            $data['productOptions'] .= "<option value='$product->id' data-unit='$unit' $isSelected>"
                                    .$product->skuFormat()
                                    .'</option>';
        }

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateInventoryMutationRequest $request,
        InventoryMutation $inventoryMutation,
        InventoryStockBalanceService $balanceService
    )
    {
        $request->validated();

        DB::transaction(function () use ($request, $inventoryMutation, $balanceService) {
            $previousNewProductId = $inventoryMutation->new_product_id;
            $oldOriginalStock = $balanceService->lockStock((int) $inventoryMutation->original_stock_id);
            $oldDestinationStock = $inventoryMutation->destination_stock_id
                ? $balanceService->lockStock((int) $inventoryMutation->destination_stock_id)
                : null;
            $destinationWarehouseId = $request->warehouse_destination
                ?: $oldDestinationStock?->warehouse_id;

            $balanceService->restoreToStock(
                $oldOriginalStock,
                (float) $inventoryMutation->qty_mutation,
                (float) $inventoryMutation->source_purchase_cost
            );

            if ($oldDestinationStock) {
                $balanceService->subtractFromStock(
                    $oldDestinationStock,
                    (float) $inventoryMutation->qty,
                    (float) $inventoryMutation->price
                );
            }

            $newSku = Product::findOrFail((int) $request->new_sku);
            $newOriginalStock = $balanceService->lockStock((int) $request->stock);
            $sourceCostPerUnit = (float) $newOriginalStock->getRawOriginal('purchase_cost_per_unit');
            $sourceCost = $sourceCostPerUnit * (float) $request->quantity;

            $inventoryMutation->original_product_id       = $request->sku;
            $inventoryMutation->original_stock_id         = $request->stock;
            $inventoryMutation->original_stock_type       = $newOriginalStock->getStockBucket();
            $inventoryMutation->qty_mutation              = $request->quantity;
            $inventoryMutation->new_product_id            = $request->new_sku;
            $inventoryMutation->qty                       = $request->new_quantity;
            $inventoryMutation->unit_id                   = $newSku->unit_id;
            $inventoryMutation->price                     = $request->price;
            $inventoryMutation->source_purchase_cost      = $sourceCost;
            $inventoryMutation->source_purchase_cost_per_unit = $sourceCostPerUnit;
            $inventoryMutation->save();

            $balanceService->subtractFromStock($newOriginalStock, (float) $request->quantity, $sourceCost);

            $destinationStock = $balanceService->addToActiveStock(
                (int) $inventoryMutation->new_product_id,
                $balanceService->resolvePoStockIdFromStock($newOriginalStock),
                (int) $destinationWarehouseId,
                (int) $inventoryMutation->unit_id,
                $newOriginalStock->getStockBucket(),
                (float) $inventoryMutation->qty,
                (float) $inventoryMutation->price,
                [
                    'inventory_id' => $inventoryMutation->id,
                    'inventory_type' => 'mutation',
                    'reference_purchase_cost' => (float) $inventoryMutation->price,
                    'reference_purchase_cost_per_unit' => (float) $inventoryMutation->qty !== 0.0
                        ? (float) $inventoryMutation->price / (float) $inventoryMutation->qty
                        : 0.0,
                ]
            );

            $inventoryMutation->destination_stock_id = $destinationStock->id;
            $inventoryMutation->save();

            updateProductStock(Product::find($request->sku), $newOriginalStock);

            if ((int) $previousNewProductId !== (int) $request->new_sku) {
                updateProductStock(Product::find($previousNewProductId));
            }

            updateProductStock(Product::find($request->new_sku), $destinationStock);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryMutation $inventoryMutation)
    {
        DB::transaction(function () use ($inventoryMutation) {
            $balanceService = app(InventoryStockBalanceService::class);
            $originalStock = $balanceService->lockStock((int) $inventoryMutation->original_stock_id);
            $mutationStock = $inventoryMutation->destination_stock_id
                ? $balanceService->lockStock((int) $inventoryMutation->destination_stock_id)
                : null;

            $balanceService->restoreToStock(
                $originalStock,
                (float) $inventoryMutation->qty_mutation,
                (float) $inventoryMutation->source_purchase_cost
            );

            if ($mutationStock) {
                $balanceService->subtractFromStock(
                    $mutationStock,
                    (float) $inventoryMutation->qty,
                    (float) $inventoryMutation->price
                );
            }

            $mutationStockProductID = $mutationStock?->product_id;
            $inventoryMutation->delete();

            updateProductStock(Product::find($originalStock->product_id), $originalStock);

            if ($mutationStockProductID) {
                updateProductStock(Product::find($mutationStockProductID), $mutationStock);
            }
        });
    }

    public function getProductStockOptions(Request $request)
    {
        $selectedID    = $request->selected_id ?: null;
        $productID     = $request->product_id;
        $product       = Product::findOrFail($productID);
        $unit          = ($product->unit_id === 1) ? 'weight' : 'amount';
        $productStocks = $product->inventoryStocks()
                                    ->where($unit, '>', 0)
                                    ->get();

        $options = '<option disabled selected>-- Select Stock --</option>';

        foreach ($productStocks as $inventoryStock) {;
            $isSelected = ($selectedID === $inventoryStock->id) ? 'selected' : '';

            $options .= "<option value='$inventoryStock->id' $isSelected>"
                            .$inventoryStock->inventoryFormat()
                        ."</option>";
        }

        return $options;
    }
}
