<?php

namespace App\DataTables;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'pages.account.components.action-button')
            ->setRowId('id')
            ->addColumn('signature', 'pages.account.components.show-button')
            ->rawColumns(['action', 'signature']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'users.*',
                'roles.name as role_name',
            );
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
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

            Column::make('id', 'id')
                ->title('ID')->addClass('align-middle'),
            Column::make('name', 'name')
                ->title('Name')->addClass('align-middle'),
            Column::make('username', 'username')
                ->title('Username')->addClass('align-middle'),
            Column::make('email', 'email')
                ->title('Email')->addClass('align-middle'),
            Column::make('position', 'position')
                ->title('Position')->addClass('align-middle'),
            Column::make('role_name', 'role_id')
                ->title('Role')->addClass('align-middle'),
            Column::computed('signature')->title('Signature')->addClass('align-middle'),
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
        return 'Users_' . date('YmdHis');
    }
}
