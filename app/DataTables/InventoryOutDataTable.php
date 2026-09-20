<?php

namespace App\DataTables;

use App\Models\InventoryOut;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InventoryOutDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('product_sku', function(InventoryOut $inventoryOut) {
                    return $inventoryOut->product->skuFormat();
                })
            ->editColumn('original_warehouse_id', function(InventoryOut $inventoryOut) {
                    return $inventoryOut->originalWarehouse()->warehouse_name;
                })
            ->editColumn('quantity', function(InventoryOut $inventoryOut) {
                    return $inventoryOut->quantityValue();
                })
            ->editColumn('purchase_cost', function(InventoryOut $inventoryOut) {
                    return currencyFormat($inventoryOut->purchase_cost);
                })
            ->editColumn('purchase_cost_per_unit', function(InventoryOut $inventoryOut) {
                    return currencyFormat($inventoryOut->purchase_cost_per_unit);
                })
            ->addColumn('action', 'pages.inventory_out.components.action-button')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(InventoryOut $model): QueryBuilder
    {
        return $model->newQuery()
            ->join('products', 'inventory_outs.product_id', '=', 'products.id')
            ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->join('specifications', 'products.specification_id', '=', 'specifications.id')
            ->join('packagings', 'products.packaging_id', '=', 'packagings.id')
            ->join('units', 'inventory_outs.unit_id', '=', 'units.id')
            ->join('warehouses', 'inventory_outs.warehouse_id', '=', 'warehouses.id')
            ->select(
                    'inventory_outs.*',
                    'products.sku',
                    'product_types.product_type_name',
                    'specifications.specification_name',
                    'packagings.packaging_name',
                    'units.unit_name',
                    'warehouses.warehouse_name',
                );
        ;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('inventory-out-datatable')
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
            Column::make('product_sku')->title('Product'),
            Column::make('status'),
            Column::make('warehouse_name', 'warehouses.warehouse_name')->title('Warehouse'),
            Column::make('original_warehouse_id')->title('Original Warehouse'),
            Column::computed('quantity'),
            Column::make('purchase_cost'),
            Column::make('purchase_cost_per_unit'),
            Column::make('updated_at'),
            Column::make('created_at'),
            Column::computed('action')
                //   ->exportable(false)
                //   ->printable(false)
                //   ->width(60)
                //   ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'InventoryOut_' . date('YmdHis');
    }
}
