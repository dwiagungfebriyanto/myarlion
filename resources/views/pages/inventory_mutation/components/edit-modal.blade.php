<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" action="" id="form-edit" method="post">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div style="border: 2px solid gray; border-radius: 3px; padding: 10px">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="edit-sku">SKU</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="edit-sku" readonly>
                                <input type="hidden" name="sku" id="edit-sku-value">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="edit-stock">Stock</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="edit-stock" readonly>
                                <input type="hidden" name="stock" id="edit-stock-value">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="qty">Quantity</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control autonumeric" id="edit-qty" name="quantity">
                            </div>
                            <div class="col-md-2">
                                <p id="edit-unit-text"></p>
                            </div>
                        </div>
                    </div>

                    <h5>Mutate to:</h5>

                    <div style="border: 2px solid gray; border-radius: 3px; padding: 10px">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="edit-warehouse-destination">
                                Warehouse
                            </label>
                            <div class="col-md-10">
                                <input type="text" id="edit-warehouse-destination" name="warehouse_destination" class="form-control" disabled>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="edit-new-sku">New SKU</label>
                            <div class="col-md-10">
                                <select id="edit-new-sku" class="form-control select2" name="new_sku">
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="edit-new-qty">Quantity</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control autonumeric" id="edit-new-qty"
                                    name="new_quantity">
                            </div>
                            <div class="col-md-2">
                                <p id="edit-unit-text-new"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="edit-price">Price</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="edit-price">IDR</span>
                                    </div>
                                    <input type="text" id="edit-price-field"
                                        class="form-control autonumeric disableEnterSubmit" aria-label="price"
                                        aria-describedby="price" name="price">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@push('scripts')
<script>
    $(function () {
        $('#edit-new-sku').change(function (e) {
            let unit = $(this).find(':selected').data('unit');

            $('#edit-unit-text-new').text(unit);
        });

        $('#form-edit').submit(function (e) {
            e.preventDefault();

            unformatNumeric($('#edit-price-field'));

            submitData();
        });

        function submitData() {
            $.ajax({
                type: 'POST',
                url: $('#form-edit').attr('action'),
                data: $('#form-edit').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#form-edit')[0].reset();
                    $('.help-block').remove()
                    // $('#inventory-mutation-datatable').DataTable().ajax.reload();

                    try {
                        toastSuccess('Inventory mutation data successfully updated.');
                    } catch (error) {
                        
                    }

                    window.location.href = "{{ route('product.inventory-mutation.index') }}"
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

                    toastDanger("Something went wrong!");
                }
            });
        }
    });

</script>
@endpush