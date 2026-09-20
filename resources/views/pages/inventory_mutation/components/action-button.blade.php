<div class="button-list">
    @can('edit inventory mutation')
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit" data-toggle="modal"
        data-target="#editModal" data-url="{{ route('product.inventory-mutation.edit', $inventoryMutation) }}">
        <i class=" fas fa-pen-alt"></i></button>
    @endcan

    @can('delete inventory mutation')
    <form action="{{ route('product.inventory-mutation.destroy', $inventoryMutation) }}" method="post"
        style="display:inline">
        @csrf
        @method('delete')
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger sa-warning">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
    @endcan
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    $(function () {
        $('.btn-edit').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    let inventoryMutation = response.inventoryMutation;

                    $('#form-edit').attr('action', response.actionUrl);
                    $('#edit-sku').val(response.originalSku);
                    $('#edit-sku-value').val(inventoryMutation.original_product_id);
                    $('#edit-stock').val(response.originalStock);
                    $('#edit-stock-value').val(inventoryMutation.original_stock_id);
                    $('#edit-qty').val(inventoryMutation.qty_mutation);
                    $('#edit-quantity').val(response.inventoryLostUnitValue);
                    $('#edit-unit-text').text(response.stockUnit.unit_name);
                    // mutate to
                    $(`#edit-warehouse-destination`).val(response.warehouse.warehouse_name);
                    $(`#edit-new-sku`).html(response.productOptions);
                    $('#edit-new-qty').val(inventoryMutation.qty);
                    $('#edit-unit-text-new').text(response.mutationUnit.unit_name);
                    $('#edit-price-field').val(inventoryMutation.price);
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
                            // $('#inventory-mutation-datatable').DataTable().ajax.reload();

                            $.toast({
                                heading  : "Success!",
                                text     : 'Inventory Mutation data successfully deleted.',
                                position : "top-right",
                                loaderBg : "#5ba035",
                                icon     : "success",
                                hideAfter: 3e3,
                                stack    : 1
                            })

                            window.location.href = "{{ route('product.inventory-mutation.index') }}"
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
