<?php

namespace App\Http\Controllers;

use App\DataTables\PurchaseOrderDataTable;
use App\Exports\PoStockDetailExport;
use App\Http\Requests\PurchaseOrder\FilterPoRequest;
use App\Http\Requests\PurchaseOrder\StorePoRequest;
use App\Http\Requests\UpdatePoStockRequest;
use App\Models\Main_Category;
use App\Models\PoStock;
use App\Models\PoStockProduct;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\PoStockHistoryService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf as PdfMpdf;

class PurchaseOrderController extends Controller
{
    protected $poTypes = [
        [
            'value' => 'stock',
            'name'  => 'Stock'
        ],
        [
            'value' => 'sample',
            'name'  => 'Sample'
        ],
    ];

    protected $statuses = [
        [
            'value' => 'complete',
            'name'  => 'Complete'
        ],
        [
            'value' => 'incomplete',
            'name'  => 'Incomplete'
        ],
    ];


    /**
     * Display a listing of the resource.
     */
    public function index(FilterPoRequest $request, PurchaseOrderDataTable $dataTable)
    {
        $data['mainCategories'] = Main_Category::all();
        $data['pageTitle']      = 'PO Stock - Sample';
        $data['poTypes']        = $this->poTypes;
        $data['statuses']       = $this->statuses;

        return $dataTable->render('pages.po_stock.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePoRequest $request)
    {
        $request->validated();

        $additionalExpenses = $request->additional_expenses;
        $shippingCost       = $request->shipping_cost;

        DB::transaction(function () use ($request, $shippingCost, $additionalExpenses) {
            $poStock                      = new PoStock;
            $poStock->unique_id           = $request->unique_id ?? $this->generateUniqueId($request->main_category, $request->supplier);
            $poStock->po_type             = $request->po_type;
            $poStock->main_category_id    = $request->main_category;
            $poStock->supplier_id         = $request->supplier;
            $poStock->shipping_cost       = $shippingCost;
            $poStock->additional_expenses = $additionalExpenses;
            $poStock->total               = $request->total;
            $poStock->note                = $request->note;
            $poStock->save();

            foreach ($request->product_id as $product_id) {
                $qty = $request->quantity[$product_id];
                $price = cleanCurrencyFormat($request->price[$product_id]);
                $purchaseCost = $this->calculatePurchaseCost($request->total, $shippingCost, $additionalExpenses, $qty, $price);

                $poStockProduct                = new PoStockProduct;
                $poStockProduct->po_stock_id   = $poStock->id;
                $poStockProduct->product_id    = $product_id;
                $poStockProduct->qty           = $qty;
                $poStockProduct->remaining_qty = $qty;
                $poStockProduct->unit_id       = $request->unit[$product_id];
                $poStockProduct->price         = $price;
                $poStockProduct->purchase_cost = $purchaseCost;
                $poStockProduct->save();
            }
        });
    }

    public function detail(PoStock $poStock)
    {
        $data['pageTitle'] = 'PO ' . ucfirst($poStock->po_type) . ' Detail';
        $data['poStock']   = $poStock;

        return view('pages.po_stock.detail', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(PoStock $purchase_order): JsonResponse
    {
        $data['poStock']  = $purchase_order;
        $data['supplier'] = $purchase_order->supplier;

        return response()->json($data);
    }

    public function history(PoStock $poStock, PoStockHistoryService $historyService): View
    {
        $poStock->load([
            'supplier',
            'poStockDetail.product',
            'poStockDetail.unit',
        ]);

        $history = $historyService->build($poStock);

        $data['pageTitle'] = 'PO ' . ucfirst($poStock->po_type) . ' History';
        $data['poStock'] = $poStock;
        $data['itemCount'] = $history['item_count'];
        $data['productHistories'] = $history['product_histories'];

        return view('pages.po_stock.history', $data);
    }

    /**
     * Export PO Stock detail using XLSX template.
     */
    public function exportExcel(PoStock $poStock)
    {
        $fileName = 'PO_' . str_replace(['/', ' '], '-', $poStock->unique_id) . '.xlsx';
        return Excel::download(new PoStockDetailExport($poStock->id), $fileName);
    }

    /**
     * Export PO Stock detail to PDF.
     * Optimized approach: Direct HTML to PDF (fast, no Excel conversion)
     */
    public function exportPdf(PoStock $poStock)
    {
        // Increase execution time for PDF generation
        set_time_limit(180);
        ini_set('memory_limit', '512M');

        // Load PO Stock with all relationships
        $poStock->load([
            'supplier', 
            'mainCategory', 
            'poStockDetail.product', 
            'poStockDetail.unit'
        ]);

        // Format date in Indonesian
        try {
            $formattedDate = Carbon::now()->locale('id')->translatedFormat('j F Y');
        } catch (\Throwable $e) {
            $months = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            $d = (int) date('j');
            $m = (int) date('n');
            $y = (int) date('Y');
            $formattedDate = $d . ' ' . ($months[$m] ?? date('F')) . ' ' . $y;
        }

        // Prepare data for view
        $data = [
            'poStock' => $poStock,
            'formattedDate' => $formattedDate,
        ];

        try {
            // Configure mPDF
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_header' => 5,
                'margin_footer' => 5,
                'default_font' => 'Arial',
            ]);

            // Render view to HTML
            $html = view('exports.pdf.po-stock-detail', $data)->render();
            
            // Write HTML to PDF
            $mpdf->WriteHTML($html);

            // Generate filename
            $fileName = 'PO_' . str_replace(['/', ' '], '-', $poStock->unique_id) . '_' . date('Ymd') . '.pdf';

            // Output PDF for download
            return $mpdf->Output($fileName, 'D');
            
        } catch (\Exception $e) {
            return back()->with('danger', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PoStock $purchase_order): View|RedirectResponse
    {
        $purchase_order->load([
            'supplier',
            'mainCategory',
            'poStockDetail.product.unit',
            'poStockDetail.unit',
        ]);

        if (!$this->isPurchaseOrderEditable($purchase_order)) {
            return $this->editBlockedResponse();
        }

        $currentProductIDs = $purchase_order->poStockDetail()->pluck('product_id')->toArray();
        $filter = [
            'main_category_id' => $purchase_order->main_category_id,
            'supplier_id'      => $purchase_order->supplier_id,
        ];

        $selectedProductIDs = collect(old('product_id', $currentProductIDs))
            ->filter(fn ($id) => !is_null($id) && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $productController = new ProductController();
        $productOptions = $productController->getProductOptions($selectedProductIDs, $filter);
        $productOptions = str_replace(
            '<option value="" disabled selected>-- Select Product SKU --</option>',
            '',
            $productOptions
        );

        $data['pageTitle']           = 'Edit PO Stock - Sample';
        $data['actionUrl']           = route('purchase_orders.update', $purchase_order);
        $data['poStock']             = $purchase_order;
        $data['statuses']            = $this->statuses;
        $data['productOptions']      = $productOptions;
        $data['selectedProductIDs']  = $selectedProductIDs;
        $data['detailsByProductId']  = $purchase_order->poStockDetail->keyBy('product_id');
        $data['productsById']        = Product::where($filter)->with('unit')->get()->keyBy('id');

        return view('pages.po_stock.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePoStockRequest $request, PoStock $purchase_order): RedirectResponse
    {
        $purchase_order->loadMissing('poStockDetail');

        if (!$this->isPurchaseOrderEditable($purchase_order)) {
            return $this->editBlockedResponse();
        }

        $request->validated();

        $additionalExpenses = $request->additional_expenses ?? 0;
        $shippingCost       = $request->shipping_cost ?? 0;

        DB::transaction(function () use ($purchase_order, $request, $shippingCost, $additionalExpenses) {
            $poStock = $purchase_order;
            $poStock->shipping_cost       = $shippingCost;
            $poStock->note                = $request->note;
            $poStock->additional_expenses = $additionalExpenses;
            $poStock->total               = $request->total;
            $poStock->status              = $request->status;
            $poStock->save();

            $poStock->products()->detach();

            foreach ($request->product_id as $product_id) {
                $qty = $request->quantity[$product_id];
                $price = cleanCurrencyFormat($request->price[$product_id]);

                $purchaseCost = $this->calculatePurchaseCost(
                    $request->total,
                    $shippingCost,
                    $additionalExpenses,
                    $qty,
                    $price
                );

                $poStockProduct                = new PoStockProduct;
                $poStockProduct->po_stock_id   = $poStock->id;
                $poStockProduct->product_id    = $product_id;
                $poStockProduct->qty           = $qty;
                $poStockProduct->remaining_qty = $qty;
                $poStockProduct->unit_id       = $request->unit[$product_id];
                $poStockProduct->price         = $price;
                $poStockProduct->purchase_cost = $purchaseCost;
                $poStockProduct->save();
            }
        });

        return redirect()
            ->route('purchase_orders.edit', $purchase_order)
            ->with('success', 'Data has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PoStock $purchase_order)
    {
        try {
            DB::transaction(function () use ($purchase_order) {
                $purchase_order->products()->detach();
                $purchase_order->delete();
            });

            return APIresponse(true, 'Data successfully deleted.');
        } catch (\Throwable $th) {
            return APIresponse(false, $th->getMessage());
        }
    }

    public function changeStatus(Request $request, PoStock $poStock)
    {
        $poStock->status = $request->status;
        $poStock->save();
    }

    private function isPurchaseOrderEditable(PoStock $purchaseOrder): bool
    {
        if ($purchaseOrder->iscomplete()) {
            return false;
        }

        $hasUsedDetail = $purchaseOrder->poStockDetail->contains(function (PoStockProduct $detail) {
            return (float) $detail->remaining_qty !== (float) $detail->qty;
        });

        return !$hasUsedDetail;
    }

    private function editBlockedResponse(): RedirectResponse
    {
        $message = 'PO cannot be edited because it is complete or already used in another transaction.';

        return redirect()
            ->route('purchase_orders.index')
            ->with('danger', $message);
    }

    private function generateUniqueId($mainCategoryId, $supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $prefixId = sprintf("%03d", $mainCategoryId) . "/$supplier->code";
        $suffixId = date('m/Y');

        $records = PoStock::select('unique_id')->where('unique_id', 'like', "$prefixId/%/$suffixId")->get();

        if (count($records) < 1) {
            return "$prefixId/001/$suffixId";
        } else {
            $highestSequenceNumber = 0;

            foreach ($records as $record) {
                $sequenceNumber = (int)explode('/', $record->unique_id)[2];

                if ($sequenceNumber > $highestSequenceNumber) {
                    $highestSequenceNumber = $sequenceNumber;
                }
            }

            $newSequenceNumber = sprintf("%03d", $sequenceNumber + 1);

            return "$prefixId/$newSequenceNumber/$suffixId";
        }
    }

    public function getPoProducts(Request $request)
    {
        $poStock        = PoStock::findOrFail($request->po_stock_id);
        $poStockDetails = $poStock->poStockDetail;

        $options  = '<option disabled selected>-- Select Product SKU</option>';
        $products = [];
        $list     = '<ul>';

        foreach ($poStockDetails as $poStockDetail) {
            $products[] = [
                /* urutan berpengaruh pada kodingan javascriptnya */
                $poStockDetail->product,
                $poStockDetail->unit,
                $poStockDetail,
            ];

            $isSelected = ($poStockDetail->product_id == $request->product_id) ? 'selected' : '';

            $list .= '<li>' . $poStockDetail->product->skuFormat() . ' | ' . $poStockDetail->quantityFormat() . " (Remaining: $poStockDetail->remaining_qty " . $poStockDetail->unit->unit_name . ')</li>';

            $options .= '<option ' . $isSelected . ' data-qty="' . $poStockDetail->qty . '" '
                . 'data-remaining-qty="' . $poStockDetail->remaining_qty . '" '
                . 'data-unit-id="' . $poStockDetail->unit_id . '" '
                . 'data-unit-name="' . $poStockDetail->unit->unit_name . '" '
                . 'data-po-detail-id="' . $poStockDetail->id . '" '
                . 'data-price="' . $poStockDetail->price . '" '
                . 'data-purchase-cost="' . $poStockDetail->purchase_cost . '" '
                . 'value="' . $poStockDetail->product->id . '">'
                . $poStockDetail->product->skuFormat()
                . ' | ' . $poStockDetail->quantityFormat()
                . " (remaining: $poStockDetail->remaining_qty " . $poStockDetail->unit->unit_name
                . ")</option>";
        }

        $data['poStock']  = $poStock;
        $data['options']  = $options;
        $data['list']     = $list . '</ul>';
        $data['products'] = $products;

        return $data;
    }

    public function getPoOptions()
    {
        $poStockId = request('po_stock_id');

        $poStocks = PoStock::incomplete()
            ->whereHas('poStockDetail', function ($query) use ($poStockId) {
                $query->where('remaining_qty', '>', 0)
                    ->orWhere('po_stock_id', $poStockId);
            })->orderBy('unique_id')
            ->get();

        $options = '<option disabled selected>-- Select PO --</option>';

        foreach ($poStocks as $poStock) {
            $selected = ($poStock->id == $poStockId) ? 'selected' : '';

            $options .= "<option value='$poStock->id' $selected "
                    . "data-total='$poStock->total' "
                    ."data-supplier-id='$poStock->supplier_id'>"
                    . $poStock->poStockFormat()
                . "</option>";
        }

        return $options;
    }

    private function calculatePurchaseCost($totalPurchase, $shippingCost, $additionalExpenses, $qty, $price)
    {
        # nilai barang_A yg baru = (
        #     (
        #         (total nilai barang_A / total nilai seluruh barang)
        #         * (ongkir + additional expense)
        #     )
        #     / qty barang_A)
        #     + harga per pcs barang_A

        $totalXItemPrice = $qty * $price;
        $allItemPurchase = $totalPurchase - ($shippingCost + $additionalExpenses);
        $totalExpenses = $shippingCost + $additionalExpenses;

        $purchaseCost = ((($totalXItemPrice / $allItemPurchase) * $totalExpenses) / $qty) + $price;

        return round($purchaseCost);
    }
}
