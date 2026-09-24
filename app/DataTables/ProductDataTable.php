<?php

namespace App\DataTables;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Product_Type;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('harga_rata_rata', function (Product $row) {
                return currencyFormat($row->harga_rata_rata);
            })
            ->editColumn('harga_tertinggi', function (Product $row) {
                return currencyFormat($row->harga_tertinggi);
            })
            ->editColumn('qty', function (Product $row) {
                return $row->qty . ' ' . $row->unit->unit_name;
            })
            ->editColumn('updated_at', function (Product $row) {
                return $row->updated_at->format('d-m-Y H:i:s');
            })
            // New column handler
            ->addColumn('category_label', function (Product $row) {
                return 'General';
            })
            ->addColumn('color_label', function (Product $row) {
                return 'Brown';
            })
            ->addColumn('dimension_label', function (Product $row) {
                return '1x1x1';
            })
            ->addColumn('action', 'pages.product.list.components.action-button')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery()
        ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
        ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
        ->join('brands', 'products.brand_id', '=', 'brands.id')
        ->select('products.*', 'product_types.product_type_name', 'suppliers.supplier_name', 'brands.brand_name')
        ;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('product-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('sku', 'sku')->title('SKU')->addClass('align-middle'),
            // New column
            Column::computed('category_label')
                ->title('Category')
                ->addClass('align-middle'),
            Column::computed('color_label')
                ->title('Color')
                ->addClass('align-middle'),
            Column::computed('dimension_label')
                ->title('Dimension')
                ->addClass('align-middle'),
            Column::make('product_type_name', 'product_types.product_type_name')->title('Product')->addClass('align-middle'),
            Column::make('supplier_name', 'suppliers.supplier_name')->title('Supplier')->addClass('align-middle'),
            Column::make('brand_name', 'brands.brand_name')->title('Brand')->addClass('align-middle'),
            Column::make('qty', 'qty')->title('Stock')->addClass('align-middle'),
            Column::make('harga_rata_rata', 'harga_rata_rata')->title('Harga Rata - Rata')->addClass('align-middle'),
            Column::make('harga_tertinggi', 'harga_tertinggi')->title('Harga Tertinggi')->addClass('align-middle'),
            Column::computed('updated_at')->title('Updated')->addClass('align-middle'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Product_' . date('YmdHis');
    }
}
