<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="form-edit" data-url="{{ route('product.inventory-lost.index') }}" method="post" class="parsley-examples">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit Inventory Lost</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-inventory-stock">Inventory<span class="text-danger">*</span></label>
                            <br>
                            <select id="edit-inventory-stock" data-selected="" class="form-control select2" disabled>
                            </select>
                            <input type="hidden" name="inventory_stock">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-status">Status<span class="text-danger">*</span></label>
                            <input id="edit-status" class="form-control" type="text" name="status"
                                value="" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-datetime">Datetime<span class="text-danger">*</span></label>
                            <input id="edit-datetime" class="form-control" type="datetime-local" name="datetime"
                                value="{{ old('datetime') ?: date('Y-m-d H:i') }}"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-quantity">Quantity<span class="text-danger">*</span></label>
                            <input id="edit-quantity" type="text" class="form-control" name="quantity" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-unit">Unit<span class="text-danger">*</span></label>
                            <input id="edit-unit" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-warehouse">Warehouse<span class="text-danger">*</span></label>
                            <input id="edit-warehouse" class="form-control" type="text"
                                value="" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-notes">Notes</label>
                            <textarea id="edit-notes" class="form-control" rows="3"
                                name="notes"></textarea>
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
        $('#editModal').on('shown.bs.modal', function () {
            $.ajax({
                type: "post",
                url: "{{ route('api.inventory_stock.ready_stock_options') }}",
                success: function (response) {
                    let editStockElement = $('#edit-inventory-stock');
                    let selectedOption = editStockElement.data('selected');


                    editStockElement.html(response.options);
                    $(`#edit-inventory-stock option[value="${selectedOption}"]`).attr('selected', 'selected');
                    $(`input[name="inventory_stock"]`).val(selectedOption);
                }
            });
        });

        $('#edit-inventory-stock').change(function (e) {
            let selectedOption = $(this).find(':selected')
            let unit           = selectedOption.data('unit')
            let unitName       = selectedOption.data('unit-name')
            let maxAmount      = selectedOption.data('amount')
            let maxWeight      = selectedOption.data('weight')

            // manipulate Unit form
            $('#edit-unit').val(unitName);

            // manipulate Warehouse form field
            $('#edit-warehouse').val(selectedOption.data('warehouse'));
        });

        $('#form-edit').submit(function (e) {
            e.preventDefault();

            $.ajax({
                type   : 'POST',
                url    : $('#form-edit').attr('action'),
                data   : $('#form-edit').serialize(),
                success: function (data) {
                    // Handle success response
                    location.reload();
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

                            $(`#form-edit`).find(`[name="${index}"]`).parent().append(errorMessage);
                        });
                    }

                    toastDanger();
                }
            });
        });
    });
</script>
@endpush
