<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class InventoryStockListTest extends TestCase
{
    use DatabaseTransactions;

    public function test_inventory_stock_list_renders_without_summary_links_and_keeps_filters(): void
    {
        $user = $this->createUser();
        $product = $this->createProductFixture('LIST-REG');
        $warehouse = $this->createWarehouse('List Warehouse Regular');

        $this->createInventoryStock([
            'inventory_type' => 'in',
            'product_id' => $product['id'],
            'warehouse_id' => $warehouse,
            'unit_id' => 2,
            'amount' => 3,
            'purchase_cost' => 45,
            'purchase_cost_per_unit' => 15,
        ]);

        $response = $this->actingAs($user)
            ->get(route('product.inventory-stock.index', [
                'warehouse' => $warehouse,
                'show_all' => 1,
            ]));

        $response->assertOk();
        $response->assertSee('Inventory Stock');
        $response->assertSee('Show All Data');
        $response->assertSee('List Warehouse Regular');
        $response->assertSee('3,0 Pcs');
        $response->assertSee('<option value="' . $warehouse . '"selected>List Warehouse Regular</option>', false);
        $response->assertSee('name="show_all"', false);
        $response->assertSee('checked', false);
    }

    public function test_inventory_sample_list_keeps_sample_scope_without_summary_links(): void
    {
        $user = $this->createUser();
        $sampleProduct = $this->createProductFixture('LIST-SMP');
        $regularProduct = $this->createProductFixture('LIST-OTH');
        $warehouse = $this->createWarehouse('List Warehouse Sample');

        $this->createInventoryStock([
            'inventory_type' => 'sample',
            'product_id' => $sampleProduct['id'],
            'warehouse_id' => $warehouse,
            'unit_id' => 2,
            'amount' => 2,
            'purchase_cost' => 30,
            'purchase_cost_per_unit' => 15,
        ]);

        $this->createInventoryStock([
            'inventory_type' => 'in',
            'product_id' => $regularProduct['id'],
            'warehouse_id' => $warehouse,
            'unit_id' => 2,
            'amount' => 6,
            'purchase_cost' => 90,
            'purchase_cost_per_unit' => 15,
        ]);

        $response = $this->actingAs($user)
            ->get(route('product.inventory-stock.index', [
                'type' => 'sample',
                'warehouse' => $warehouse,
                'show_all' => 1,
            ]));

        $response->assertOk();
        $response->assertSee('Inventory Sample');
        $response->assertSee('<input type="hidden" name="type" value="sample">', false);
        $response->assertSee('2,0 Pcs');
        $response->assertDontSee('6,0 Pcs');
    }

    public function test_inventory_stock_and_sample_rows_with_po_lineage_show_history_buttons(): void
    {
        $user = $this->createUser();
        $regularProduct = $this->createProductFixture('HIS-REG');
        $sampleProduct = $this->createProductFixture('HIS-SMP');
        $warehouse = $this->createWarehouse('History Warehouse');
        $regularPoStockId = $this->createPoStock(
            'PO-HISTORY-REG',
            $regularProduct['main_category_id'],
            $regularProduct['supplier_id']
        );
        $samplePoStockId = $this->createPoStock(
            'PO-HISTORY-SMP',
            $sampleProduct['main_category_id'],
            $sampleProduct['supplier_id']
        );

        $regularStockId = $this->createInventoryStock([
            'inventory_type' => 'in',
            'product_id' => $regularProduct['id'],
            'warehouse_id' => $warehouse,
            'po_stock_id' => $regularPoStockId,
            'unit_id' => 2,
            'amount' => 5,
            'purchase_cost' => 75,
            'purchase_cost_per_unit' => 15,
        ]);

        $sampleStockId = $this->createInventoryStock([
            'inventory_type' => 'sample',
            'product_id' => $sampleProduct['id'],
            'warehouse_id' => $warehouse,
            'po_stock_id' => $samplePoStockId,
            'unit_id' => 2,
            'amount' => 4,
            'purchase_cost' => 60,
            'purchase_cost_per_unit' => 15,
        ]);

        $regularResponse = $this->actingAs($user)
            ->get(route('product.inventory-stock.index', ['warehouse' => $warehouse]));

        $regularResponse->assertOk();
        $regularResponse->assertSee(route('product.inventory_stock.history', $regularStockId), false);
        $regularResponse->assertDontSee(route('product.inventory_stock.sample_history', $regularStockId), false);
        $regularResponse->assertSee(route('po_stock.detail', $regularPoStockId), false);

        $sampleResponse = $this->actingAs($user)
            ->get(route('product.inventory-stock.index', [
                'type' => 'sample',
                'warehouse' => $warehouse,
            ]));

        $sampleResponse->assertOk();
        $sampleResponse->assertSee(route('product.inventory_stock.sample_history', $sampleStockId), false);
        $sampleResponse->assertDontSee(route('product.inventory_stock.history', $sampleStockId), false);
        $sampleResponse->assertSee(route('po_stock.detail', $samplePoStockId), false);
    }

    private function createUser(): User
    {
        $suffix = Str::lower(Str::random(8));

        $userId = DB::table('users')->insertGetId([
            'name' => 'Test User ' . $suffix,
            'position' => 'QA',
            'role_id' => '1',
            'username' => 'user_' . $suffix,
            'email' => $suffix . '@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($userId);
    }

    private function createProductFixture(string $skuPrefix): array
    {
        static $categoryCodeIndex = 0;
        static $skuSequence = 900000000;
        $categoryCode = chr(65 + $categoryCodeIndex);
        $categoryCodeIndex++;

        $mainCategoryId = DB::table('main_categories')->insertGetId([
            'code' => $categoryCode,
            'main_category_name' => $skuPrefix . ' Main Category',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subCategoryId = DB::table('sub_categories')->insertGetId([
            'main_category_id' => $mainCategoryId,
            'code' => Str::upper(Str::random(1)),
            'category_name' => $skuPrefix . ' Sub Category',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $brandId = DB::table('brands')->insertGetId([
            'main_category_id' => $mainCategoryId,
            'code' => Str::upper(Str::random(1)),
            'brand_name' => $skuPrefix . ' Brand',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productTypeId = DB::table('product_types')->insertGetId([
            'main_category_id' => $mainCategoryId,
            'code' => Str::upper(Str::random(3)),
            'product_type_name' => $skuPrefix . ' Type',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $packagingId = DB::table('packagings')->insertGetId([
            'main_category_id' => $mainCategoryId,
            'code' => Str::upper(Str::random(3)),
            'packaging_name' => $skuPrefix . ' Packaging',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $specificationId = DB::table('specifications')->insertGetId([
            'main_category_id' => $mainCategoryId,
            'code' => Str::upper(Str::random(3)),
            'specification_name' => $skuPrefix . ' Specification',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $supplierId = DB::table('suppliers')->insertGetId([
            'main_category_id' => $mainCategoryId,
            'code' => $skuPrefix . '-SUP',
            'supplier_name' => $skuPrefix . ' Supplier',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->ensureUnitExists(2, 'Pcs');

        $sku = $skuSequence++;

        $productId = DB::table('products')->insertGetId([
            'sku' => $sku,
            'main_category_id' => $mainCategoryId,
            'sub_category_id' => $subCategoryId,
            'brand_id' => $brandId,
            'product_type_id' => $productTypeId,
            'packaging_id' => $packagingId,
            'specification_id' => $specificationId,
            'supplier_id' => $supplierId,
            'unit_id' => 2,
            'qty' => 0,
            'harga_rata_rata' => 0,
            'harga_tertinggi' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'id' => $productId,
            'sku' => (string) $sku,
            'main_category_id' => $mainCategoryId,
            'supplier_id' => $supplierId,
        ];
    }

    private function createWarehouse(string $warehouseName): int
    {
        return DB::table('warehouses')->insertGetId([
            'warehouse_name' => $warehouseName,
            'status' => 'Active',
            'address' => $warehouseName . ' Address',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPoStock(string $uniqueId, int $mainCategoryId, int $supplierId): int
    {
        return DB::table('po_stocks')->insertGetId([
            'po_type' => 'stock',
            'unique_id' => $uniqueId,
            'main_category_id' => $mainCategoryId,
            'supplier_id' => $supplierId,
            'shipping_cost' => null,
            'note' => 'Test PO stock fixture',
            'additional_expenses' => 0,
            'total' => 0,
            'status' => 'incomplete',
            'arrived_warehouse' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createInventoryStock(array $overrides): int
    {
        static $inventoryId = 100000;

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

    private function ensureUnitExists(int $id, string $unitName): void
    {
        $existing = DB::table('units')->where('id', $id)->first();

        if ($existing) {
            return;
        }

        DB::table('units')->insert([
            'id' => $id,
            'unit_name' => $unitName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
