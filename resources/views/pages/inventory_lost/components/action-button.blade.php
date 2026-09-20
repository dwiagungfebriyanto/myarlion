<div class="button-list">
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-info btn-detail" data-toggle="modal"
        data-target="#detailModal" data-url="{{ route('product.inventory-lost.show', $inventoryLost) }}"><i
            class="fas fa-eye"></i></button>

    @can('edit inventory lost')
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit" data-toggle="modal"
        data-target="#editModal" data-url="{{ route('product.inventory-lost.edit', $inventoryLost) }}">
        <i class=" fas fa-pen-alt"></i></button>
    @endcan

    @can('delete inventory lost')
    <form action="{{ route('product.inventory-lost.destroy', $inventoryLost) }}" method="post"
        style="display:inline">
        @csrf
        @method('delete')
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger sa-warning">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
    @endcan
</div>


@push('scripts')
<script>
    $(function () {
        $('.btn-detail').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                dataType: "json",
                success: function (response) {
                    let {
                        productSkuFormat, 
                        inventoryLost, 
                        quantityValue, 
                        sourceInventory,
                        warehouse
                    } = response;

                    let inventoryType = sourceInventory.stock_bucket === 'sample' ? 'Sample' : 'Stock';
                    
                    $('#detail-product').text(productSkuFormat);
                    $('#detail-status').text(inventoryLost.status);
                    $('#detail-datetime').text(inventoryLost.datetime);
                    $('#detail-quantity').text(quantityValue);
                    $('#detail-inventory').text(`${inventoryType} #${sourceInventory.id}`);
                    $('#detail-warehouse').text(warehouse.warehouse_name);
                    $('#detail-notes').text(inventoryLost.notes);
                }
            });
        });

        $('.btn-edit').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    let inventoryLost = response.inventoryLost;

                    $('#form-edit').attr('action', $('#form-edit').data('url') + `/${inventoryLost.id}`);
                    $('#edit-inventory-stock').data('selected', inventoryLost.inventory_stock_id);
                    $('#edit-status').val(inventoryLost.status);
                    $('#edit-datetime').val(response.inventoryLostDatetime);
                    $('#edit-quantity').val(response.inventoryLostUnitValue);
                    $('#edit-unit').val(response.unit.unit_name);
                    $('#edit-warehouse').val(response.warehouse.warehouse_name);
                    $('#edit-notes').val(inventoryLost.notes);
                }
            });
        });

        $(".sa-warning").click(function () {
            let element = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(function (t) {
                if (t.value === true) {
                    $.ajax({
                        type   : 'POST',
                        url    : element.parent().attr('action'),
                        data   : element.parent().serialize(),
                        success: function (data) {
                            // Handle success response
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            // Handle error response
                            let response = xhr.responseJSON;

                            $.toast({
                                heading  : "Something went wrong!",
                                text     : "Failed to delete data.",
                                position : "top-right",
                                loaderBg : "#bf441d",
                                icon     : "error",
                                hideAfter: 3e3,
                                stack    : 1
                            })
                        }
                    });
                }
            })
        })
    });

</script>
@endpush
