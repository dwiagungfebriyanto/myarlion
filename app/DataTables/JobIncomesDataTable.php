<?php

namespace App\DataTables;

use App\Models\Currency;
use App\Models\JobIncome;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;

class JobIncomesDataTable extends DataTable
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
            ->editColumn('date', function (JobIncome $row) {
                return date('Y-m-d H:i', strtotime($row->date));
            })
            ->editColumn('payment', function (JobIncome $row) {
                $currency = Currency::find($row->job->currency_id);

                return currencyFormat($row->payment, "$currency->currency_code ");
            })
            ->editColumn('outstanding', function (JobIncome $row) {
                $currency = Currency::find($row->job->currency_id);

                return currencyFormat($row->outstanding, "$currency->currency_code ");
            })
            ->editColumn('bank_account_id', function (JobIncome $row) {
                return $row->bankAccount->bankAccountLabel();
            })
            ->addColumn('action', function (JobIncome $row) {
                if ($row->job->statusIsOpen()) {
                    return view('pages.job.components.action.action-income', $row);
                }
            })
            ->addColumn('no', function () {
                static $i = 0;
                $i++;
                return $i;
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(JobIncome $model): QueryBuilder
    {
        $model = $model->newQuery();
        // $model->join('bank_accounts', 'job_incomes.bank_account_id', '=', 'bank_accounts.id');
        $model->where('job_id', $this->job_id);
        $model->orderBy('date');
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('jobincomes-table')
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
            Column::make('no')->title('No')->orderable(false),
            Column::make('date', 'date')->title('Date')->orderable(false),
            Column::make('payment', 'payment')->title('Payment')->orderable(false),
            Column::make('outstanding', 'outstanding')->title('Outstanding')->orderable(false),
            Column::make('bank_account_id')->title('Bank Account')->orderable(false),
            Column::make('action')
                ->width(60)
                ->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'JobIncomes_' . date('YmdHis');
    }
}
