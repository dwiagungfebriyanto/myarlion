<button type="button" class="btn btn-success waves-effect waves-light btn-rounded" data-toggle="modal"
    data-target="#createModal">
    <i class="mdi mdi-plus mr-1"></i> Add New
</button>


<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" action="{{ route('product.inventory-mutation.store') }}"
                id="form-create" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div style="border: 2px solid gray; border-radius: 3px; padding: 10px">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="warehouse">Warehouse</label>
                            <div class="col-md-10">
                                <select id="warehouse" class="form-control select2" name="warehouse">
                                    {!! selectGenerate('Warehouse', $warehouses, 'id', 'warehouse_name') !!}
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="supplier">Supplier</label>
                            <div class="col-md-10">
                                <select id="supplier" class="form-control select2" name="supplier">
                                    {!! selectGenerate('supplier', $suppliers, 'id', 'supplier_name') !!}
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="stock">
                                Stock<span class="text-danger">*</span>
                            </label>
                            <div class="col-md-10">
                                <select id="stock" class="form-control select2" name="stock" disabled>
                                </select>
                            </div>

                            <input type="hidden" id="sku" name="sku">
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="qty">
                                Quantity<span class="text-danger">*</span>                                
                            </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control autonumeric" id="qty" name="quantity">
                            </div>
                            <div class="col-md-2">
                                <p class="unit-text"></p>
                            </div>
                        </div>
                    </div>

                    <h5>Mutate to:</h5>

                    <div style="border: 2px solid gray; border-radius: 3px; padding: 10px">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="warehouse-destination">
                                Warehouse<span class="text-danger">*</span>
                            </label>
                            <div class="col-md-10">
                                <select id="warehouse-destination" class="form-control select2" name="warehouse_destination">
                                    {!! selectGenerate('Warehouse', $warehouses, 'id', 'warehouse_name') !!}
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="new-sku">
                                New SKU<span class="text-danger">*</span>
                            </label>
                            <div class="col-md-10">
                                <select id="new-sku" class="form-control select2" name="new_sku">
                                    <option disabled selected>-- Select SKU --</option>

                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            data-unit="{{ ($product->unit_id === 1) ? 'Kg' : 'Pcs' }}">
                                            {{ $product->skuFormat() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="new-qty">
                                Quantity<span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control autonumeric" id="new-qty" name="new_quantity">
                            </div>
                            <div class="col-md-2">
                                <p class="unit-text-new"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label" for="price">
                                Price<span class="text-danger">*</span>
                            </label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="price">IDR</span>
                                    </div>
                                    <input type="text" class="form-control autonumeric disableEnterSubmit"
                                        aria-label="price" aria-describedby="price" name="price">
                                </div>
                            </div>
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
                        supplier_id: supplier,
                        with_sample: 'false'
                    },
                    success: function (response) {
                        $('#stock').removeAttr('disabled');
                        $('#stock').html(response.options);
                    }
                })
            });

            $('#stock').change(function (e) { 
                e.preventDefault();
                let selectedData = $(this).find(':selected');
                let unit = selectedData.data('unitName');
                let productId = selectedData.data('productId');

                $('.unit-text').text(unit);
                $('#sku').val(productId);
            });

            $('#new-sku').change(function (e) {
                let unit = $(this).find(':selected').data('unit');

                $('.unit-text-new').text(unit);
            });

            $('#form-create').submit(function (e) {
                e.preventDefault();

                unformatNumeric($('#form-create #qty'))
                unformatNumeric($('#form-create [name="price"]'))

                $.ajax({
                    type: 'POST',
                    url: $('#form-create').attr('action'),
                    data: $('#form-create').serialize(),
                    success: function (data) {
                        // Handle success response
                        $('#createModal').modal('hide');
                        $('#form-create')[0].reset();
                        $('.help-block').remove();

                        toastSuccess('New Inventory Mutation data successfully added.');

                        window.location.reload()
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

                                $(`#form-create`).find(`[name="${index}"]`).parent().append(
                                    errorMessage);
                            });
                        }

                        toastDanger();
                    }
                });
            });
        });

    </script>
@endpush
