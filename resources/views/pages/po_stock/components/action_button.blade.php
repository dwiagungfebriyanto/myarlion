<div class="button-list">
    <a href="{{ route('po_stock.detail', $purchaseOrder) }}" target="_blank"
        class="btn btn-icon btn-sm waves-effect waves-light btn-info">
        <i class="fas fa-info-circle"></i> Detail
    </a>

    @can('edit PO stock')
        @if(!$purchaseOrder->iscomplete())
            @if($disableEditButton)
                <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btnEdit"
                    disabled>
                    <i class="fas fa-pen-alt"></i> Edit
                </button>
            @else
                <a href="{{ route('purchase_orders.edit', $purchaseOrder) }}"
                    class="btn btn-icon btn-sm waves-effect waves-light btn-warning btnEdit">
                    <i class="fas fa-pen-alt"></i> Edit
                </a>
            @endif
        @endif

        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-success btn-edit-status"
            data-target="#changeStatusModal" data-toggle="modal" onclick="setEditStatusModal(this)"
            data-url="{{ route('po_stock.change_status', $purchaseOrder) }}"
            data-status="{{ $purchaseOrder->status }}">

            <i class="fas fa-check-square"></i> Change Status
        </button>
    @endcan

    <a href="{{ route('po_stock.history', $purchaseOrder) }}" target="_blank"
        class="btn btn-icon btn-sm waves-effect waves-light btn-info">
        <i class="fas fa-history"></i> History
    </a>

    @can('delete PO stock')
        @if(!$purchaseOrder->iscomplete())
            <form action="{{ route('purchase_orders.destroy', $purchaseOrder) }}" method="post" style="display:inline">
                @csrf
                @method('delete')

                <button type="button" {{ $disableEditButton }}
                    class="btn btn-icon btn-sm waves-effect waves-light btn-danger sa-warning"
                    data-datatable="#po-datatable"
                    onclick="saDelete(this, true)">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </form>
        @endif
    @endcan
</div>

<script>
    function setEditStatusModal(e) {
        let currentStatus = $(e).data('status');

        $('#form-status').attr('action', $(e).data('url'));
        
        $('#change-status').find(`option[value="${currentStatus}"]`)
            .prop('selected', true)
            .trigger('change');
    }
</script>
