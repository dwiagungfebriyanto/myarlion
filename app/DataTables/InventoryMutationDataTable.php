<?php

namespace App\DataTables;

use App\Models\InventoryMutation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InventoryMutationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('new_sku.sku', function (InventoryMutation $inventoryMutation)
            {
                return $inventoryMutation->product->skuFormat();
            })
            ->editColumn('qty', function (InventoryMutation $inventoryMutation)
            {
                return $inventoryMutation->quantityValue();
            })
            ->editColumn('price', function (InventoryMutation $inventoryMutation)
            {
                return currencyFormat($inventoryMutation->price);
            })
            ->editColumn('ori_sku.sku', function (InventoryMutation $inventoryMutation)
            {
                return $inventoryMutation->originalSku->skuFormat();
            })
            ->editColumn('original_stock_type', function (InventoryMutation $inventoryMutation)
            {
                return "$inventoryMutation->original_stock_type#$inventoryMutation->original_stock_id";
            })
            ->editColumn('qty_mutation', function (InventoryMutation $inventoryMutation)
            {
                return $inventoryMutation->quantityMutationValue();
            })
            ->addColumn('action', 'pages.inventory_mutation.components.action-button')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(InventoryMutation $model): QueryBuilder
    {
        return $model->newQuery()
        ->join('products as new_sku', 'inventory_mutations.new_product_id', '=', 'new_sku.id')
        ->join('products as ori_sku', 'inventory_mutations.original_product_id', '=', 'ori_sku.id')
        ->select(
            'inventory_mutations.*',
            'new_sku.sku',
            'ori_sku.sku',
        );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('inventory-mutation-datatable')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('new_sku.sku', 'new_sku.sku')->title('New SKU'),
            Column::make('qty')->title('Quantity'),
            Column::make('price'),
            Column::make('ori_sku.sku', 'ori_sku.sku')->title('Original SKU'),
            Column::make('original_stock_type')->title('Original Stock ID'),
            Column::make('qty_mutation')->title('Quantity Mutation'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::computed('action')->printable(false)->exportable(false)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'InventoryMutation_' . date('YmdHis');
    }
}
