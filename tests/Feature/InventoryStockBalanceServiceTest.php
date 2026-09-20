<?php

namespace Tests\Feature;

use App\Models\InventoryStock;
use App\Services\InventoryStockBalanceService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InventoryStockBalanceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('activitylog.enabled', false);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id')->default(0);
            $table->string('inventory_type')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('po_stock_id')->nullable();
            $table->string('stock_bucket')->default('stock');
            $table->string('product_sku')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('unit_id')->default(2);
            $table->double('amount')->nullable();
            $table->double('weight')->nullable();
            $table->double('purchase_cost')->default(0);
            $table->double('purchase_cost_per_unit')->default(0);
            $table->double('history_overall_qty')->default(0);
            $table->double('history_overall_avg')->default(0);
            $table->timestamps();
        });

        Schema::create('po_stock_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_stock_id');
            $table->unsignedBigInteger('product_id');
            $table->double('qty');
            $table->double('purchase_cost');
        });
    }

    public function test_add_to_active_stock_reuses_existing_row_with_same_key(): void
    {
        $productId = 101;
        $warehouse = 201;
        $poStockId = 9001;

        $this->createPoDetail($poStockId, $productId, 8, 10);

        $existingStockId = $this->createInventoryStock([
            'inventory_type' => 'in',
            'product_id' => $productId,
            'po_stock_id' => $poStockId,
            'stock_bucket' => 'stock',
            'warehouse_id' => $warehouse,
            'unit_id' => 2,
            'amount' => 5,
            'purchase_cost' => 80,
            'purchase_cost_per_unit' => 10,
        ]);

        $stock = DB::transaction(function () use ($productId, $poStockId, $warehouse) {
            return app(InventoryStockBalanceService::class)->addToActiveStock(
                $productId,
                $poStockId,
                $warehouse,
                2,
                'stock',
                3,
                30,
                [
                    'inventory_id' => 123456,
                    'inventory_type' => 'in',
                ]
            );
        });

        $this->assertSame($existingStockId, $stock->id);

        $refreshed = InventoryStock::findOrFail($existingStockId);

        $this->assertSame(1, InventoryStock::query()
            ->where('product_id', $productId)
            ->where('po_stock_id', $poStockId)
            ->where('warehouse_id', $warehouse)
            ->where('unit_id', 2)
            ->where('stock_bucket', 'stock')
            ->count());
        $this->assertSame(8.0, (float) $refreshed->amount);
        $this->assertSame(80.0, (float) $refreshed->purchase_cost);
        $this->assertSame(10.0, (float) $refreshed->purchase_cost_per_unit);
    }

    public function test_partial_receipt_uses_full_po_product_reference_cost(): void
    {
        $this->createPoDetail(9010, 110, 10, 76);

        $stock = DB::transaction(function () {
            return app(InventoryStockBalanceService::class)->addToActiveStock(
                110,
                9010,
                211,
                2,
                'stock',
                3,
                228,
                [
                    'inventory_id' => 123457,
                    'inventory_type' => 'in',
                    'reference_purchase_cost' => 760,
                    'reference_purchase_cost_per_unit' => 76,
                ]
            );
        });

        $this->assertSame(3.0, (float) $stock->amount);
        $this->assertSame(760.0, (float) $stock->purchase_cost);
        $this->assertSame(76.0, (float) $stock->purchase_cost_per_unit);
    }

    public function test_transfer_stock_merges_into_existing_destination_row_with_same_key(): void
    {
        $productId = 102;
        $warehouseA = 202;
        $warehouseB = 203;
        $poStockId = 9002;

        $this->createPoDetail($poStockId, $productId, 10, 10);

        $sourceStockId = $this->createInventoryStock([
            'inventory_type' => 'in',
            'product_id' => $productId,
            'po_stock_id' => $poStockId,
            'stock_bucket' => 'stock',
            'warehouse_id' => $warehouseA,
            'unit_id' => 2,
            'amount' => 10,
            'purchase_cost' => 100,
            'purchase_cost_per_unit' => 10,
        ]);

        $destinationStockId = $this->createInventoryStock([
            'inventory_type' => 'out',
            'product_id' => $productId,
            'po_stock_id' => $poStockId,
            'stock_bucket' => 'stock',
            'warehouse_id' => $warehouseB,
            'unit_id' => 2,
            'amount' => 4,
            'purchase_cost' => 100,
            'purchase_cost_per_unit' => 10,
        ]);

        $transfer = DB::transaction(function () use ($sourceStockId, $warehouseB) {
            $service = app(InventoryStockBalanceService::class);
            $sourceStock = $service->lockStock($sourceStockId);

            return $service->transferStock(
                $sourceStock,
                $warehouseB,
                6,
                [
                    'inventory_id' => 654321,
                    'inventory_type' => 'out',
                ]
            );
        });

        $this->assertSame($destinationStockId, $transfer['destination']->id);
        $this->assertSame(2, InventoryStock::query()
            ->where('product_id', $productId)
            ->where('po_stock_id', $poStockId)
            ->where('unit_id', 2)
            ->where('stock_bucket', 'stock')
            ->count());

        $sourceStock = InventoryStock::findOrFail($sourceStockId);
        $destinationStock = InventoryStock::findOrFail($destinationStockId);

        $this->assertSame(4.0, (float) $sourceStock->amount);
        $this->assertSame(100.0, (float) $sourceStock->purchase_cost);
        $this->assertSame(10.0, (float) $destinationStock->amount);
        $this->assertSame(100.0, (float) $destinationStock->purchase_cost);
    }

    public function test_transfering_stock_back_to_origin_reuses_original_row_instead_of_creating_third_record(): void
    {
        $productId = 103;
        $warehouseA = 204;
        $warehouseB = 205;
        $poStockId = 9003;

        $this->createPoDetail($poStockId, $productId, 10, 10);

        $originStockId = $this->createInventoryStock([
            'inventory_type' => 'in',
            'product_id' => $productId,
            'po_stock_id' => $poStockId,
            'stock_bucket' => 'stock',
            'warehouse_id' => $warehouseA,
            'unit_id' => 2,
            'amount' => 10,
            'purchase_cost' => 100,
            'purchase_cost_per_unit' => 10,
        ]);

        $firstTransfer = DB::transaction(function () use ($originStockId, $warehouseB) {
            $service = app(InventoryStockBalanceService::class);
            $originStock = $service->lockStock($originStockId);

            return $service->transferStock(
                $originStock,
                $warehouseB,
                4,
                [
                    'inventory_id' => 700001,
                    'inventory_type' => 'out',
                ]
            );
        });

        $returnedDestination = DB::transaction(function () use ($firstTransfer, $warehouseA) {
            $service = app(InventoryStockBalanceService::class);
            $destinationStock = $service->lockStock($firstTransfer['destination']->id);

            return $service->transferStock(
                $destinationStock,
                $warehouseA,
                4,
                [
                    'inventory_id' => 700002,
                    'inventory_type' => 'out',
                ]
            );
        });

        $this->assertSame($originStockId, $returnedDestination['destination']->id);
        $this->assertSame(2, InventoryStock::query()
            ->where('product_id', $productId)
            ->where('po_stock_id', $poStockId)
            ->where('unit_id', 2)
            ->where('stock_bucket', 'stock')
            ->count());

        $originStock = InventoryStock::findOrFail($originStockId);
        $returnSourceStock = InventoryStock::findOrFail($firstTransfer['destination']->id);

        $this->assertSame(10.0, (float) $originStock->amount);
        $this->assertSame(100.0, (float) $originStock->purchase_cost);
        $this->assertSame(0.0, (float) $returnSourceStock->amount);
        $this->assertSame(100.0, (float) $returnSourceStock->purchase_cost);
    }

    public function test_subtract_from_po_stock_preserves_reference_cost_and_unit_cost(): void
    {
        $this->createPoDetail(9004, 104, 10, 76);

        $stockId = $this->createInventoryStock([
            'inventory_type' => 'in',
            'product_id' => 104,
            'po_stock_id' => 9004,
            'stock_bucket' => 'stock',
            'warehouse_id' => 206,
            'unit_id' => 2,
            'amount' => 10,
            'purchase_cost' => 760,
            'purchase_cost_per_unit' => 76,
        ]);

        DB::transaction(function () use ($stockId) {
            $service = app(InventoryStockBalanceService::class);
            $stock = $service->lockStock($stockId);

            $service->subtractFromStock($stock, 3, 228);
        });

        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame(7.0, (float) $stock->amount);
        $this->assertSame(760.0, (float) $stock->purchase_cost);
        $this->assertSame(76.0, (float) $stock->purchase_cost_per_unit);
    }

    public function test_restore_to_po_stock_preserves_reference_cost_and_unit_cost(): void
    {
        $this->createPoDetail(9005, 105, 10, 76);

        $stockId = $this->createInventoryStock([
            'inventory_type' => 'in',
            'product_id' => 105,
            'po_stock_id' => 9005,
            'stock_bucket' => 'stock',
            'warehouse_id' => 207,
            'unit_id' => 2,
            'amount' => 7,
            'purchase_cost' => 760,
            'purchase_cost_per_unit' => 76,
        ]);

        DB::transaction(function () use ($stockId) {
            $service = app(InventoryStockBalanceService::class);
            $stock = $service->lockStock($stockId);

            $service->restoreToStock($stock, 3, 228);
        });

        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame(10.0, (float) $stock->amount);
        $this->assertSame(760.0, (float) $stock->purchase_cost);
        $this->assertSame(76.0, (float) $stock->purchase_cost_per_unit);
    }

    public function test_po_reference_cost_is_preserved_when_stock_reaches_zero(): void
    {
        $this->createPoDetail(9006, 106, 5, 20);

        $stockId = $this->createInventoryStock([
            'product_id' => 106,
            'po_stock_id' => 9006,
            'warehouse_id' => 208,
            'amount' => 5,
            'purchase_cost' => 100,
            'purchase_cost_per_unit' => 20,
        ]);

        DB::transaction(function () use ($stockId) {
            $service = app(InventoryStockBalanceService::class);
            $service->subtractFromStock($service->lockStock($stockId), 5, 100);
        });

        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame(0.0, (float) $stock->amount);
        $this->assertSame(100.0, (float) $stock->purchase_cost);
        $this->assertSame(20.0, (float) $stock->purchase_cost_per_unit);
    }

    public function test_po_stock_without_detail_preserves_stored_reference_cost(): void
    {
        $stockId = $this->createInventoryStock([
            'product_id' => 107,
            'po_stock_id' => 9007,
            'warehouse_id' => 209,
            'amount' => 8,
            'purchase_cost' => 240,
            'purchase_cost_per_unit' => 30,
        ]);

        DB::transaction(function () use ($stockId) {
            $service = app(InventoryStockBalanceService::class);
            $service->subtractFromStock($service->lockStock($stockId), 3, 90);
        });

        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame(5.0, (float) $stock->amount);
        $this->assertSame(240.0, (float) $stock->purchase_cost);
        $this->assertSame(30.0, (float) $stock->purchase_cost_per_unit);
    }

    public function test_non_po_stock_keeps_proportional_cost_behavior(): void
    {
        $stockId = $this->createInventoryStock([
            'product_id' => 108,
            'po_stock_id' => null,
            'warehouse_id' => 210,
            'amount' => 10,
            'purchase_cost' => 500,
            'purchase_cost_per_unit' => 50,
        ]);

        DB::transaction(function () use ($stockId) {
            $service = app(InventoryStockBalanceService::class);
            $service->subtractFromStock($service->lockStock($stockId), 4, 200);
        });

        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame(6.0, (float) $stock->amount);
        $this->assertSame(300.0, (float) $stock->purchase_cost);
        $this->assertSame(50.0, (float) $stock->purchase_cost_per_unit);
    }

    private function createInventoryStock(array $overrides): int
    {
        static $inventoryId = 200000;

        return DB::table('inventory_stocks')->insertGetId(array_merge([
            'inventory_id' => $inventoryId++,
            'inventory_type' => 'in',
            'po_stock_id' => null,
            'stock_bucket' => 'stock',
            'product_id' => null,
            'product_sku' => null,
            'warehouse_id' => null,
            'unit_id' => 2,
            'amount' => 0,
            'weight' => null,
            'purchase_cost' => 0,
            'purchase_cost_per_unit' => 0,
            'history_overall_qty' => 0,
            'history_overall_avg' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides, [
            'stock_bucket' => ($overrides['stock_bucket'] ?? null)
                ?: (($overrides['inventory_type'] ?? 'in') === 'sample' ? 'sample' : 'stock'),
        ]));
    }

    private function createPoDetail(int $poStockId, int $productId, float $qty, float $unitCost): void
    {
        DB::table('po_stock_product')->insert([
            'po_stock_id' => $poStockId,
            'product_id' => $productId,
            'qty' => $qty,
            'purchase_cost' => $unitCost,
        ]);
    }
}
