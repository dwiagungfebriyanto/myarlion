<?php

namespace App\DataTables;

use App\Models\JobEditAmountApproval;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class JobEditAmountApprovalDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', function (JobEditAmountApproval $row) {
                return Carbon::parse($row->created_at)->diffForHumans();
            })
            ->editColumn('status', function (JobEditAmountApproval $row) {
                $icon = "<u>$row->status</u>";

                if ($row->status === 'approved') {
                    $icon = '<i class="fas fa-check-circle text-success" title="Approved"></i>';
                } elseif ($row->status === 'rejected') {
                    $icon = '<i class="fas fa-times-circle text-danger" title="Rejected"></i>';
                }

                if (auth()->user()->can('approve request edit amount')) {
                    return '<i class="btnChangeStatus text-info" data-toggle="modal" data-target="#modalChangeStatus"
                                data-url="' .route('edit_amount_approval.change_status', $row->id) .'"
                                data-status="' . $row->status . '" onclick="setChangeStatusModal(this)">
                                '.$icon.'
                            </i>';
                } else {
                    return $icon;
                }
            })
            ->editColumn('job_code', function (JobEditAmountApproval $row) {
                return "<a href='" . route('job.list.edit', $row->job_id) . "' target='_blank'>{$row->job_code}</a>";
            })
            ->editColumn('period_job', function (JobEditAmountApproval $row) {
                return !is_null($row->period_job)
                    ? date('Y F', strtotime($row->period_job))
                    : '-';
            })
            ->editColumn('customer_name', function (JobEditAmountApproval $row) {
                return "{$row->customer_code} - {$row->customer_name}";
            })
            ->editColumn('old_amount', function (JobEditAmountApproval $row) {
                return currencyFormat($row->old_amount) . " <i class='fas fa-arrow-right'></i> " .currencyFormat($row->request_amount);
            })
            ->editColumn('marketing_name', function (JobEditAmountApproval $row) {
                return $row->marketing_name;
            })
            ->editColumn('requester_id', function (JobEditAmountApproval $row) {
                return $row->requester->name ?? '-';
            })
            ->editColumn('reviewer_id', function (JobEditAmountApproval $row) {
                return $row->reviewer->name ?? '-';
            })
            // ->addColumn('action', function (JobEditAmountApproval $row) {
            //     return view('pages.job_edit_amount_approval.components.action-button', $row);
            // })
            ->addIndexColumn()
            ->rawColumns(['status', 'job_code', 'old_amount', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(JobEditAmountApproval $model): QueryBuilder
    {
        $filterStatus = request('status');

        $query = $model->newQuery()
            ->with(['requester', 'reviewer'])
            ->select(
                'job_edit_amount_approvals.*',
                'jobs.code as job_code',
                'jobs.period_job',
                'customers.code as customer_code',
                'customers.name as customer_name',
                'users.name as marketing_name',
            )
            ->leftJoin('jobs', 'job_edit_amount_approvals.job_id', '=', 'jobs.id')
            ->leftJoin('customers', 'jobs.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'jobs.employee_id', '=', 'users.id');

        if ($filterStatus && $filterStatus !== 'all') {
            $query->where('job_edit_amount_approvals.status', $filterStatus);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('jobEditAmountApproval-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(2, 'desc')
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->addClass('text-center'),
            Column::make('created_at')->title('Requested at'),
            Column::make('status')->addClass('text-center'),
            Column::make('job_code', 'jobs.code')->title('Job')->addClass('text-center'),
            Column::make('period_job', 'jobs.period_job')->title('Period')->addClass('text-center'),
            Column::make('customer_name', 'customers.name')->title('Customer'),
            Column::make('old_amount')->title('Edit Amount'),
            Column::make('marketing_name', 'users.name')->title('Marketing'),
            Column::make('note'),
            Column::make('requester_id')->title('Requested by')->searchable(false),
            Column::make('reviewer_id')->title('Reviewed by')->searchable(false),
            // Column::computed('action')->addClass('text-center')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'JobEditAmountRequest_' . date('YmdHis');
    }
}
