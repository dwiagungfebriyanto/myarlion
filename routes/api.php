<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryStockController;
use App\Http\Controllers\JobEditAmountApprovalController;
use App\Http\Controllers\JobProductController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DashboardAggregateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['as' => 'api.'], function () {
    # Dashboard
    Route::get('dashboard/target-achievement-monthly', [DashboardController::class, 'getTargetAchievementMonthly'])->name('dashboard.target_achievement_monthly');
    Route::post('dashboard/customer-report', [DashboardController::class, 'getCustomerReport'])->name('dashboard.customer_report');
    Route::post('dashboard/inquiry-by-channel', [DashboardController::class, 'getInquiryByChannel'])->name('dashboard.inquiry_by_channel');


    Route::post('inventory-stock/ready-stock-options/{selected?}', [InventoryStockController::class, 'getReadyStockOptions'])->name('inventory_stock.ready_stock_options');
    Route::get('job-product/product-options/{selected?}', [JobProductController::class, 'getProductOptions'])->name('job_product.product_options');
    Route::get('job-product/product-detail/{product}', [JobProductController::class, 'getProductDetail'])->name('job_product.product_detail');
    Route::get('supplier/options', [SupplierController::class, 'getApiOptions'])->name('supplier.options');
    
    Route::post('jobs/{job}/request-edit-amount', [JobEditAmountApprovalController::class, 'store'])->name('edit_amount_approval.store');
});

// Endpoint agregat dashboard (token based)
Route::get('v1/dashboard/full', DashboardAggregateController::class)->name('api.v1.dashboard.full');
