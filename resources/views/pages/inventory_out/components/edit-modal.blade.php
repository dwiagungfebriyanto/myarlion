<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="form-update" data-url="{{ route('product.inventory-out.index') }}"
                method="post">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-inventory-stock">Inventory<span class="text-danger">*</span></label>
                            <input id="edit-inventory-stock" class="form-control" type="text" readonly>
                            <input type="hidden" name="inventory_stock">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-status">Status<span class="text-danger">*</span></label>
                            <input id="edit-status" class="form-control" type="text" name="status">
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
                            <input id="edit-quantity" type="text" class="form-control" name="quantity">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-cost-per-unit">Purchase Cost/Unit<span class="text-danger">*</span></label>
                            <input id="edit-cost-per-unit" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-warehouse">Original Warehouse<span class="text-danger">*</span></label>
                            <input id="edit-warehouse" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-destination-warehouse">Destination Warehouse<span
                                    class="text-danger">*</span></label>
                            <br>
                            <select name="destination_warehouse" id="edit-destination-warehouse"
                                class="form-control select2">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-notes">Notes</label>
                            <textarea id="edit-notes" class="form-control" rows="3" name="notes"></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btn-add">Update</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->


@push('scripts')
    <script>
    $(function () {
        $('#edit-inventory-stock').change(function (e) {
            let selectedOption = $(this).find(':selected')
            let unit           = selectedOption.data('unit')
            let maxAmount      = selectedOption.data('amount')
            let maxWeight      = selectedOption.data('weight')
            let costPerUnit    = selectedOption.data('cost-per-unit')

            // manipulate Purchase Cost/Unit form
            $('#edit-cost-per-unit').val(costPerUnit + ' /' + selectedOption.data('unit-name'));

            // manipulate Warehouse form field
            $('#edit-warehouse').val(selectedOption.data('warehouse'));
        });

        $('#form-update').submit(function (e) {
            e.preventDefault();

            submitData();
        });

        function submitData() {
            $.ajax({
                type   : 'POST',
                url    : $('#form-update').attr('action'),
                data   : $('#form-update').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#editModal').modal('hide');
                    $('#form-update')[0].reset();
                    $('.help-block').remove()
                    // $('#inventory-out-datatable').DataTable().ajax.reload();

                    toastSuccess('Inventory Out data updated.')

                    window.location.href = "{{ route('product.inventory-out.index') }}"
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

                            $(`#form-update`).find(`[name="${index}"]`).parent().append(
                                errorMessage);
                        });
                    }

                    toastDanger('Something went wrong!')
                }
            });
        }
    });

</script>
@endpush
