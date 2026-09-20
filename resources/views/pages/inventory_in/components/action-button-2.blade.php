<div class="button-list">
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-info btn-detail" data-toggle="modal"
        data-target="#detailModal" title="Detail"
        data-url="{{ route('product.inventory-in.show', $inventoryIn->id) }}">
        <i class="fas fa-eye"></i></button>

    <a href="{{ route('inventory_in.history', $inventoryIn) }}" target="_blank" title="History"
        class="btn btn-icon btn-sm waves-effect waves-light btn-info">
        <i class="fas fa-history"></i>
    </a>

    @can('edit inventory in')
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit"
            data-toggle="modal" data-target="#editModal" title="Edit"
            data-url="{{ route('product.inventory-in.edit', $inventoryIn->id) }}"><i
                class="fas fa-pen-alt"></i></button>
    @endcan
</div>

<script>
    $(document).ready(function () {
        $('.btn-detail').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                dataType: "json",
                success: function (response) {
                    let {
                        poStock,
                        productSkuFormat,
                        inventoryIn,
                        quantityValue,
                        warehouse,
                    } = response;

                    let poUniqueID = poStock ? poStock.unique_id : '';
                    let poType = poStock ? poStock.po_type : '';

                    $('#detail-po-stock').text(`PO ${poType}: ${poUniqueID}`);
                    $('#detail-product').text(productSkuFormat);
                    $('#detail-status').text(inventoryIn.status);
                    $('#detail-datetime').text(inventoryIn.datetime);
                    $('#detail-quantity').text(quantityValue);
                    $('#detail-warehouse').text(warehouse.warehouse_name);
                    $('#detail-notes').text(inventoryIn.notes);
                    $('#detail-purchase-cost').text(inventoryIn.purchase_cost);
                    $('#detail-purchase-cost-per-unit').text(inventoryIn.purchase_cost_per_unit);
                }
            });
        });

        $('.btn-edit').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    let {
                        unit,
                        inventoryIn,
                        poStock,
                        updateActionUrl,
                        skuData,
                        inventoryInDatetime,
                        inventoryInQuantity,
                        warehouseOptions,
                    } = response;


                    if (unit.unit_name === 'Kg') {
                        $('#edit-weight').prop('disabled', false)
                    } else {
                        $('#edit-amount').prop('disabled', false)
                    }


                    let poType = poStock ? poStock.po_type : '';
                    let poStockUniqueId = poStock ? poStock.unique_id : '';

                    $('#form-edit').attr('action', updateActionUrl);
                    $('#edit-po-stock').val(`PO ${poType}: ${poStockUniqueId}`);
                    $('#edit-po-stock').data('po-stock-id', inventoryIn.po_stock_id);
                    $('#edit-product-sku').val(skuData);
                    $('#edit-product-sku').data('product-id', inventoryIn.product_id);
                    $('#edit-status').val(inventoryIn.status);
                    $('#edit-datetime').val(inventoryInDatetime);
                    $('#edit-unit').val(unit.unit_name);
                    $('#edit-quantity').val(inventoryInQuantity);
                    $('#edit-warehouse').html(warehouseOptions);
                    $('#edit-notes').text(inventoryIn.notes);
                    $('#edit-purchase-cost').val(inventoryIn.purchase_cost);
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
