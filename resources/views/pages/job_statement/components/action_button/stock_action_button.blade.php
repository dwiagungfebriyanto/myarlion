<div class="d-flex my-2 justify-content-center">
    @can('job statement delete stock')
    <form action="{{ route('job_statement.destroy_job_stock', $stock) }}" method="post">
        @csrf
        @method('delete')

        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="saDelete(this)">
            <i class="fas fa-trash-alt"></i></button>
    </form>
    @endcan
</div>