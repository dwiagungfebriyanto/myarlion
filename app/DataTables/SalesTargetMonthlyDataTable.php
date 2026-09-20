<?php

namespace App\DataTables;

use App\Models\SalesTargetMonthly;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SalesTargetMonthlyDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('month', function ($salesTarget) {
                return date('Y F' , strtotime($salesTarget->month));
            })
            ->editColumn('target', function ($salesTarget) {
                return currencyFormat($salesTarget->target);
            })
            ->editColumn('estimate_profit', function ($salesTarget) {
                return currencyFormat($salesTarget->estimate_profit);
            })
            ->editColumn('percentage', function ($salesTarget) {
                return "$salesTarget->percentage%";
            })
            ->editColumn('user_id', function ($salesTarget) {
                return $salesTarget->user->name;
            })
            ->addColumn('action', 'pages.sales_target_monthly.components.action_button')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SalesTargetMonthly $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('salestargetmonthly-table')
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
            Column::computed('DT_RowIndex')
              ->title('#')
              ->searchable(false)
              ->orderable(false)
              ->width(30) 
              ->addClass('text-center'),
            Column::make('month'),
            Column::make('user_id')->title('Marketing'),
            Column::make('target')->addClass('text-right'),
            Column::make('estimate_profit')->addClass('text-right'),
            Column::make('percentage')->addClass('text-center'),
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
        return 'SalesTargetMonthly_' . date('YmdHis');
    }
}
