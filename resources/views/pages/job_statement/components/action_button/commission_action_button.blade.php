<div class="d-flex my-2 justify-content-center">
    @can('job statement edit sales commission')
    <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btn-edit-commission" type="button"
        data-toggle="modal" data-target="#editSalesCommission"
        data-url="{{ route('job_statement.update_job_commission', $commission) }}"
        data-date="{{ $commission->release_date }}"
        data-marketing="{{ $commission->user_id }}"
        data-percentage="{{ $commission->percentage }}"
        data-nominal="{{ $commission->nominal }}"
        data-note="{{ $commission->note }}">

        <i class="fas fa-pen-alt"></i>
    </button>
    @endcan

    @can('job statement delete sales commission')
    <form action="{{ route('job_statement.destroy_job_commission', $commission) }}" method="POST">
        @csrf
        @method('DELETE')

        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light sa-delete" onclick="saDelete(this)">
            <i class="fas fa-trash-alt"></i></button>
    </form>
    @endcan
</div>
