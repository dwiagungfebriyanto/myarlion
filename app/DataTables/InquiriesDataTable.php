<?php

namespace App\DataTables;

use App\Models\Country;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InquiriesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('date', function ($inquiry) {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $inquiry->date)->format('d/m/Y');
            })
            ->editColumn('country_name', function ($inquiry) {
                if ($inquiry->country_name == null) {
                    return '';
                } else {
                    return $inquiry->country_code . ' - ' . $inquiry->country_name;
                }
            })
            ->editColumn('city', function ($inquiry) {
                if ($inquiry->city == null) {
                    return '';
                } else {
                    return $inquiry->city;
                }
            })
            ->addColumn('action', 'pages.inquiry.components.action-button')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Inquiry $model): QueryBuilder
    {
        $filterMonth     = request('filter_month');
        $filterMarketing = request('filter_marketing');
        $filterStatus    = request('filter_status');

        $query = $model->newQuery()
            ->join('users', 'inquiries.user_id', '=', 'users.id')
            ->join('countries', 'inquiries.country_code', '=', 'countries.id')
            ->select('inquiries.*', 'users.name as marketing', 'countries.country_code', 'countries.country_name');

        if (!is_null($filterMonth)) {
            $query = $query->where('date', 'like', "$filterMonth%");
        }

        if (!is_null($filterMarketing) && $filterMarketing !== 'all') {
            $query = $query->where('user_id', $filterMarketing);
        }

        if (!is_null($filterStatus) && $filterStatus !== 'all') {
            $query = $query->where('status', $filterStatus);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('inquiries-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
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
            Column::make('id')->title('ID'),
            Column::make('date')->title('Date'),
            Column::make('name', 'inquiries.name')->title('Name'),
            Column::make('country_name', 'countries.country_name')->title('Country'),
            Column::make('city')->title('City'),
            Column::make('marketing', 'users.name')->title('Marketing'),
            Column::make('status', 'status')->title('Status'),
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
        return 'Inquiries_' . date('YmdHis');
    }
}
