<?php

namespace Tests\Feature;

use App\DataTables\PurchaseOrderDataTable;
use App\Models\PoStock;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PurchaseOrderDataTableFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('activitylog.enabled', false);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('supplier_name')->nullable();
            $table->timestamps();
        });

        Schema::create('po_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->nullable();
            $table->string('po_type')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('status')->default('incomplete');
            $table->timestamps();
        });

        Schema::create('po_stock_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_stock_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->double('qty')->default(0);
            $table->double('remaining_qty')->default(0);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->double('price')->default(0);
            $table->double('purchase_cost')->default(0);
            $table->timestamps();
        });
    }

    public function test_default_filter_only_shows_incomplete_purchase_orders_with_remaining_qty(): void
    {
        $this->createPurchaseOrder('PO-INCOMPLETE-ACTIVE', 'stock', 'incomplete', 5);
        $this->createPurchaseOrder('PO-INCOMPLETE-EMPTY', 'stock', 'incomplete', 0);
        $this->createPurchaseOrder('PO-COMPLETE-ACTIVE', 'stock', 'complete', 3);

        $uniqueIds = $this->queryUniqueIds([]);

        $this->assertSame(['PO-INCOMPLETE-ACTIVE'], $uniqueIds);
    }

    public function test_explicit_status_filter_is_respected_while_show_all_is_disabled(): void
    {
        $this->createPurchaseOrder('PO-COMPLETE-ACTIVE', 'stock', 'complete', 2);
        $this->createPurchaseOrder('PO-COMPLETE-EMPTY', 'stock', 'complete', 0);
        $this->createPurchaseOrder('PO-INCOMPLETE-ACTIVE', 'stock', 'incomplete', 4);

        $uniqueIds = $this->queryUniqueIds([
            'status' => 'complete',
        ]);

        $this->assertSame(['PO-COMPLETE-ACTIVE'], $uniqueIds);
    }

    public function test_show_all_includes_complete_or_empty_purchase_orders_for_selected_status(): void
    {
        $this->createPurchaseOrder('PO-COMPLETE-ACTIVE', 'stock', 'complete', 2);
        $this->createPurchaseOrder('PO-COMPLETE-EMPTY', 'stock', 'complete', 0);
        $this->createPurchaseOrder('PO-INCOMPLETE-ACTIVE', 'stock', 'incomplete', 4);

        $uniqueIds = $this->queryUniqueIds([
            'status' => 'complete',
            'show_all' => 1,
        ]);

        $this->assertSame([
            'PO-COMPLETE-ACTIVE',
            'PO-COMPLETE-EMPTY',
        ], $uniqueIds);
    }

    public function test_type_filter_still_applies_when_show_all_is_disabled(): void
    {
        $this->createPurchaseOrder('PO-STOCK-ACTIVE', 'stock', 'incomplete', 3);
        $this->createPurchaseOrder('PO-SAMPLE-ACTIVE', 'sample', 'incomplete', 3);
        $this->createPurchaseOrder('PO-SAMPLE-EMPTY', 'sample', 'incomplete', 0);

        $uniqueIds = $this->queryUniqueIds([
            'type' => 'sample',
        ]);

        $this->assertSame(['PO-SAMPLE-ACTIVE'], $uniqueIds);
    }

    public function test_html_builder_defaults_to_created_at_desc_order(): void
    {
        $options = app(PurchaseOrderDataTable::class)
            ->html()
            ->getOptions();

        $this->assertSame([[11, 'desc']], $options['order']);
    }

    private function queryUniqueIds(array $query): array
    {
        $request = Request::create('/purchase-orders', 'GET', $query);
        $this->app->instance('request', $request);

        return app(PurchaseOrderDataTable::class)
            ->query(new PoStock())
            ->orderBy('po_stocks.unique_id')
            ->pluck('unique_id')
            ->all();
    }

    private function createPurchaseOrder(
        string $uniqueId,
        string $type,
        string $status,
        float $remainingQty
    ): void {
        $supplierId = DB::table('suppliers')->insertGetId([
            'code' => $uniqueId . '-SUP',
            'supplier_name' => $uniqueId . ' Supplier',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $poStockId = DB::table('po_stocks')->insertGetId([
            'unique_id' => $uniqueId,
            'po_type' => $type,
            'supplier_id' => $supplierId,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('po_stock_product')->insert([
            'po_stock_id' => $poStockId,
            'product_id' => 1,
            'qty' => max($remainingQty, 1),
            'remaining_qty' => $remainingQty,
            'unit_id' => 2,
            'price' => 100,
            'purchase_cost' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
