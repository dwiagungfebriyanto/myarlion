<?php

namespace App\DataTables;

use App\Models\InventoryStock;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InventoryStockDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('stock', function(InventoryStock $inventoryStock) {
                return $inventoryStock->getStock() ?? '';
            })
            ->editColumn('product_id', function(InventoryStock $inventoryStock) {
                return $inventoryStock->product?->skuFormat() ?? '';
            })
            ->addColumn('po', function(InventoryStock $inventoryStock) {
                if (!is_null($inventoryStock->getPoStock())) {
                    return '<a href="' .route('po_stock.detail', $inventoryStock->getPoStock()->id) .'" target="_blank">'
                        ."{$inventoryStock->getPoStock()->unique_id}</a>";
                }

                return '';
            })
            ->editColumn('warehouse_id', function(InventoryStock $inventoryStock) {
                return $inventoryStock->warehouse->warehouse_name;
            })
            ->editColumn('purchase_cost', function(InventoryStock $inventoryStock) {
                return currencyFormat($inventoryStock->purchase_cost);
            })
            ->editColumn('purchase_cost_per_unit', function(InventoryStock $inventoryStock) {
                return currencyFormat($inventoryStock->purchase_cost_per_unit);
            })
            ->editColumn('inventory_id', function(InventoryStock $inventoryStock) {
                return ucfirst($inventoryStock->getStockBucket());
            })
            ->editColumn('created_at', function(InventoryStock $inventoryStock) {
                return Carbon::parse($inventoryStock->updated_at)->diffForHumans();
            })
            ->editColumn('updated_at', function(InventoryStock $inventoryStock) {
                return Carbon::parse($inventoryStock->updated_at)->diffForHumans();
            })
            ->rawColumns(['po'])
            // ->addIndexColumn()
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(InventoryStock $model): QueryBuilder
    {
        $query = $model->newQuery();
        $query->where('stock_bucket', '!=', 'sample');

        $filterWarehouse = request('warehouse');

        if ($filterWarehouse && $filterWarehouse !== 'all') {
            $query->where('warehouse_id', $filterWarehouse);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('inventorystock-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(8)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('print'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            // Column::computed('DT_RowIndex')
            //     ->title('#')
            //     ->addClass('text-center'),
            
            Column::make('id')
                ->title('ID')
                ->addClass('text-center'),
            
            Column::computed('stock')
                ->orderable(false),
            
            Column::make('product_id')
                ->title('Product')
                ->orderable(false),
            
            Column::computed('po')
                ->title('PO Stock'),
            
            Column::make('warehouse_id')
                ->title('Warehouse')
                ->orderable(false),

            Column::make('purchase_cost'),
            Column::make('purchase_cost_per_unit'),

            Column::make('inventory_id')
                ->title('Stock Bucket')
                ->orderable(false),

            Column::make('created_at')
                ->printable(false),

            Column::make('updated_at')
                ->printable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'InventoryStock_' . date('YmdHis');
    }
}
