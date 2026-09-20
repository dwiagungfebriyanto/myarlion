<div class="button-list">
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-info btn-detail" data-toggle="modal"
        data-target="#detailModal" data-url="{{ route('product.inventory-in.show', $id) }}"><i
            class="fas fa-eye"></i></button>

    @can('edit inventory in')
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit" data-toggle="modal"
        data-target="#editModal" data-url="{{ route('product.inventory-in.edit', $id) }}"><i
        class="fas fa-pen-alt"></i></button>
    @endcan

    {{-- <form action="{{ route('product.inventory-in.destroy', $id) }}" method="post"
        style="display:inline">
        @csrf
        @method('delete')

        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger sa-warning">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form> --}}
</div>

<script>
    $(document).ready(function () {
        $('.btn-detail').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                dataType: "json",
                success: function (response) {
                    $('#detail-product').text(response.productSkuFormat);
                    $('#detail-status').text(response.inventoryIn.status);
                    $('#detail-datetime').text(response.inventoryIn.datetime);
                    $('#detail-quantity').text(response.quantityValue);
                    $('#detail-warehouse').text(response.warehouse.warehouse_name);
                    $('#detail-notes').text(response.inventoryIn.notes);
                    $('#detail-purchase-cost').text(response.inventoryIn.purchase_cost);
                    $('#detail-purchase-cost-per-unit').text(response.inventoryIn
                        .purchase_cost_per_unit);
                }
            });
        });

        $('.btn-edit').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    if (response.unit.unit_name === 'Kg') {
                        $('#edit-weight').prop('disabled', false)
                    } else {
                        $('#edit-amount').prop('disabled', false)
                    }

                    $('#form-edit').attr('action', response.updateActionUrl);
                    // $('#edit-product-sku').html(response.skuOptions);
                    $('#edit-product-sku').val(response.skuData);
                    $('#edit-status').val(response.inventoryIn.status);
                    $('#edit-datetime').val(response.inventoryInDatetime);
                    $('#edit-unit').val(response.unit.unit_name);
                    $('#edit-quantity').val(response.inventoryInQuantity);
                    $('#edit-warehouse').html(response.warehouseOptions);
                    $('#edit-notes').text(response.inventoryIn.notes);
                    $('#edit-purchase-cost').val(response.inventoryIn.purchase_cost);
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
                    element.parent().submit()
                }
            })
        })

    });

</script>
