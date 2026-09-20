<div id="editStatementStock" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditStock" action="" method="post" class="parsley-form">
                @csrf
                @method('put')

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit Stock</h4>
                </div>

                <div class="modal-body">
                    <input type="text" name="job_id" id="edit_job_id" hidden>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>Supplier<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 supplier" name="supplier" id="edit_supplier" required>
                                {!! selectGenerate('Supplier', $suppliers, 'id', 'supplier_name') !!}
                            </select>

                            @error('supplier')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>Stock Product<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 inventory_stock_id" name="inventory_stock_id"
                                id="edit_inventory_stock_id" required disabled>
                                <option value="">-- Product --</option>
                            </select>

                            @error('inventory_stock_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label>Quantity<span class="text-danger">*</span></label>
                                    <input class="form-control autonumber quantity" type="text" id="edit_quantity"
                                        name="quantity" placeholder="0" data-a-sign="" data-a-sep="."
                                        data-a-dec="," value="{{ old('quantity') }}" required>

                                    <input type="number" id="edit_stock_validation" name="stock" value="{{ old('stock') }}" hidden>
                                    <p class="mb-0">
                                        <span class="text-danger">*</span>Stock : <span id="stock"></span>
                                    </p>
                                </div>
                                {{-- /.col-12 validate-input --}}

                                @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- /.form-group --}}
                        </div>
                        {{-- /.col-md-6 --}}

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label>Harga Satuan<span class="text-danger">*</span></label>
                                    <input class="form-control autonumber price" type="text" id="edit_price" name="price"
                                        placeholder="0" value="{{ old('price') }}" data-a-sign="" data-a-sep="." data-a-dec="," required>

                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label>Total Harga<span class="text-danger">*</span></label>
                            <input class="form-control autonumber total" type="text" id="edit_total" name="total"
                                placeholder="0" value="{{ old('total') }}" data-a-sign=""
                                data-a-sep="." data-a-dec="," required>

                            @error('total')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- /.modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        id="submitEditStock">Update</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    {{-- /.modal-dialog --}}
</div>
{{-- /.modal --}}


@push('scripts')
    <script>
        $(document).ready(function () {
            $('#editStatementStock').on('show.bs.modal', function () {
                $(this).find('#edit_supplier').trigger('change');
            });

            $('#edit_supplier').on('change', function () {
                let supplier = $(this).val();

                $.ajax({
                    url: "{{ route('jobStatement.get_stock') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function (response) {
                        $('#edit_inventory_stock_id').removeAttr('disabled');
                        $("#formEditStock .inventory_stock_id").html(
                            '<option value="">-- Select Product --</option>');

                        $.each(response, function (key, value) {
                            if (value.product.supplier_id == supplier) {
                                uniqueId = (value.inventory_in.po_stock != null) ?
                                    value.inventory_in.po_stock.unique_id :
                                    '';

                                $("#formEditStock .inventory_stock_id").append('<option value="' +
                                    value.id + '">' + value.product.sku +
                                    ' | ' + uniqueId +
                                    ' | ' + value.product.product_type_name +
                                    ' | ' + value.product.specification_name +
                                    ' | ' + value.product.packaging_name +
                                    ' | ' + value.warehouse.warehouse_name +
                                    '</option>');
                            }
                        });
                    }
                })
            });

            $('#formEditStock .inventory_stock_id').on('change', function () {
                let inventory_stock_id = $(this).val();

                $.ajax({
                    url: "/api/fetchStock/" + inventory_stock_id,
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        // job product
                        let stock = (response.unit.unit_name == 'Kg')
                            ? response.weight
                            : response.amount;
                        let unit = response.unit.unit_name;
                        let price = response.purchase_cost_per_unit.toFixed(2)
                            .replace(/\./g, ',')
                            .replace(/(\d)(?=(\d{3})+\,)/g, '$1.');

                        $('#edit_stock').html(stock + ' ' + unit);
                        $('#edit_stock_validation').val(stock);
                        $('#edit_price').val(price);
                    }
                })
            });

            $('#edit_price, #edit_quantity').on('keyup', function () {
                let qty = $('#edit_quantity').autoNumeric('get');
                let price = $('#edit_price').autoNumeric('get');
                let total = qty * price;

                $('#edit_total').autoNumeric('set', total);
            });

            $('#submitEditStock').on('click', function(e) {
                let qty = $('#edit_quantity').autoNumeric('get');
                let price = $('#edit_price').autoNumeric('get');
                let total = $('#edit_total').autoNumeric('get');

                $('#edit_quantity').val(qty);
                $('#edit_price').val(price);
                $('#edit_total').val(total);

                $('#formEditStock').submit();
            });
        })

    </script>
@endpush
