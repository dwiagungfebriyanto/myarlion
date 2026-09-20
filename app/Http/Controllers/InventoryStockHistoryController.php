<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Services\InventoryStockHistoryService;
use Illuminate\View\View;

class InventoryStockHistoryController extends Controller
{
    public function __construct(
        private readonly InventoryStockHistoryService $historyService
    ) {
    }

    public function show(InventoryStock $inventoryStock): View
    {
        abort_if($inventoryStock->getStockBucket() !== 'stock', 404);

        $poStock = $inventoryStock->getPoStock();
        abort_if(is_null($poStock), 404);

        $history = $this->historyService->build($inventoryStock);

        return view('pages.inventory_stock.history', [
            'pageTitle' => 'Stock History',
            'inventoryStock' => $inventoryStock,
            'poStock' => $poStock,
            'supplier' => $poStock->supplier,
            'product' => $inventoryStock->product,
            'warehouse' => $inventoryStock->warehouse,
            'unit' => $inventoryStock->unit,
            'events' => $history['events'],
            'currentStockDisplay' => $history['current_stock_display'],
            'ledgerEndingStockDisplay' => $history['ledger_ending_stock_display'],
            'hasBalanceMismatch' => $history['has_balance_mismatch'],
        ]);
    }
}
