<div class="button-list">
    @can('edit sales target')
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning" data-toggle="modal"
            data-target="#editModal" data-url="{{ route('sales_target_monthly.update', $id) }}"
            data-month="{{ date('Y-m', strtotime($month)) }}" data-marketing="{{ $user_id }}"
            data-sales-target="{{ $target }}" onclick="setEditModalData(this)">
            <i class="fas fa-pen-alt"></i>
        </button>
    @endcan

    @can('delete sales target')
        <form action="{{ route('sales_target_monthly.destroy', $id) }}" method="post"
            data-datatable="#salestargetmonthly-table" style="display:inline">
            @csrf
            @method('delete')

            <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger"
                onclick="saDelete(this)">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</div>
