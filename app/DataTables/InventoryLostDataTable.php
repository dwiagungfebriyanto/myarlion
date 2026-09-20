<?php

namespace App\DataTables;

use App\Models\InventoryLost;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InventoryLostDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('product_id', function(InventoryLost $inventoryLost) {
                return $inventoryLost->product->skuFormat();
            })
            ->editColumn('inventory_stock_id', function(InventoryLost $inventoryLost) {
                return '#' .$inventoryLost->inventory_stock_id;
            })
            ->editColumn('quantity', function(InventoryLost $inventoryLost) {
                    return $inventoryLost->quantityValue();
                })
            ->addColumn('action', 'pages.inventory_lost.components.action-button')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(InventoryLost $model): QueryBuilder
    {
        return $model->newQuery()
        ->join('products', 'inventory_losts.product_id', '=', 'products.id')
        ->join('warehouses', 'inventory_losts.warehouse_id', '=', 'warehouses.id')
        ->select(
                'inventory_losts.*',
                'warehouses.warehouse_name',
            );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('inventory-lost-datatable')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle();
                    // ->buttons([
                    //     Button::make('excel'),
                    //     Button::make('csv'),
                    //     Button::make('pdf'),
                    //     Button::make('print'),
                    //     Button::make('reset'),
                    //     Button::make('reload')
                    // ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('#'),
            Column::make('datetime'),
            Column::make('product_id')->title('Product'),
            Column::make('status'),
            Column::make('inventory_stock_id')->title('From Stock'),
            Column::computed('quantity'),
            Column::make('warehouse_name', 'warehouses.warehouse_name')->title('Warehouse'),
            Column::make('updated_at'),
            Column::make('created_at'),
            Column::computed('action')
                // ->exportable(false)
                // ->printable(false)
                // ->width(60)
                // ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'InventoryLost_' . date('YmdHis');
    }
}
