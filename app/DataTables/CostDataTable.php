<?php

namespace App\DataTables;

use App\Models\OutcomeCheque;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CostDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('code', function (OutcomeCheque $cost) {
                return outputCodeByType($cost->code, $cost->code_type);
            })
            ->editColumn('outcome_type_id', function (OutcomeCheque $cost) {
                return "$cost->outcome_type_id - {$cost->outcomeType->name}";
            })
            ->editColumn('amount', function (OutcomeCheque $cost) {
                return currencyFormat($cost->amount);
            })
            ->editColumn('account_number', function (OutcomeCheque $cost) {
                return '[ ' . $cost->bank_name . " - $cost->currency_code ] "
                    . "$cost->account_number - $cost->account_name";
            })
            ->addColumn('action', 'pages.cost.components.action-button')
            ->rawColumns(['code', 'action'])
            ->setRowId('record_id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(OutcomeCheque $model): QueryBuilder
    {
        return $model->newQuery()
            ->join('outcome_types', 'outcome_cheques.outcome_type_id', '=', 'outcome_types.id', 'left')
            ->join('bank_accounts', 'outcome_cheques.bank_account_id', '=', 'bank_accounts.id', 'left')
            ->join('banks', 'banks.id', '=', 'bank_accounts.bank_id', 'left')

            ->select(
                'outcome_cheques.*',
                'outcome_cheques.id as record_id',
                'outcome_types.name as outcome_type_name',
                'bank_accounts.*',
                'bank_accounts.id as bank_accounts_id as ',
                'banks.bank_name',
            );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('cost-datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('print'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('date'),
            Column::make('code'),
            Column::make('outcome_type_id')->title('Category Code'),
            Column::make('note')->addClass('long-text'),
            Column::make('amount')->title('Amount (IDR)'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Cost_' . date('YmdHis');
    }
}
