<?php

namespace App\DataTables;

use App\Models\PoStock;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PurchaseOrderDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('supplier_id', function (PoStock $purchaseOrder) {
                $supplier = $purchaseOrder->supplier;

                return "$supplier->code | $supplier->supplier_name";
            })
            ->addColumn('products', function (PoStock $purchaseOrder) {
                $productsHTML = '';

                foreach ($purchaseOrder->products as $product) {
                    $productsHTML .= "<li>{$product->skuFormat()}</li>";
                }

                return "<ul class='pl-2'>$productsHTML</ul>";
            })
            ->addColumn('quantity', function (PoStock $purchaseOrder) {
                $qtyHTML = '';

                foreach ($purchaseOrder->poStockDetail as $poDetail) {
                    $qtyHTML .= "<li>{$poDetail->quantityFormat()} | Remaining: {$poDetail->quantityRemainingFormat()}</li>";
                }

                return "<ul class='pl-2'>$qtyHTML</ul>";
            })
            ->editColumn('status', function (PoStock $purchaseOrder) {
                return ($purchaseOrder->status === 'complete')
                    ? '<i class="fas fa-check text-success"></i>'
                    : '';
            })
            ->addColumn('price', function (PoStock $purchaseOrder) {
                $priceHTML = '';

                foreach ($purchaseOrder->poStockDetail as $poDetail) {
                    $priceHTML .= "<li>" . currencyFormat($poDetail->price) . " /{$poDetail->unit->unit_name}</li>";
                }

                return "<ul class='pl-2'>$priceHTML</ul>";
            })
            ->addColumn('purchase_cost', function (PoStock $purchaseOrder) {
                $purchaseCostHTML = '';

                foreach ($purchaseOrder->poStockDetail as $poDetail) {
                    $purchaseCostHTML .= "<li>" . currencyFormat($poDetail->purchase_cost) . " /{$poDetail->unit->unit_name}</li>";
                }

                return "<ul class='pl-2'>$purchaseCostHTML</ul>";
            })
            ->editColumn('total', function (PoStock $purchaseOrder) {
                return currencyFormat($purchaseOrder->total);
            })
            ->editColumn('created_at', function (PoStock $purchaseOrder) {
                return Carbon::parse($purchaseOrder->created_at)->diffForHumans();
            })
            ->editColumn('updated_at', function (PoStock $purchaseOrder) {
                return Carbon::parse($purchaseOrder->updated_at)->diffForHumans();
            })
            ->addColumn('action', function (PoStock $purchaseOrder) {
                $disableEditButton = $purchaseOrder->poStockDetail->contains(function($detail) {
                    return $detail->remaining_qty !== $detail->qty;
                }) ? 'disabled' : '';

                return view('pages.po_stock.components.action_button', compact('purchaseOrder', 'disableEditButton'));
            })
            ->rawColumns([
                'products',
                'quantity',
                'price',
                'purchase_cost',
                'status',
                'action',
            ])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PoStock $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with('supplier')
            ->leftJoin('suppliers', 'po_stocks.supplier_id', '=', 'suppliers.id')
            ->select('po_stocks.*');

        $typeFilter   = request('type');
        $statusFilter = request('status');
        $showAllData  = request()->boolean('show_all');

        if ($typeFilter && $typeFilter !== 'all') {
            $query->where('po_stocks.po_type', $typeFilter);
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('po_stocks.status', $statusFilter);
        } elseif (!$showAllData) {
            $query->where('po_stocks.status', 'incomplete');
        }

        if (!$showAllData) {
            $query->whereHas('poStockDetail', function ($detailQuery) {
                $detailQuery->where('remaining_qty', '>', 0);
            });
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('po-datatable')
            ->columns($this->getColumns())
            ->minifiedAjax(
                url()->current(),
                <<<'JS'
                    data.type = $('#filter-type').val();
                    data.status = $('#filter-status').val();
                    data.show_all = $('#showAllData').is(':checked') ? 1 : 0;
                JS
            )
            ->dom('Bfrtip')
            ->orderBy(11, 'desc')
            ->selectStyleSingle()
            ->buttons([
                // Button::make('excel'),
                // Button::make('csv'),
                // Button::make('pdf'),
                // Button::make('print'),
                // Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')
                ->title('#')
                ->className('text-center')
                ->searchable(false),

            Column::make('unique_id')
                ->title('Unique ID'),

            Column::make('po_type')
                ->title('PO Type')
                ->className('text-center text-capitalize')
                ->orderable(false),

            Column::make('supplier_id', 'suppliers.supplier_name')
                ->title('Supplier'),

            Column::computed('products'),
            Column::computed('quantity'),

            Column::make('status')
                ->title('Complete')
                ->className('text-center'),

            Column::computed('price')
                ->orderable(true),

            Column::computed('purchase_cost')
                ->orderable(true),

            Column::make('total'),
            Column::make('note'),
            Column::make('created_at')
                ->title('Created'),

            Column::make('updated_at')
                ->title('Updated'),

            Column::computed('action')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'PurchaseOrder_' . date('YmdHis');
    }
}
