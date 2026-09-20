<div class="d-flex my-2 justify-content-center">
    @can('job statement edit PO stock')
    <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btnEditPoStock" type="button"
        data-toggle="modal" data-target="#editPoStockModal"
        data-url="{{ route('job_statement.update_job_po_stock', $jobPoStock) }}"
        data-po-stock-id="{{ $jobPoStock->po_stock_id }}"
        data-product-id="{{ $jobPoStock->product_id }}"
        data-qty="{{ $jobPoStock->qty }}"
        data-amount="{{ $jobPoStock->amount }}"
        data-note="{{ $jobPoStock->note }}">

        <i class="fas fa-pen-alt"></i>
    </button>
    @endcan

    @can('job statement delete PO stock')
    <form action="{{ route('job_statement.destroy_job_po_stock', $jobPoStock) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light sa-delete" onclick="saDelete(this)">
            <i class="fas fa-trash-alt"></i></button>
    </form>
    @endcan
</div>
