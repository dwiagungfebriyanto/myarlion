<?php

namespace App\DataTables;

use App\Models\ReturnStock;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ReturnStockDataTable extends DataTable
{
    /**
     * Build DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('warehouse', function ($row) {
                return $row->warehouse ? $row->warehouse->warehouse_name : $row->warehouse_id;
            })
            ->addColumn('inventory_stock', function ($row) {
                if ($row->inventoryStock && $row->inventoryStock->product) {
                    return $row->inventoryStock->product->skuFormat();
                }
                return '#' . $row->inventory_stock_id;
            })
            ->addColumn('supplier', function ($row) {
                $supplier = $row->supplier;
                return $supplier ? $supplier->supplier_name : '-';
            })
            ->addColumn('quantities', function ($row) {
                $qty = $row->qty;
                $unitType = "";

                if ($row->inventoryStock && $row->inventoryStock->unit) {
                    $unitType = $row->inventoryStock->unit->unit_name;
                }

                $formattedQty = (floor($qty) == $qty) ? number_format($qty, 0) : rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
                return $formattedQty . ' ' . $unitType;
            })
            ->addColumn('total', function ($row) {
                return currencyFormat($row->total);
            })
            ->addColumn('action', function ($row) {
                return view('pages.return_stock.components.action-button', ['return' => $row])->render();
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     */
    public function query(ReturnStock $model): QueryBuilder
    {
        $query = $model->with(['warehouse', 'inventoryStock.product', 'inventoryStock.unit'])->newQuery();

        // Apply filter if present
        if (request()->has('filter') && is_numeric(request()->filter)) {
            $query->where('warehouse_id', request()->filter);
        }

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('responsive-datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 10,
                'searching' => true,
                'info' => true,
                'ordering' => true,
            ]);
    }

    /**
     * Get columns.
     */
    protected function getColumns(): array
    {
        return [
            Column::make('id')->title('#')->width('10%'),
            Column::computed('warehouse')->title('Warehouse'),
            Column::computed('inventory_stock')->title('Inventory Stock'),
            Column::computed('supplier')->title('Supplier'),
            Column::computed('quantities')->title('Quantities'),
            Column::computed('total')->title('Total'),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'ReturnStock_' . date('YmdHis');
    }
}
