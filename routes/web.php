<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountingAssetController;
use App\Http\Controllers\AccountingCostController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeStatementController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\InventoryInController;
use App\Http\Controllers\InventoryLostController;
use App\Http\Controllers\InventoryMutationController;
use App\Http\Controllers\InventoryOutController;
use App\Http\Controllers\InventorySampleHistoryController;
use App\Http\Controllers\InventoryStockController;
use App\Http\Controllers\InventoryStockHistoryController;
use App\Http\Controllers\JobEditAmountApprovalController;
use App\Http\Controllers\JobIncomeController;
use App\Http\Controllers\JobListController;
use App\Http\Controllers\JobOutstandingController;
use App\Http\Controllers\JobProductController;
use App\Http\Controllers\JobProfitController;
use App\Http\Controllers\JobStatementController;
use App\Http\Controllers\JobTeamController;
use App\Http\Controllers\MainCategoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PackagingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReturnStockController;
use App\Http\Controllers\SalesTargetController;
use App\Http\Controllers\SalesTargetMonthlyController;
use App\Http\Controllers\FreeSampleController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WarehouseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/cost-category', [DashboardController::class, 'getCostCategory'])->name('cost_category');
        Route::get('/country-origin', [DashboardController::class, 'getCountryOrigin'])->name('country_origin');
        Route::get('/inquiry-products', [DashboardController::class, 'getInquiryByProduct'])->name('inquiry_products');
        Route::get('/junk-inquiry', [DashboardController::class, 'getJunkInquiry'])->name('junk_inquiry');
        Route::get('/sales-inquiry', [DashboardController::class, 'getSalesInquiry'])->name('sales_inquiry');
        Route::get('/shipment-destination', [DashboardController::class, 'getShipmentDestination'])->name('shipment_destination');
        Route::get('/profit-country', [DashboardController::class, 'getProfitCountry'])->name('profit_country');
        Route::get('/target-achievement', [DashboardController::class, 'getTargetAchievement'])->name('target_achievement');
    });
});


// Modul Auth Permission and role
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::resource('permission', PermissionController::class);
    Route::resource('role', RoleController::class);
    Route::resource('sales-target', SalesTargetController::class);
    Route::resource('sales-target-monthly', SalesTargetMonthlyController::class)->except(['create', 'show', 'edit'])->names('sales_target_monthly');
});

Route::prefix('auth')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('authenticate', [AuthController::class, 'authenticate'])->name('auth.authenticate');
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Modul Account
Route::middleware(['auth'])->group(function () {
    Route::resource('account', UserController::class);
    Route::get('account/create/{id}/signature', [UserController::class, 'createSign'])->name('account.createSign');
    Route::get('account/edit/{id}/signature', [UserController::class, 'editSign'])->name('account.editSign');
    Route::put('account/store/{id}/signature', [UserController::class, 'uploadSign'])->name('account.uploadSign');
    Route::get('account/permission/{user}/edit', [UserController::class, 'editPermission'])->name('account.edit_permission');
    Route::put('account/permission/{user}/update', [UserController::class, 'updatePermission'])->name('account.update_permission');
});

Route::resources([
    'product/list'               => ProductController::class,
    'product/inventory-in'       => InventoryInController::class,
    'product/inventory-out'      => InventoryOutController::class,
    'product/inventory-lost'     => InventoryLostController::class,
    'product/inventory-stock'    => InventoryStockController::class,
    'product/inventory-mutation' => InventoryMutationController::class,
    'product/main-category'      => MainCategoryController::class,
    'product/sub-category'       => SubCategoryController::class,
    'product/brand'              => BrandController::class,
    'product/product-type'       => ProductTypeController::class,
    'product/specification'      => SpecificationController::class,
    'product/packaging'          => PackagingController::class,
    'product/return-stock'       => ReturnStockController::class,
], [
    'as'         => 'product',
    'middleware' => 'auth',
    // 'except' => ['create']
]);

Route::middleware('auth')->group(function () {
    Route::get(
        'product/inventory-stock/{inventoryStock}/history',
        [InventoryStockHistoryController::class, 'show']
    )->name('product.inventory_stock.history');

    Route::get(
        'product/inventory-stock/{inventoryStock}/sample-history',
        [InventorySampleHistoryController::class, 'show']
    )->name('product.inventory_stock.sample_history');
});

//session for return stock
Route::prefix('product')->name('product.return-stock.')->middleware('auth')->group(function () {
    Route::post('/return-stock/save-from-session', [ReturnStockController::class, 'saveFromSession'])->name('save-from-session');
    Route::post('/return-stock/clear-session', [ReturnStockController::class, 'clearSession'])->name('clear-session');
});

Route::post('api/fetchMainCategory', [ProductController::class, 'fetchMainCategory'])->name('product.fetchMainCategory');
Route::post('api/fetchMainCategory/product_type', [ProductTypeController::class, 'fetchMainCategory'])->name('product_type.fetchMainCategory');
Route::get('product/options', [ProductController::class, 'getProductOptions'])->name('options.product');
Route::get('inventory-mutation/product-stock-options', [InventoryMutationController::class, 'getProductStockOptions'])->name('options.inventory_mutation.product_stock');

Route::resource('free-samples', FreeSampleController::class)
    ->middleware('auth')
    ->names('free_sample')
    ->only(['index', 'store']);

Route::middleware(['auth'])->group(function () {
    // Modul Supplier
    Route::resource('supplier', SupplierController::class);
    Route::post('api/fetchMainCategory/supplier', [SupplierController::class, 'fetchMainCategory'])->name('supplier.fetchMainCategory');
    Route::get('api/supplier/options/{selectedId?}/{filterByMainCategory?}', [SupplierController::class, 'getSupplierOptions'])->name('options.supplier');
    Route::get('/suppliers/import-template', [SupplierController::class, 'importTemplate'])->name('supplier.import_template');
    Route::post('/suppliers/import', [SupplierController::class, 'import'])->name('supplier.import');
    Route::put('/suppliers/import-update', [SupplierController::class, 'importUpdate'])->name('supplierUpdate.import');
    Route::post('/suppliers/export', [SupplierController::class, 'export'])->name('supplier.export');


    // Modul Inquiry
    Route::resource('inquiry', InquiryController::class);
    Route::post('/inquiry/inquiry-product/store', [InquiryController::class, 'storeProduct'])->name('inquiry.inquiryProduct.store');
    Route::get('/inquiry/inquiry-product/import-template', [InquiryController::class, 'importTemplate'])->name('inquiry.inquiryProduct.import_template');
    Route::post('/inquiry/inquiry-product/import', [InquiryController::class, 'import'])->name('inquiry.inquiryProduct.import');
    Route::get('/inquiry/inquiry-product/{id}/data', [InquiryController::class, 'dataProduct'])->name('inquiry.inquiryProduct.data');
    Route::get('/inquiry/inquiry-product/{id}/edit', [InquiryController::class, 'editProduct'])->name('inquiry.inquiryProduct.edit');
    Route::put('/inquiry/inquiry-product/{id}/update', [InquiryController::class, 'updateProduct'])->name('inquiry.inquiryProduct.update');
    Route::delete('/inquiry/inquiry-product/{id}/delete', [InquiryController::class, 'destroyDataProduct'])->name('inquiry.inquiryProduct.destroy');


    // Modul Customer
    Route::resource('customer', CustomerController::class)->except('show');
    Route::post('customers/import', [CustomerController::class, 'import'])->name('customer.import');
    Route::get('customers/import-template', [CustomerController::class, 'importTemplate'])->name('customer.import_template');
    Route::put('customers/import-update', [CustomerController::class, 'importUpdate'])->name('customerUpdate.import');
    Route::post('customers/export', [CustomerController::class, 'export'])->name('customer.export');


    // Modul Job
    Route::resources([
        'job/list'        => JobListController::class,
        'job/profit'      => JobProfitController::class,
        'job/outstanding' => JobOutstandingController::class,
    ], [
        'as' => 'job',
    ]);

    #job import
    Route::get('/job/import-template', [JobListController::class, 'importTemplate'])->name('job.import_template');
    Route::post('/job/import', [JobListController::class, 'import'])->name('job.import');
    Route::post('/job/{job}/export-invoice', [JobListController::class, 'exportInvoice'])->name('job.export_invoice');

    #job team
    Route::resource('job.team', JobTeamController::class)->except(['create', 'show', 'edit']);
    Route::get('job/{job}/team/marketing-option/{selected?}', [JobTeamController::class, 'marketingOption'])->name('job.team.marketing_option');

    #job income
    Route::prefix('job-income')->name('job_income.')->group(function () {
        Route::get('/{job}', [JobIncomeController::class, 'index'])->name('index');
        Route::post('/{job}', [JobIncomeController::class, 'store'])->name('store');
        Route::post('/{jobIncome}/update', [JobIncomeController::class, 'update'])->name('update');
        Route::delete('/{jobIncome}', [JobIncomeController::class, 'destroy'])->name('destroy');
        Route::get('{job}/change-status-payment', [JobIncomeController::class, 'changeStatusPayment'])->name('change_status_payment');
    });

    // job product
    Route::get('job-product/{job}', [JobProductController::class, 'index'])->name('job_product.index');
    Route::post('job-product/{job}', [JobProductController::class, 'store'])->name('job_product.store');
    Route::get('job-product/{job_id}/{id}/edit-modal', [JobProductController::class, 'editModal'])->name('job_product.edit_modal');
    Route::put('job-product/{job}/{job_product_id}', [JobProductController::class, 'update'])->name('job_product.update');
    Route::delete('job-product/{job}/destroy/{id}', [JobProductController::class, 'destroy'])->name('job_product.destroy');
    // end of job product

    // job statement
    Route::prefix('job-statement')->name('job_statement.')->group(function () {
        Route::get('/{job}', [JobStatementController::class, 'index'])->name('index');
        Route::post('/{job}/convert-income', [JobStatementController::class, 'convertIncome'])->name('convert_income');
        Route::get('/{job}/change-status', [JobStatementController::class, 'changeStatus'])->name('change_status');
        // job stock
        Route::post('/store-job-stock/{job}', [JobStatementController::class, 'storeJobStock'])->name('store_job_stock');
        Route::put('/update-job-stock/{jobStatement}', [JobStatementController::class, 'updateJobStock'])->name('update_job_stock');
        Route::delete('/delete-job-stock/{jobStatement}', [JobStatementController::class, 'destroyJobStock'])->name('destroy_job_stock');
        // job PO stock
        Route::post('/{job}/store-job-po-stock', [JobStatementController::class, 'storeJobPoStock'])->name('store_job_po_stock');
        Route::put('/{jobPoStock}/update-job-po-stock', [JobStatementController::class, 'updateJobPoStock'])->name('update_job_po_stock');
        Route::delete('/{jobPoStock}/delete-job-po-stock', [JobStatementController::class, 'destroyJobPoStock'])->name('destroy_job_po_stock');
        // job commission
        Route::post('/{job}/store-job-commission', [JobStatementController::class, 'storeJobCommission'])->name('store_job_commission');
        Route::put('/{jobCommission}/update-job-commission', [JobStatementController::class, 'updateJobCommission'])->name('update_job_commission');
        Route::delete('/{jobCommission}/delete-job-commission', [JobStatementController::class, 'destroyJobCommission'])->name('destroy_job_commission');
    });

    Route::post('api/get-stock',[JobListController::class, 'getStock'])->name('jobStatement.get_stock');

    // Route::get('job/list/job-statement/{id}/stock', [JobListController::class, 'jobStatementStock'])
    //     ->name('job.list.statement.stock');
    // Route::get('job/list/job-statement/{id}/stock/edit', [JobListController::class, 'jobStatementEdit'])->name('job.list.statement.stock.edit');
    // Route::put('job/list/job-statement/{id}/stock/update', [JobListController::class, 'jobStatementUpdate'])->name('job.list.statement.stock.update');
    // Route::delete('job/list/job-statement/{id}/stock/delete', [JobListController::class, 'destroyJobStatement'])->name('job.list.statement.stock.delete');

    Route::get('api/fetchExpenseStock/{id}', [JobListController::class, 'getTotalExpenseStock'])->name('job.fetchExpenseStock');
    Route::get('api/fetchStock/{id}', [JobListController::class, 'fetchStock'])->name('job.fetchStock');
    // end of modul job

    Route::get('inventory-in/{inventoryIn}/history', [InventoryInController::class, 'history'])->name('inventory_in.history');

    // Job - Request Edit Amount
    Route::get('edit-amount-approval', [JobEditAmountApprovalController::class, 'index'])->name('job.edit_amount_approval.index');
    Route::put('edit-amount-approval/{editRequest}/change-status', [JobEditAmountApprovalController::class, 'changeStatus'])->name('edit_amount_approval.change_status');
});

// Modul Shipment
Route::resource('shipment', ShipmentController::class)->middleware('auth');

// Modul Accounting
Route::resources([
    'accounting/asset' => AccountingAssetController::class,
    'accounting/cost'  => AccountingCostController::class,
], [
    'as'         => 'accounting',
    'middleware' => 'auth',
]);


Route::prefix('accounting')->middleware('auth')->group(function () {
    Route::resource('purchase-orders', PurchaseOrderController::class)->names('purchase_orders');
    Route::get('asset/get/id-suggestion', [AccountingAssetController::class, 'getIdSuggestion'])->name('asset.get_id_suggestion');
    Route::get('po_stock/get-id-suggestion', [PurchaseOrderController::class, 'getIdSuggestion'])->name('po_stock.get_id_suggestion');
    Route::get('po_stock/get-po-products', [PurchaseOrderController::class, 'getPoProducts'])->name('po_stock.get_po_products');
    Route::get('po_stock/get-po-purchase-cost', [PurchaseOrderController::class, 'getPoProductPurchaseCost'])->name('po_stock.get_po_product_purchase_cost');
    Route::get('po_stock/get-po-options', [PurchaseOrderController::class, 'getPoOptions'])->name('po_stock.get_po_options');
    Route::put('po_stock/{poStock}/change-status', [PurchaseOrderController::class, 'changeStatus'])->name('po_stock.change_status');
    Route::get('po_stock/{poStock}/history', [PurchaseOrderController::class, 'history'])->name('po_stock.history');
    Route::get('po-stock/{poStock}/detail', [PurchaseOrderController::class, 'detail'])->name('po_stock.detail');
    Route::get('po-stock/{poStock}/export-excel', [PurchaseOrderController::class, 'exportExcel'])->name('po_stock.export_excel');
    Route::get('po-stock/{poStock}/export-pdf', [PurchaseOrderController::class, 'exportPdf'])->name('po_stock.export_pdf');

    Route::get('income-statement', [IncomeStatementController::class, 'index'])->name('accounting.income_statement.index');
    Route::get('income-statement/get-excel', [IncomeStatementController::class, 'getExcel'])->name('accounting.income_statement.get_excel');

    Route::resource('other-income', OtherIncomeController::class)->except('show', 'update')->names('other_income');
    Route::post('other-income/{otherIncome}/update', [OtherIncomeController::class, 'update'])->name('other_income.update');

    Route::get('/cost/import/template', [AccountingCostController::class, 'importTemplate'])->name('cost.import_template');
    Route::post('/cost/import', [AccountingCostController::class, 'import'])->name('cost.import');
});


// Modul Warehouse
Route::middleware(['auth'])->group(function () {
    Route::resource('warehouse', WarehouseController::class);
    Route::get('warehouse-options/{except?}/{selected?}', [WarehouseController::class, 'getWarehouseOptions'])->name('options.warehouse');
});

// Modul Vendor
Route::resource('vendors', VendorController::class)->middleware('auth');
Route::get('api/vendor/options/{selectedId?}', [VendorController::class, 'getVendorOptions'])->name('options.vendor');
Route::get('vendor/import-template', [VendorController::class, 'importTemplate'])->name('vendor.import_template');
Route::post('vendor/import', [VendorController::class, 'import'])->name('vendor.import');
Route::put('vendor/import-update', [VendorController::class, 'importUpdate'])->name('vendorUpdate.import');
Route::post('/vendor/export', [VendorController::class, 'export'])->name('vendor.export');


// Modul System
Route::middleware(['auth'])->group(function () {
    Route::get('system', [SystemController::class, 'index'])->name('system.index');
    Route::get('system/artisan/cache-clear', [SystemController::class, 'artisanCacheClear'])->name('system.cache_clear');
    Route::get('system/logs', [SystemController::class, 'log'])->name('system.log');
});
