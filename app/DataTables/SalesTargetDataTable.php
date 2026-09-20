<?php

namespace App\DataTables;

use App\Models\SalesTarget;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesTargetDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('user_id', function ($data) {
                return $data->user->name;
            })
            ->editColumn('target', function ($data) {
                return currencyFormat($data->target);
            })
            ->editColumn('estimate_profit', function ($data) {
                return currencyFormat($data->estimate_profit);
            })
            ->editColumn('percentage', function ($data) {
                return empty($data->percentage) ? '' : "$data->percentage%";
            })
            ->addColumn('action', 'pages.sales_target.components.action-button')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SalesTarget $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('salestarget-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
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
            Column::make('id')->title('#'),
            Column::make('year'),
            Column::make('user_id')->title('Marketing'),
            Column::make('target'),
            Column::make('estimate_profit'),
            Column::make('percentage')->addClass('text-center'),
            // Column::make('commission'),
            Column::computed('action')
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SalesTarget_' . date('YmdHis');
    }
}
