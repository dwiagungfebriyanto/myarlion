<?php

namespace App\DataTables;

use App\Models\JobTeam;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class JobTeamDataTable extends DataTable
{
    private $_jobID;

    public function __construct($_jobID)
    {
        $this->_jobID = $_jobID;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('no', function () {
                static $i = 0;
                $i++;

                return $i;
            })
            ->editColumn('job_percentage', function (JobTeam $jobTeam) {
                return "$jobTeam->job_percentage%";
            })
            ->addColumn('action', function ($row) {
                if ($row->job_status === 'open') {
                    return view('pages.job.components.action.action-job-team', $row);
                }
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(JobTeam $model): QueryBuilder
    {
        return $model
            ->join('jobs', 'job_teams.job_id', '=', 'jobs.id')
            ->join('users', 'job_teams.user_id', '=', 'users.id')
            ->where('job_id', $this->_jobID)
            ->select(
                'job_teams.*',
                'jobs.employee_id',
                'jobs.status AS job_status',
                'users.name',
                'users.id AS user_record_id',
            )
            ->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('jobteam-table')
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
            Column::computed('no'),
            Column::make('name', 'users.name')->title('Employee'),
            Column::make('job_percentage'),
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
        return 'JobTeam_' . date('YmdHis');
    }
}
