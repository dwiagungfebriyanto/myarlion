<?php

namespace Tests\Feature;

use App\Http\Controllers\FreeSampleController;
use App\Http\Requests\Sample\StoreFreeSampleRequest;
use App\Models\FreeSample;
use App\Models\InventoryStock;
use App\Models\User;
use App\Services\InventoryStockBalanceService;
use App\Services\InventoryStockHistoryService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use RuntimeException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\TestCase;

class FreeSampleWorkflowTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('activitylog.enabled', false);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('telp')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('name');
            $table->string('customer_category')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('country_code')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('channel_id')->nullable();
            $table->unsignedBigInteger('website_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->string('platform')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->unsignedBigInteger('unit_id')->default(2);
            $table->double('qty')->default(0);
            $table->timestamps();
        });

        Schema::create('po_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_category_id')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('po_type')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->double('shipping_cost')->nullable();
            $table->text('note')->nullable();
            $table->double('additional_expenses')->default(0);
            $table->double('total')->default(0);
            $table->string('status')->default('incomplete');
            $table->boolean('arrived_warehouse')->default(false);
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name');
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_name');
            $table->timestamps();
        });

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

        Schema::create('free_samples', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_stock_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('inquiry_id')->nullable();
            $table->double('quantity');
            $table->double('purchase_cost')->default(0);
            $table->double('purchase_cost_per_unit')->default(0);
            $table->date('date');
            $table->timestamps();
        });

        Schema::create('inventory_ins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_stock_id')->nullable();
            $table->unsignedBigInteger('destination_stock_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->double('amount')->nullable();
            $table->double('weight')->nullable();
            $table->double('purchase_cost')->default(0);
            $table->double('purchase_cost_per_unit')->default(0);
            $table->dateTime('datetime')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_outs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_stock_id')->nullable();
            $table->unsignedBigInteger('destination_stock_id')->nullable();
            $table->unsignedBigInteger('inventory_in_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('original_warehouse_id')->nullable();
            $table->double('amount')->nullable();
            $table->double('weight')->nullable();
            $table->double('purchase_cost')->default(0);
            $table->double('purchase_cost_per_unit')->default(0);
            $table->dateTime('datetime')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_mutations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_stock_id')->nullable();
            $table->unsignedBigInteger('destination_stock_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('original_product_id')->nullable();
            $table->unsignedBigInteger('new_product_id')->nullable();
            $table->double('qty_mutation')->default(0);
            $table->double('qty')->default(0);
            $table->double('price')->default(0);
            $table->double('source_purchase_cost')->default(0);
            $table->double('source_purchase_cost_per_unit')->default(0);
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->timestamps();
        });

        Schema::create('job_statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('inventory_stock_id')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->double('quantity')->default(0);
            $table->double('stock_cost')->default(0);
            $table->double('stock_cost_per_unit')->default(0);
            $table->timestamps();
        });

        Schema::create('inventory_losts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_stock_id')->nullable();
            $table->double('amount')->nullable();
            $table->double('weight')->nullable();
            $table->double('purchase_cost')->default(0);
            $table->double('purchase_cost_per_unit')->default(0);
            $table->dateTime('datetime')->nullable();
            $table->timestamps();
        });

        Schema::create('return_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_stock_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->double('qty')->default(0);
            $table->double('stock_cost')->default(0);
            $table->double('stock_cost_per_unit')->default(0);
            $table->timestamps();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_free_sample_can_be_stored_from_stock_inventory_and_customer_follows_inquiry(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Customer One', 'C001');
        $otherCustomerId = $this->createCustomer('Customer Two', 'C002');
        $inquiryId = $this->createInquiry([
            'name' => 'Customer One',
            'customer_id' => $customerId,
            'job_id' => null,
        ]);
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 10, 100);

        $request = $this->makeStoreRequest([
            'inventory_stock_id' => $stockId,
            'recipient_type' => 'inquiry',
            'inquiry' => $inquiryId,
            'customer' => $otherCustomerId,
            'quantity' => 3,
            'date' => '2026-05-13',
        ], $user);

        app(FreeSampleController::class)->store($request, app(InventoryStockBalanceService::class));

        $freeSample = FreeSample::query()->firstOrFail();
        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame($customerId, (int) $freeSample->customer_id);
        $this->assertSame($inquiryId, (int) $freeSample->inquiry_id);
        $this->assertSame(3.0, (float) $freeSample->quantity);
        $this->assertSame(30.0, (float) $freeSample->purchase_cost);
        $this->assertSame(10.0, (float) $freeSample->purchase_cost_per_unit);
        $this->assertSame(7.0, (float) $stock->amount);
        $this->assertSame(100.0, (float) $stock->purchase_cost);
        $this->assertSame(10.0, (float) $stock->purchase_cost_per_unit);
        $this->assertSame(7.0, (float) DB::table('products')->where('id', $productId)->value('qty'));
    }

    public function test_free_sample_persists_source_unit_cost_before_subtraction(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Unit Cost Customer', 'C009');
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 10, 760);

        $request = $this->makeStoreRequest([
            'inventory_stock_id' => $stockId,
            'recipient_type' => 'customer',
            'customer' => $customerId,
            'quantity' => 3,
            'date' => '2026-05-13',
        ], $user);

        app(FreeSampleController::class)->store($request, app(InventoryStockBalanceService::class));

        $freeSample = FreeSample::query()->firstOrFail();
        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame(3.0, (float) $freeSample->quantity);
        $this->assertSame(228.0, (float) $freeSample->purchase_cost);
        $this->assertSame(76.0, (float) $freeSample->purchase_cost_per_unit);
        $this->assertSame(7.0, (float) $stock->amount);
        $this->assertSame(760.0, (float) $stock->purchase_cost);
        $this->assertSame(76.0, (float) $stock->purchase_cost_per_unit);
    }

    public function test_free_sample_can_be_stored_from_sample_inventory(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Sample Customer', 'C003');
        $inquiryId = $this->createInquiry([
            'name' => 'Sample Customer',
            'customer_id' => $customerId,
            'job_id' => null,
        ]);
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'sample', 4, 40);

        $request = $this->makeStoreRequest([
            'inventory_stock_id' => $stockId,
            'recipient_type' => 'inquiry',
            'inquiry' => $inquiryId,
            'quantity' => 1,
            'date' => '2026-05-13',
        ], $user);

        app(FreeSampleController::class)->store($request, app(InventoryStockBalanceService::class));

        $freeSample = FreeSample::query()->firstOrFail();
        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame($inquiryId, (int) $freeSample->inquiry_id);
        $this->assertSame(3.0, (float) $stock->amount);
        $this->assertSame(40.0, (float) $stock->purchase_cost);
    }

    public function test_free_sample_can_be_stored_for_inquiry_without_customer_id(): void
    {
        $user = $this->createUser();
        $inquiryId = $this->createInquiry([
            'name' => 'New Prospect Inquiry',
            'customer_id' => null,
            'job_id' => null,
        ]);
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);

        $request = $this->makeStoreRequest([
            'inventory_stock_id' => $stockId,
            'recipient_type' => 'inquiry',
            'inquiry' => $inquiryId,
            'quantity' => 2,
            'date' => '2026-05-13',
        ], $user);

        app(FreeSampleController::class)->store($request, app(InventoryStockBalanceService::class));

        $freeSample = FreeSample::query()->firstOrFail();
        $stock = InventoryStock::findOrFail($stockId);

        $this->assertNull($freeSample->customer_id);
        $this->assertSame($inquiryId, (int) $freeSample->inquiry_id);
        $this->assertSame('New Prospect Inquiry', $freeSample->recipientName());
        $this->assertSame(2.0, (float) $freeSample->quantity);
        $this->assertSame(20.0, (float) $freeSample->purchase_cost);
        $this->assertSame(3.0, (float) $stock->amount);
        $this->assertSame(50.0, (float) $stock->purchase_cost);
        $this->assertSame(3.0, (float) DB::table('products')->where('id', $productId)->value('qty'));
    }

    public function test_free_sample_can_be_stored_directly_for_customer(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Direct Customer', 'C004');
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 6, 60);

        $request = $this->makeStoreRequest([
            'inventory_stock_id' => $stockId,
            'recipient_type' => 'customer',
            'customer' => $customerId,
            'quantity' => 2,
            'date' => '2026-05-13',
        ], $user);

        app(FreeSampleController::class)->store($request, app(InventoryStockBalanceService::class));

        $freeSample = FreeSample::query()->firstOrFail();
        $stock = InventoryStock::findOrFail($stockId);

        $this->assertSame($customerId, (int) $freeSample->customer_id);
        $this->assertNull($freeSample->inquiry_id);
        $this->assertSame(2.0, (float) $freeSample->quantity);
        $this->assertSame(20.0, (float) $freeSample->purchase_cost);
        $this->assertSame(4.0, (float) $stock->amount);
        $this->assertSame(60.0, (float) $stock->purchase_cost);
        $this->assertSame(4.0, (float) DB::table('products')->where('id', $productId)->value('qty'));
    }

    public function test_store_route_sets_success_session_for_inquiry_without_customer(): void
    {
        $user = $this->createUser();
        $inquiryId = $this->createInquiry([
            'name' => 'Route Prospect Inquiry',
            'customer_id' => null,
            'job_id' => null,
        ]);
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'inquiry',
                'inquiry' => $inquiryId,
                'quantity' => 2,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHas('success', 'Free Sample stored successfully.');
        $this->assertDatabaseHas('free_samples', [
            'inventory_stock_id' => $stockId,
            'inquiry_id' => $inquiryId,
            'customer_id' => null,
        ]);
    }

    public function test_store_route_accepts_inquiry_mode_even_if_customer_payload_is_empty(): void
    {
        $user = $this->createUser();
        $inquiryId = $this->createInquiry([
            'name' => 'Browser Payload Inquiry',
            'customer_id' => null,
            'job_id' => null,
        ]);
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'inquiry',
                'inquiry' => $inquiryId,
                'customer' => '',
                'quantity' => 2,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Free Sample stored successfully.');
        $this->assertDatabaseHas('free_samples', [
            'inventory_stock_id' => $stockId,
            'inquiry_id' => $inquiryId,
            'customer_id' => null,
        ]);
    }

    public function test_store_route_sets_success_session_for_customer_recipient(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Route Customer', 'C005');
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 6, 60);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'customer',
                'customer' => $customerId,
                'quantity' => 2,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHas('success', 'Free Sample stored successfully.');
        $this->assertDatabaseHas('free_samples', [
            'inventory_stock_id' => $stockId,
            'inquiry_id' => null,
            'customer_id' => $customerId,
        ]);
    }

    public function test_free_sample_request_accepts_inquiry_without_customer(): void
    {
        $user = $this->createUser();
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);
        $inquiryId = $this->createInquiry([
            'name' => 'Legacy Inquiry',
            'customer_id' => null,
            'job_id' => null,
        ]);

        $request = $this->makeStoreRequest([
            'inventory_stock_id' => $stockId,
            'recipient_type' => 'inquiry',
            'inquiry' => $inquiryId,
            'quantity' => 1,
            'date' => '2026-05-13',
        ], $user, false);

        $request->validateResolved();

        $this->assertSame($inquiryId, (int) $request->input('inquiry'));
    }

    public function test_store_route_rejects_inquiry_mode_without_inquiry(): void
    {
        $user = $this->createUser();
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'inquiry',
                'quantity' => 1,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHasErrors(['inquiry']);
    }

    public function test_free_sample_request_rejects_inquiry_that_already_has_job(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Mapped Customer', 'C004');
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);
        $inquiryId = $this->createInquiry([
            'name' => 'Mapped Customer',
            'customer_id' => $customerId,
            'job_id' => 99,
        ]);

        $request = $this->makeStoreRequest([
            'inventory_stock_id' => $stockId,
            'recipient_type' => 'inquiry',
            'inquiry' => $inquiryId,
            'quantity' => 1,
            'date' => '2026-05-13',
        ], $user, false);

        try {
            $request->validateResolved();
            $this->fail('Validation should fail for inquiry with existing job.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('inquiry', $exception->errors());
        }
    }

    public function test_free_sample_request_rejects_customer_mode_without_customer(): void
    {
        $user = $this->createUser();
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);

        $request = $this->makeStoreRequest([
            'inventory_stock_id' => $stockId,
            'recipient_type' => 'customer',
            'quantity' => 1,
            'date' => '2026-05-13',
        ], $user, false);

        try {
            $request->validateResolved();
            $this->fail('Validation should fail for customer mode without customer.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('customer', $exception->errors());
        }
    }

    public function test_store_route_rejects_customer_mode_without_customer(): void
    {
        $user = $this->createUser();
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'customer',
                'quantity' => 1,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHasErrors(['customer']);
    }

    public function test_store_route_rejects_inquiry_that_already_has_job(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Job Inquiry Customer', 'C006');
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);
        $inquiryId = $this->createInquiry([
            'name' => 'Job Inquiry Customer',
            'customer_id' => $customerId,
            'job_id' => 99,
        ]);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'inquiry',
                'inquiry' => $inquiryId,
                'quantity' => 1,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHasErrors(['inquiry']);
    }

    public function test_store_route_rejects_quantity_greater_than_available_stock(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Qty Customer', 'C007');
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'customer',
                'customer' => $customerId,
                'quantity' => 99,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHasErrors(['quantity']);
    }

    public function test_store_route_returns_clear_error_when_database_still_requires_customer_id(): void
    {
        $user = $this->createUser();
        $inquiryId = $this->createInquiry([
            'name' => 'Nullable Migration Inquiry',
            'customer_id' => null,
            'job_id' => null,
        ]);
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);
        $inventoryStock = InventoryStock::findOrFail($stockId);

        $serviceMock = \Mockery::mock(InventoryStockBalanceService::class);
        $serviceMock->shouldReceive('lockStock')->once()->with($stockId)->andReturn($inventoryStock);
        $serviceMock->shouldReceive('subtractFromStock')->once()->andThrow(
            new QueryException(
                'sqlite',
                'insert into "free_samples"',
                [],
                new Exception("SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'customer_id' cannot be null")
            )
        );
        $this->app->instance(InventoryStockBalanceService::class, $serviceMock);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'inquiry',
                'inquiry' => $inquiryId,
                'quantity' => 2,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHas(
            'error',
            'Free Sample gagal disimpan karena database belum mendukung inquiry tanpa customer. Jalankan migration yang membuat `free_samples.customer_id` nullable.'
        );
        $response->assertSessionHasInput('recipient_type', 'inquiry');
        $response->assertSessionHasInput('inquiry', $inquiryId);
    }

    public function test_store_route_returns_generic_error_for_unexpected_failure(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Runtime Customer', 'C008');
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 5, 50);

        $serviceMock = \Mockery::mock(InventoryStockBalanceService::class);
        $serviceMock->shouldReceive('lockStock')->once()->with($stockId)->andThrow(new RuntimeException('boom'));
        $this->app->instance(InventoryStockBalanceService::class, $serviceMock);

        $response = $this->actingAs($user)
            ->from('/free-samples')
            ->post(route('free_sample.store'), [
                'warehouse' => 1,
                'supplier' => 1,
                'inventory_stock_id' => $stockId,
                'recipient_type' => 'customer',
                'customer' => $customerId,
                'quantity' => 1,
                'date' => '2026-05-13',
            ]);

        $response->assertRedirect('/free-samples');
        $response->assertSessionHas(
            'error',
            'Free Sample gagal disimpan. Silakan cek data recipient, inventory, dan coba lagi.'
        );
        $response->assertSessionHasInput('recipient_type', 'customer');
        $response->assertSessionHasInput('customer', $customerId);
    }

    public function test_stock_history_includes_free_sample_event_for_stock_bucket(): void
    {
        $customerId = $this->createCustomer('History Customer', 'C005');
        $inquiryId = $this->createInquiry([
            'name' => 'History Customer',
            'customer_id' => $customerId,
            'job_id' => null,
        ]);
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 8, 80);

        DB::table('free_samples')->insert([
            'inventory_stock_id' => $stockId,
            'customer_id' => $customerId,
            'inquiry_id' => $inquiryId,
            'quantity' => 2,
            'purchase_cost' => 20,
            'purchase_cost_per_unit' => 10,
            'date' => '2026-05-13',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $history = app(InventoryStockHistoryService::class)->build(InventoryStock::findOrFail($stockId));

        $this->assertTrue(collect($history['events'])->contains(function (array $event) {
            return $event['type'] === 'free_sample'
                && $event['description'] === 'Free Sample ke Customer History Customer';
        }));
    }

    public function test_stock_history_includes_free_sample_event_for_direct_customer_recipient(): void
    {
        $customerId = $this->createCustomer('Direct History Customer', 'C005');
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 8, 80);

        DB::table('free_samples')->insert([
            'inventory_stock_id' => $stockId,
            'customer_id' => $customerId,
            'inquiry_id' => null,
            'quantity' => 2,
            'purchase_cost' => 20,
            'purchase_cost_per_unit' => 10,
            'date' => '2026-05-13',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $history = app(InventoryStockHistoryService::class)->build(InventoryStock::findOrFail($stockId));

        $this->assertTrue(collect($history['events'])->contains(function (array $event) {
            return $event['type'] === 'free_sample'
                && $event['description'] === 'Free Sample ke Customer Direct History Customer';
        }));
    }

    public function test_stock_history_includes_free_sample_event_for_inquiry_without_customer(): void
    {
        $inquiryId = $this->createInquiry([
            'name' => 'Prospect History Inquiry',
            'customer_id' => null,
            'job_id' => null,
        ]);
        $productId = $this->createProduct();
        $stockId = $this->createInventoryStock($productId, 'stock', 8, 80);

        DB::table('free_samples')->insert([
            'inventory_stock_id' => $stockId,
            'customer_id' => null,
            'inquiry_id' => $inquiryId,
            'quantity' => 2,
            'purchase_cost' => 20,
            'purchase_cost_per_unit' => 10,
            'date' => '2026-05-13',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $history = app(InventoryStockHistoryService::class)->build(InventoryStock::findOrFail($stockId));

        $this->assertTrue(collect($history['events'])->contains(function (array $event) {
            return $event['type'] === 'free_sample'
                && $event['description'] === 'Free Sample ke Customer Prospect History Inquiry';
        }));
    }

    private function makeStoreRequest(array $data, User $user, bool $validate = true): StoreFreeSampleRequest
    {
        $this->actingAs($user);

        $request = StoreFreeSampleRequest::createFromBase(
            SymfonyRequest::create('/free-samples', 'POST', $data)
        );

        $request->setContainer($this->app);
        $request->setRedirector($this->app['redirect']);
        $request->setUserResolver(fn () => $user);

        if ($validate) {
            $request->validateResolved();
        }

        return $request;
    }

    private function createUser(): User
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permissionId = DB::table('permissions')->insertGetId([
            'name' => 'add free sample',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('model_has_permissions')->insert([
            'permission_id' => $permissionId,
            'model_type' => User::class,
            'model_id' => $userId,
        ]);

        return User::findOrFail($userId);
    }

    private function createCustomer(string $name, string $code): int
    {
        return DB::table('customers')->insertGetId([
            'name' => $name,
            'code' => $code,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createInquiry(array $overrides): int
    {
        return DB::table('inquiries')->insertGetId(array_merge([
            'date' => '2026-05-13',
            'name' => 'Inquiry',
            'customer_category' => 'exis_customer',
            'customer_id' => null,
            'job_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    private function createProduct(): int
    {
        DB::table('units')->insert([
            'id' => 2,
            'unit_name' => 'Pcs',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('warehouses')->insert([
            'id' => 1,
            'warehouse_name' => 'Main Warehouse',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('po_stocks')->insert([
            'id' => 1,
            'po_type' => 'stock',
            'unique_id' => 'PO-001',
            'main_category_id' => 1,
            'supplier_id' => 1,
            'shipping_cost' => null,
            'note' => 'Test PO stock fixture',
            'additional_expenses' => 0,
            'total' => 0,
            'status' => 'incomplete',
            'arrived_warehouse' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('products')->insertGetId([
            'sku' => 'SKU-001',
            'unit_id' => 2,
            'qty' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createInventoryStock(int $productId, string $bucket, float $amount, float $cost): int
    {
        return DB::table('inventory_stocks')->insertGetId([
            'inventory_id' => 100,
            'inventory_type' => $bucket === 'sample' ? 'sample' : 'in',
            'product_id' => $productId,
            'po_stock_id' => 1,
            'stock_bucket' => $bucket,
            'warehouse_id' => 1,
            'unit_id' => 2,
            'amount' => $amount,
            'weight' => 0,
            'purchase_cost' => $cost,
            'purchase_cost_per_unit' => $amount > 0 ? $cost / $amount : 0,
            'history_overall_qty' => $amount,
            'history_overall_avg' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
