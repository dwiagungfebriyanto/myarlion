<div id="addStatementStock" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAddStock" action="{{ route('job_statement.store_job_stock', $job) }}"
                class="parsley-form" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Add <span class="stockTypeTitle"></span></h4>
                </div>

                <div class="modal-body">
                    <input type="text" name="is_sample" id="isSample" value="" hidden>
                    
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>Warehouse<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 warehouse" name="warehouse" id="warehouse" required>
                                {!! selectGenerate('warehouse', $warehouses, 'id', 'warehouse_name', old('warehouse')) !!}
                            </select>

                            @error('warehouse')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>Supplier</label>
                            <br>
                            <select class="form-control select2 supplier" name="supplier" id="supplier">
                                {!! selectGenerate('Supplier', $suppliers, 'id', 'supplier_name', old('supplier')) !!}
                            </select>

                            @error('supplier')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>
                                <span class="stockTypeTitle"></span> Product
                                <span class="text-danger">*</span>
                            </label>
                            <br>
                            <select class="form-control select2 inventory_stock_id" name="inventory_stock_id"
                                id="inventory_stock_id" required disabled>
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
                                    <input type="number" class="form-control quantity" id="quantity" name="quantity"
                                        placeholder="0" step="0.01" value="{{ old('quantity') }}" required>

                                    <input type="hidden" id="stock-validation" name="stock"
                                        value="{{ old('stock') }}">
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
                                    <input class="form-control autonumber price" type="text" id="price" name="price"
                                        placeholder="0" value="{{ old('price') }}" data-a-sign=""
                                        data-a-sep="." data-a-dec="," required>

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
                            <input class="form-control autonumber total" type="text" id="total" name="total"
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
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Add</button>
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
            $('#warehouse, #supplier, #inventory_stock_id').select2({
                allowClear: true,
                width: '100%',
            });

            let withSampleParam = '';

            $('.btnAddStock').click(function (e) { 
                e.preventDefault();
                withSampleParam = $(this).data('with-sample');

                let stockType = $(this).data('stock-title');
                $('.stockTypeTitle').text(stockType);

                let isSample = stockType === 'Sample' ? 1 : 0;
                $('#isSample').val(isSample);

                $('#formAddStock #warehouse, #formAddStock #inventory_stock_id')
                    .val(null)
                    .trigger('change');
            });

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
                        with_sample: withSampleParam
                    },
                    success: function (response) {
                        $('#inventory_stock_id').removeAttr('disabled');
                        $(".inventory_stock_id").html(response.options);
                    }
                })
            });

            $('#inventory_stock_id').on('change', function () {
                let stock = $(this).find(':selected').data('stock');
                let unit = $(this).find(':selected').data('unit-name');
                let price = $(this).find(':selected').data('cost-per-unit');

                $('#formAddStock #stock').html(`${stock} ${unit}`);
                $('#formAddStock #stock-validation').val(stock);
                
                $('#price').autoNumeric('set', price);
            });

            $('#addStatementStock #price, #addStatementStock #quantity').on('keyup', function () {
                let qty = $('#addStatementStock #quantity').val();
                let price = $('#addStatementStock #price').autoNumeric('get');
                let total = qty * price;

                $('#formAddStock #total').autoNumeric('set', total);
            });

            $('#formAddStock').on('submit', function (e) {
                e.preventDefault();
                let qty = $('#addStatementStock #quantity').val()
                let price = $('#addStatementStock #price').autoNumeric('get');
                let total = $('#addStatementStock #total').autoNumeric('get');

                $('#addStatementStock #quantity').val(qty);
                $('#addStatementStock #price').val(price);
                $('#addStatementStock #total').val(total);

                $(this).unbind('submit').submit();
            });
        })

    </script>
@endpush
