<?php

namespace App\DataTables;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ActivityLogDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', function (ActivityLog $log) {
                return $log->created_at->format('d-m-Y H:i:s');
            })
            ->editColumn('causer_id', function (ActivityLog $log) {
                return $log->causer->name .' - ' .$log->causer->position;
            })
            ->addColumn('properties', 'pages.system.components.log_detail')
            ->rawColumns(['properties']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ActivityLog $model): QueryBuilder
    {
        $query = $model->newQuery();
        
        $filterCauser = request()->causer;
        if (is_numeric($filterCauser)) {
            $query = $query->where('causer_id', $filterCauser);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('activitylog-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0)
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('created_at')->title('Time'),
            Column::make('log_name'),
            Column::make('subject_type'),
            Column::make('event'),
            Column::make('subject_id'),
            Column::make('causer_id')->title('Causer'),
            Column::computed('properties'),
            Column::make('batch_uuid'),
            Column::make('browser'),
            Column::make('os'),
            Column::make('ip_address'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ActivityLog_' . date('YmdHis');
    }
}
