<button type="button" class="btn btn-success waves-effect waves-light btn-rounded" data-toggle="modal"
    data-target="#createModal">
    <i class="mdi mdi-plus mr-1"></i> Add New</button>


<div id="createModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" id="form-create" class="form-parsley" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="po-stock">PO<span class="text-danger">*</span></label>
                            <br>
                            <select id="po-stock" class="form-control select2" name="po_stock">
                                {!! $poStocks !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="product-sku">Product SKU<span class="text-danger">*</span></label>
                            <div id="product-sku"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="brand">Status<span class="text-danger">*</span></label>
                            <input id="brand" class="form-control" type="text" name="status" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="datetime">Datetime<span class="text-danger">*</span></label>
                            <input id="datetime" class="form-control" type="datetime-local" name="datetime"
                                value="{{ old('datetime') ?: date('Y-m-d H:i') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="warehouse">Warehouse<span class="text-danger">*</span></label>
                            <br>
                            <select id="warehouse" class="form-control select2" name="warehouse">
                                {!! selectGenerate('Warehouse', $warehouses, 'id', 'warehouse_name',
                                old('warehouse')) !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="quantity">Quantity<span class="text-danger">*</span>
                                <br>
                                <h6 class="text-muted">Masukkan 0 untuk SKU yang belum sampai.</h6>
                            </label>
                            <div id="form-qty-area"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="notes">Notes</label>
                            <textarea id="notes" class="form-control" rows="3"
                                name="notes">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Add</button>
                </div>

            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


@push('scripts')
<script>
    $(function () {
        $('#po-stock').change(function (e) {
            let poStockId = $(this).val();

            // bersihkan #form-qty-area untuk inputan baru
            $('#form-qty-area').html('');

            $.ajax({
                type: "get",
                url: "{{ route('po_stock.get_po_products') }}",
                data: {
                    'po_stock_id': poStockId
                },
                success: function (response) {
                    $('#product-sku').html(response.list);

                    $.each(response.products, function (indexInArray, valueOfElement) {
                        let product = valueOfElement[0]
                        let unit = valueOfElement[1]
                        let detailPerProduct = valueOfElement[2]

                        addQtyForm(product.sku, product.id, detailPerProduct
                            .remaining_qty, unit.id, unit.unit_name)
                    });
                }
            });
        });

        // tambahkan form untuk quantity tiap sku pada PO
        function addQtyForm(sku, productId, qty, unitId, unitName) {
            let newElement = `<div class="form-group">
                                <div class="col-12 validate-input">
                                    <h6>Qty for ${sku} (${unitName})<span class="text-danger">*</span></h6>
                                    <input type="text" class="form-control autonumber" name="quantity[${productId}]" value="${qty}" max="${qty}" autocomplete="off">
                                    <input type="hidden" name="unit[${productId}]" value="${unitId}">
                                </div>
                            </div>`;

            $('#form-qty-area').append(newElement);
        }


        $('#form-create').submit(function (e) {
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
                url: '{{ route('product.inventory-in.store') }}',
                data: $('#form-create').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#createModal').modal('hide');
                    $('#form-create')[0].reset();
                    $('.help-block').remove()

                    $.toast({
                        heading: "Success!",
                        text: 'New Inventory In data successfully added.',
                        position: "top-right",
                        loaderBg: "#5ba035",
                        icon: "success",
                        hideAfter: 3e3,
                        stack: 1
                    })

                    window.location.href =
                        "{{ route('product.inventory-in.index') }}"
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

                    $.toast({
                        heading: "Something went wrong!",
                        text: response.message,
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
@endpush
