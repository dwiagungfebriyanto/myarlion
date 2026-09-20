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

class InventorySampleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('stock', function(InventoryStock $inventorySample) {
                return $inventorySample->getStock() ?? '';
            })
            ->editColumn('product_id', function(InventoryStock $inventorySample) {
                return $inventorySample->product->skuFormat() ?? '';
            })
            ->addColumn('po', function(InventoryStock $inventorySample) {
                if (!is_null($inventorySample->getPoStock())) {
                    return '<a href="' .route('po_stock.detail', $inventorySample->getPoStock()->id) .'" target="_blank">'
                            ."{$inventorySample->getPoStock()->unique_id}</a>";
                }

                return '';
            })
            ->editColumn('warehouse_id', function(InventoryStock $inventorySample) {
                return $inventorySample->warehouse->warehouse_name;
            })
            ->editColumn('purchase_cost', function(InventoryStock $inventorySample) {
                return currencyFormat($inventorySample->purchase_cost);
            })
            ->editColumn('purchase_cost_per_unit', function(InventoryStock $inventorySample) {
                return currencyFormat($inventorySample->purchase_cost_per_unit);
            })
            ->editColumn('created_at', function(InventoryStock $inventorySample) {
                return Carbon::parse($inventorySample->updated_at)->diffForHumans();
            })
            ->editColumn('updated_at', function(InventoryStock $inventorySample) {
                return Carbon::parse($inventorySample->updated_at)->diffForHumans();
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
        $query->where('stock_bucket', 'sample');

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
                    ->setTableId('inventorysample-table')
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
            
            Column::computed('stock'),
            
            Column::make('product_id')
                ->title('Product')
                ->orderable(false),
            
            Column::computed('po')
                ->title('PO Sample')
                ->orderable(false),
            
            Column::make('warehouse_id')
                ->title('Warehouse')
                ->orderable(false),

            Column::make('purchase_cost'),
            Column::make('purchase_cost_per_unit'),

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
        return 'InventorySample_' . date('YmdHis');
    }
}
