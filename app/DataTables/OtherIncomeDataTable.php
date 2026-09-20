<?php

namespace App\DataTables;

use App\Models\OtherIncome;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OtherIncomeDataTable extends DataTable
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
            ->editColumn('date', function ($data) {
                return date('d-m-Y', strtotime($data->date));
            })
            ->editColumn('bank_account_id', function ($data) {
                return $data->bankAccount->bankAccountLabel();
            })
            ->editColumn('other_income_category_id', function ($data) {
                return $data->category?->category_name ?? '-';
            })
            ->editColumn('outcome_type_id', function ($data) {
                return $data->outcomeType?->name;
            })
            ->editColumn('code', function ($data) {
                return outputCodeByType($data->code, $data->code_type);
            })
            ->editColumn('amount', function ($data) {
                return currencyFormat($data->amount);
            })
            ->addColumn('recipient', function ($data) {
                return $data->recipient ? $data->recipient->label('-') : '-';
            })
            ->addColumn('action', 'pages.other_income.components.button_action')
            ->rawColumns(['code', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(OtherIncome $model): QueryBuilder
    {
        $filterBankAccount  = request('account', null);
        $filterCategory     = request('category', null);
        $filterCostCategory = request('cost_category', null);
        
        $query = $model->newQuery()
            ->select('other_incomes.*', 'other_income_categories.category_slug')
            ->leftJoin('other_income_categories', 'other_income_categories.id', '=', 'other_incomes.other_income_category_id');

        if (!is_null($filterBankAccount)) $query->where('bank_account_id', $filterBankAccount);
        if (!is_null($filterCategory)) $query->where('other_income_categories.category_slug', $filterCategory);

        if (!is_null($filterCostCategory) && $filterCostCategory !== 'all') {
            $query->where('outcome_type_id', $filterCostCategory);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('other-income-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('print')
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
                ->orderable(false)
                ->searchable(false),
            Column::make('date'),
            Column::make('bank_account_id')
                ->title('Bank Account')
                ->searchable(false),
            Column::make('amount'),
            Column::make('other_income_category_id')
                ->title('Category')
                ->searchable(false),
            Column::make('outcome_type_id')
                ->title('Cost Category')
                ->searchable(false),
            Column::make('code'),
            Column::make('description')
                ->orderable(false),
            Column::computed('recipient'),
            Column::computed('action')
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
        return 'OtherIncome_' . date('YmdHis');
    }
}
