<?php

namespace App\DataTables;

use App\Models\Brand;
use App\Models\InquiryProduct;
use App\Models\Main_Category;
use App\Models\Packaging;
use App\Models\Product_Type;
use App\Models\Specification;
use App\Models\Sub_Category;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InquiryProductsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */

    private $inq_id;
    private $status;

    public function __construct($inq_id, $status)
    {
        $this->inq_id = $inq_id;
        $this->status = $status;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('no', function () {
                static $no = 0;
                $no++;
                return $no;
            })
            ->editColumn('main_category_id', function ($inquiryProduct) {
                $main_category = Main_Category::find($inquiryProduct->main_category_id);
                return $main_category->code . ' - ' . $main_category->main_category_name;
            })
            ->editColumn('sub_category_id', function ($inquiryProduct) {
                $sub_category = Sub_Category::find($inquiryProduct->sub_category_id);
                if ($sub_category == null) {
                    return '';
                } else {
                    return $sub_category->code . ' - ' . $sub_category->category_name;
                }
            })
            ->editColumn('product_type_id', function ($inquiryProduct) {
                $product_type = Product_Type::find($inquiryProduct->product_type_id);
                if ($product_type == null) {
                    return '';
                } else {
                    return $product_type->code . ' - ' . $product_type->product_type_name;
                }
            })
            ->editColumn('brand_id', function ($inquiryProduct) {
                $brand = Brand::find($inquiryProduct->brand_id);
                if ($brand == null) {
                    return '';
                } else {
                    return $brand->code . ' - ' . $brand->brand_name;
                }
            })
            ->editColumn('packaging_id', function ($inquiryProduct) {
                $packaging = Packaging::find($inquiryProduct->packaging_id);
                if ($packaging == null) {
                    return '';
                } else {
                    return $packaging->code . ' - ' . $packaging->packaging_name;
                }
            })
            ->editColumn('specification_id', function ($inquiryProduct) {
                $specification = Specification::find($inquiryProduct->specification_id);
                if ($specification == null) {
                    return '';
                } else {
                    return $specification->code . ' - ' . $specification->specification_name;
                }
            })
            ->addColumn('action', 'pages.inquiry.components.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(InquiryProduct $model): QueryBuilder
    {
        return $model->newQuery()->where('inquiry_id', $this->inq_id);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('inquiryproducts-table')
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
        $columns = [
            Column::make('no', 'no')->title('No'),
            Column::make('main_category_id', 'main_category_id')->title('Main Category'),
            Column::make('sub_category_id', 'sub_category_id')->title('Sub Category'),
            Column::make('brand_id', 'brand_id')->title('Brand'),
            Column::make('product_type_id', 'product_type_id')->title('Product Type'),
            Column::make('packaging_id', 'packaging_id')->title('Packaging'),
            Column::make('specification_id', 'specification_id')->title('Specification'),
        ];

        if ($this->status !== 'sales') {
            $columns[] = Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center');
        }

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'InquiryProducts_' . date('YmdHis');
    }
}
