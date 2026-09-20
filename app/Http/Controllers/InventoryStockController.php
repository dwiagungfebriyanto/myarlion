<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $inventoryStocks = ($request->query('type') !== 'sample')
            ? InventoryStock::stocks()
            : InventoryStock::samples();

        $filterWarehouse = $request->query('warehouse');
        if ($filterWarehouse && $filterWarehouse !== 'all') {
            $inventoryStocks->where('warehouse_id', $filterWarehouse);
        }

        if (!$request->boolean('show_all')) {
            $inventoryStocks->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('unit_id', 1)
                        ->where('weight', '>', 0);
                })->orWhere(function ($query) {
                    $query->where('unit_id', '!=', 1)
                        ->where('amount', '>', 0);
                });
            });
        }

        $stockType = ($request->query('type') !== 'sample') ? 'Stock' : 'Sample';
        
        $data['pageTitle']       = "Inventory $stockType";
        $data['warehouses']      = Warehouse::getActiveWarehouse();
        $data['inventoryStocks'] = $inventoryStocks->get();

        return view('pages.inventory_stock.index', $data);
    }

    public function getReadyStockOptions($selected = null) : JsonResponse
    {
        $stocks = InventoryStock::getReadyStock(
            request('with_sample') ?? true,
            request('supplier_id'),
            request('warehouse_id'),
            request('main_category_id')
        );
        $showBucket = request()->boolean('show_bucket');


        $options = '<option value="" disabled selected>-- Select Inventory --</option>';
        foreach ($stocks as $inventoryStock) {
            $stockQty = $inventoryStock->unitValue();

            $unitName      = $inventoryStock->unit->unit_name;
            $warehouseName = $inventoryStock->warehouse->warehouse_name;
            $bucket        = $inventoryStock->getStockBucket();
            $labelPrefix   = $showBucket ? '[' . ucfirst($bucket) . '] ' : '';
            $isSelected    = ($inventoryStock->id == $selected) ? 'selected' : '';

            $options .= "<option value='$inventoryStock->id'"
                            ."data-unit='$inventoryStock->unit_id'"
                            ."data-unit-name='$unitName'"
                            ."data-cost-per-unit='$inventoryStock->purchase_cost_per_unit'"
                            ."data-stock='$stockQty'"
                            ."data-stock-bucket='$bucket'"
                            ."data-warehouse-id='$inventoryStock->warehouse_id'"
                            ."data-warehouse='$warehouseName'"
                            ."data-product-id='$inventoryStock->product_id'"
                            ."data-sku='{$inventoryStock->product->sku}'"
                            ."$isSelected>"
                            .$labelPrefix .$inventoryStock->inventoryFormat()
                        ."</option>";
        }

        return response()->json(['options' => $options]);
    }
}
