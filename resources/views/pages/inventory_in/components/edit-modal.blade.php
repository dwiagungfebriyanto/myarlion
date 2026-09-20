<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" id="form-edit" method="post">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-po-stock">PO<span class="text-danger">*</span></label>
                            <input id="edit-po-stock" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-product-sku">Product SKU<span class="text-danger">*</span></label>
                            <input id="edit-product-sku" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-status">Status<span class="text-danger">*</span></label>
                            <input id="edit-status" class="form-control" type="text" name="status">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-datetime">Datetime<span class="text-danger">*</span></label>
                            <input id="edit-datetime" class="form-control" type="datetime-local" name="datetime"
                                value="{{ old('datetime') ?: date('Y-m-d H:i') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-warehouse">Warehouse<span class="text-danger">*</span></label>
                            <br>
                            <select id="edit-warehouse" class="form-control select2" name="warehouse">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-quantity">Quantity<span class="text-danger">*</span></label>
                            <input id="edit-quantity" type="text" class="form-control autonumber" name="quantity">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-unit">Unit<span class="text-danger">*</span></label>
                            <input id="edit-unit" name="unit_name" type="text" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-notes">Notes</label>
                            <textarea id="edit-notes" class="form-control" rows="3" name="notes"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-purchase-cost">
                                Purchase Cost<span class="text-danger">*</span>
                                <span class="text-muted" style="font-size: x-small;">(Total keseluruhan)</span>
                            </label>
                            <input id="edit-purchase-cost" type="text" data-a-sign="Rp "
                                class="form-control autonumber disableEnterSubmit" name="purchase_cost">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                </div>

            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
    $(function () {        
        $('#editModal').on('editModal', function () {
            $('#form-edit')[0].reset();
        });

        $('#edit-quantity').change(function (e) {
            let poStockId = $('#edit-po-stock').data('po-stock-id');
            let productId = $('#edit-product-sku').data('product-id');
            let qty = $(this).val();

            $.ajax({
                type: "get",
                url: "{{ route('po_stock.get_po_product_purchase_cost') }}",
                data: {
                    po_stock_id: poStockId,
                    product_id: productId,
                    qty: qty,
                },
                dataType: "json",
                success: function (response) {
                    $('#edit-purchase-cost').val(response);
                }
            });
        });


        $('#form-edit').submit(function (e) {
            e.preventDefault();

            $('.autonumber').each(function (index, element) {
                let value = $(this).autoNumeric().autoNumeric('get');
                $(this).val(value);
            });

            submitData();
        });

        function submitData() {
            $.ajax({
                type: 'POST',
                url: $('#form-edit').attr('action'),
                data: $('#form-edit').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#editModal').modal('hide');
                    $('#form-edit')[0].reset();
                    $('.help-block').remove()

                    $.toast({
                        heading: "Success!",
                        text: 'Inventory In data successfully updated.',
                        position: "top-right",
                        loaderBg: "#5ba035",
                        icon: "success",
                        hideAfter: 3e3,
                        stack: 1
                    })

                    window.location.href = "{{ route('product.inventory-in.index') }}"
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    $('.help-block').remove()

                    let response = xhr.responseJSON;

                    if (!$.isEmptyObject(response)) {

                        $.each(response.errors, function (index, value) {
                            let errorMessage = `<span class="help-block">
                                                    <mdall>${value}</mdall>
                                                </span>`;

                            $(`#form-edit`).find(`[name="${index}"]`).parent().append(
                                errorMessage);
                        });
                    }

                    $.toast({
                        heading: "Something went wrong!",
                        text: "Change a few things up and try submitting again.",
                        position: "top-right",
                        loaderBg: "#bf441d",
                        icon: "error",
                        hideAfter: 3e3,
                        stack: 1
                    })
                }
            });
        }
    });

</script>
