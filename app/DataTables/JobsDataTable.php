<?php

namespace App\DataTables;

use App\Models\Customer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class JobsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'pages.job.components.action.action-button')
            ->editColumn('customers.name', function ($job) {
                return $job->customer_id . ' - ' . $job->customer_name;
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Job $model): QueryBuilder
    {
        $customerFilter = request('customer') && request('customer') !== 'all'
            ? request('customer')
            : null;

        $marketingFilter = request('marketing') && request('marketing') !== 'all'
            ? request('marketing')
            : null;

        $query = $model->newQuery()
            ->select(DB::raw('CAST(jobs.code AS SIGNED) AS code_int'), 'jobs.*', 'customers.name as customer_name', 'customers.code as customer_id', 'users.name as marketing')
            ->leftJoin('customers', 'jobs.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'jobs.employee_id', '=', 'users.id');

        if ($customerFilter) $query->where('customers.code', $customerFilter);
        if ($marketingFilter) $query->where('users.id', $marketingFilter);

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('jobs-table')
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
            Column::make('code')->orderable(false)->visible(false),
            Column::make('code_int', 'code_int')->title('Job ID')->addClass('align-middle')->searchable(false),
            Column::make('period_job', 'period_job')->title('Period')->addClass('align-middle'),
            Column::make('customers.name', 'customers.name')->title('Customer')->addClass('align-middle'),
            Column::make('marketing', 'users.name')->title('Marketing')->addClass('align-middle'),
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
        return 'Jobs_' . date('YmdHis');
    }
}
