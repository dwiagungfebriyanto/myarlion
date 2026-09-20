<div class="button-list">
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-info btn-detail" data-toggle="modal"
        data-target="#detailModal" data-url="{{ route('product.inventory-out.show', $id) }}"><i
            class="fas fa-eye"></i></button>

    @can('edit inventory out')
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit" data-toggle="modal"
        data-target="#editModal" data-url="{{ route('product.inventory-out.edit', $id) }}"><i
            class="fas fa-pen-alt"></i></button>
    @endcan

    @can('delete inventory out')
    <form action="{{ route('product.inventory-out.destroy', $id) }}" id="form-delete" method="post"
        style="display:inline">
        @csrf
        @method('delete')
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger sa-warning">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
    @endcan
</div>

<script>
    $(function () {
        $('.btn-detail').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                dataType: "json",
                success: function (response) {
                    $('#detail-product').text(response.productSkuFormat);
                    $('#detail-status').text(response.inventoryOut.status);
                    $('#detail-datetime').text(response.inventoryOut.datetime);
                    $('#detail-quantity').text(response.quantityValue);
                    $('#detail-warehouse').text(response.warehouse.warehouse_name);
                    $('#detail-original-warehouse').text(response.originalWarehouse.warehouse_name);
                    $('#detail-notes').text(response.inventoryOut.notes);
                    $('#detail-purchase-cost').text(response.purchaseCost);
                    $('#detail-purchase-cost-per-unit').text(response.purchaseCostPerUnit);
                }
            });
        });

        $('.btn-edit').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    let inventoryOut = response.inventoryOut;

                    $('#form-update').attr('action', $('#form-update').data('url') + '/' + inventoryOut.id);
                    $('#edit-inventory-stock').val(response.inventoryStockSku);
                    $('[name="inventory_stock"]').val(inventoryOut.original_stock_id);
                    $('#edit-status').val(inventoryOut.status);
                    $('#edit-datetime').val(response.inventoryOutDatetime);
                    $('#edit-unit').val(response.inventoryUnit.unit_name);
                    $('#edit-quantity').val(response.inventoryOutQuantity);
                    $('#edit-cost-per-unit').val(response.purchaseCostPerUnit);
                    $('#edit-warehouse').val(response.originalWarehouse.warehouse_name);
                    $('#edit-destination-warehouse').html(response.warehouseOptions);
                    $(`#edit-destination-warehouse option[value="${inventoryOut.warehouse_id}"]`).prop('selected', true)
                    $('#edit-notes').val(inventoryOut.notes);
                }
            });
        });

        $(".sa-warning").click(function (e) {
            e.preventDefault();

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
                        url    : $('#form-delete').attr('action'),
                        data   : $('#form-delete').serialize(),
                        success: function (data) {
                            // Handle success response
                            $('#inventory-out-datatable').DataTable().ajax.reload();

                            $.toast({
                                heading  : "Success!",
                                text     : 'Inventory Out data successfully deleted.',
                                position : "top-right",
                                loaderBg : "#5ba035",
                                icon     : "success",
                                hideAfter: 3e3,
                                stack    : 1
                            })
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
