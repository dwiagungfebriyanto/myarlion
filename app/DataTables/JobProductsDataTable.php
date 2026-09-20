<?php

namespace App\DataTables;

use App\Models\Job;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class JobProductsDataTable extends DataTable
{
    private $job_id;

    public function __construct($job_id)
    {
        $this->job_id = $job_id;
    }


    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('price', function ($jobproduct) {
                return 'Rp. ' . number_format($jobproduct->price, 2, ',', '.');
            })
            ->editColumn('note', function ($jobproduct) {
                return $jobproduct->note ?? '-';
            })
            ->addColumn('action', function ($jobproduct) {
                if ($jobproduct->status === 'open') {
                    return view('pages.job.components.action.action-job-product', $jobproduct);
                }
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Job $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('jobs.id', $this->job_id)
            ->join('jobs_has_products', 'jobs_has_products.job_id', '=', 'jobs.id')
            ->join('products', 'products.id', '=', 'jobs_has_products.product_id')
            ->join('product_types', 'product_types.id', '=', 'products.product_type_id')
            ->join('specifications', 'specifications.id', '=', 'products.specification_id')
            ->join('packagings', 'packagings.id', '=', 'products.packaging_id')
            ->select(
                'jobs_has_products.*',
                'products.sku',
                'product_types.product_type_name',
                'specifications.specification_name',
                'packagings.packaging_name',
                'jobs.status',
            );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('jobproducts-table')
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
            Column::make('sku', 'products.sku')->title('SKU'),
            Column::make('product_type_name', 'product_types.product_type_name')->title('Product'),
            Column::make('specification_name', 'specifications.specification_name')->title('Specification'),
            Column::make('packaging_name', 'packagings.packaging_name')->title('Packaging'),
            Column::make('quantity', 'jobs_has_products.quantity')->title('Quantity'),
            Column::make('price', 'jobs_has_products.price')->title('Price'),
            Column::make('note', 'jobs_has_products.note')->title('Note'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'JobProducts_' . date('YmdHis');
    }
}
