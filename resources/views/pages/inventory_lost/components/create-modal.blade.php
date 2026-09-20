<button type="button" class="btn btn-success waves-effect waves-light btn-rounded" data-toggle="modal"
    data-target="#createModal">
    <i class="mdi mdi-plus mr-1"></i> Add New
</button>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('product.inventory-lost.store') }}" id="form-create" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="warehouse">Warehouse
                                <span class="text-danger">*</span></label>
                            <br>
                            <select name="warehouse" id="warehouse" class="form-control select2">
                                {!! selectGenerate('Warehouse', $warehouses, 'id', 'warehouse_name') !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="supplier">Supplier
                                <span class="text-danger">*</span></label>
                            <br>
                            <select name="supplier" id="supplier" class="form-control select2">
                                {!! selectGenerate('supplier', $suppliers, 'id', 'supplier_name') !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="inventory-stock">Inventory<span class="text-danger">*</span></label>
                            <br>
                            <select id="inventory-stock" class="form-control select2" name="inventory_stock" required>
                            </select>

                            @if($errors)
                                <span class="help-block">
                                    <mdall>{{ $errors->first('inventory_stock') }}</mdall>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="brand">Status<span class="text-danger">*</span></label>
                            <input id="brand" class="form-control" type="text" name="status"
                                value="{{ old('status') }}" autocomplete="off">

                            @if($errors)
                                <span class="help-block">
                                    <mdall>{{ $errors->first('status') }}</mdall>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="datetime">Datetime<span class="text-danger">*</span></label>
                            <input id="datetime" class="form-control" type="datetime-local" name="datetime"
                                value="{{ old('datetime') ?: date('Y-m-d H:i') }}"
                                required>

                            @if($errors)
                                <span class="help-block">
                                    <mdall>{{ $errors->first('datetime') }}</mdall>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="quantity">Quantity<span class="text-danger">*</span></label>
                            <input id="quantity" type="text" class="form-control" name="quantity" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="unit">Unit<span class="text-danger">*</span></label>
                            <input id="unit" class="form-control" type="text" name="unit" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="notes">Notes</label>
                            <textarea id="notes" class="form-control" rows="3"
                                name="notes">{{ old('notes') }}</textarea>

                            @if($errors)
                                <span class="help-block">
                                    <mdall>{{ $errors->first('notes') }}</mdall>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Add</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@push('scripts')
    <script>
        $(function () {
            $('#supplier, #warehouse').on('change', function () {
                let warehouse = $('#warehouse').val();
                let supplier = $('#supplier').val();

                $.ajax({
                    url: "{{ route('api.inventory_stock.ready_stock_options') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        warehouse_id: warehouse,
                        supplier_id: supplier
                    },
                    success: function (response) {
                        $('#inventory-stock').removeAttr('disabled');
                        $("#inventory-stock").html(response.options);
                    }
                })
            });

            // #inventory-stock ada di file create-modal
            $('#inventory-stock').change(function (e) {
                let selectedOption = $(this).find(':selected')
                let unit = selectedOption.data('unit')
                let maxAmount = selectedOption.data('amount')
                let maxWeight = selectedOption.data('weight')

                // manipulate Unit form field
                $('#unit').val(selectedOption.data('unit-name'));

                // manipulate Warehouse form field
                $('#warehouse').val(selectedOption.data('warehouse'));
            });

            $('#form-create').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '{{ route("product.inventory-lost.store") }}',
                    data: $('#form-create').serialize(),
                    success: function (data) {
                        // Handle success response
                        $('#createModal').modal('hide');
                        $('#form-create')[0].reset();
                        $('.help-block').remove()
                        // $('#inventory-lost-datatable').DataTable().ajax.reload();

                        $.toast({
                            heading: "Success!",
                            text: 'New Inventory Lost data successfully added.',
                            position: "top-right",
                            loaderBg: "#5ba035",
                            icon: "success",
                            hideAfter: 3e3,
                            stack: 1
                        })

                        window.location.href =
                            "{{ route('product.inventory-lost.index') }}"
                    },
                    error: function (xhr, status, error) {
                        // Handle error response
                        $('.help-block').remove()

                        let response = xhr.responseJSON;

                        if (!$.isEmptyObject(response)) {

                            console.log(response.errors);

                            $.each(response.errors, function (index, value) {
                                let errorMessage = `<span class="help-block">
                                                    <mdall>${value}</mdall>
                                                </span>`;

                                $(`#form-create`).find(`[name="${index}"]`).parent().append(
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
            });
        });

    </script>
@endpush
