<button type="button" class="btn btn-success waves-effect waves-light btn-rounded" data-toggle="modal"
    data-target="#createModal">
    <i class="mdi mdi-plus mr-1"></i> Add New</button>


<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('purchase_orders.store') }}" id="form-add" class="form-parsley" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="type">PO Type</label>

                            <select name="po_type" id="type" class="form-control select2" data-parsley-errors-container="#type-error" required>
                                {!! selectGenerate('PO Type', $poTypes, 'value', 'name') !!}
                            </select>
                            <div id="type-error"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="unique_id">Unique ID</label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="id-prefix"></span>
                                </div>
                                <input type="text" class="form-control" id="unique_id" name="unique_id"
                                    placeholder="Let empty for generated ID" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="main-category">Main Category<span class="text-danger">*</span></label>
                            <select id="main-category" class="form-control select2" name="main_category" data-parsley-errors-container="#category-error" required>
                                {!! selectGenerate('Main Category', $mainCategories, 'id', 'main_category_name') !!}
                            </select>
                            <div id="category-error"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="supplier">Supplier<span class="text-danger">*</span></label>
                            <select id="supplier" class="form-control select2" name="supplier" data-parsley-errors-container="#supplier-error" required>
                            </select>
                            <div id="supplier-error"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="product-sku">Product SKU<span class="text-danger">*</span></label>
                            <select id="product-sku" class="form-control select2 select2-multiple" name="product_id[]"
                                data-parsley-errors-container="#product-error" required multiple>
                            </select>
                            <div id="product-error"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>Quantity<span class="text-danger">*</span></label>
                            <div id="form-qty-area"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>Unit Price<span class="text-danger">*</span></label>
                            <div id="form-price-area"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="shipping-cost">Shipping Cost</label>
                            <input id="shipping-cost" type="text" placeholder="Ongkir" data-a-sign="Rp "
                                class="form-control autonumber disableEnterSubmit autoCalculateInput"
                                name="shipping_cost" value="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="additional-expenses">Additional Expenses</label>
                            <input id="additional-expenses" type="text" data-a-sign="Rp "
                                class="form-control autonumber autoCalculateInput" name="additional_expenses" value="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="total">Total<span class="text-danger">*</span></label>
                            <input id="total" type="text" data-a-sign="Rp " class="form-control autonumber"
                                name="total" value="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="note">Notes<span class="text-danger">*</span></label>
                            <textarea id="note" class="form-control" rows="3" name="note"></textarea>
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
        let supplierRequest = null;
        let productRequest = null;
        let supplierRequestToken = 0;
        let productRequestToken = 0;

        function abortRequest(request) {
            if (request && request.readyState !== 4) {
                request.abort();
            }
        }

        function clearProductSelection() {
            $('#product-sku').html('');
            $('#form-qty-area').html('');
            $('#form-price-area').html('');
        }

        $('#main-category, #supplier').change(function (e) {
            $('#form-qty-area').html('');
            $('#form-price-area').html('');

            const mainCategoryValue = $('#main-category').val();
            const supplierCode = $('#supplier').find(':selected').data('code');

            if (!mainCategoryValue || !supplierCode) {
                $('#id-prefix').text('');
                return;
            }

            const mainCategoryId = String(mainCategoryValue).padStart(3, '0');
            $('#id-prefix').text(`${mainCategoryId}/${supplierCode}/`);
        });

        $('#main-category').change(function (e) {
            const mainCategoryId = $(this).val();
            const currentToken = ++supplierRequestToken;

            abortRequest(supplierRequest);
            abortRequest(productRequest);
            clearProductSelection();
            $('#id-prefix').text('');

            $('#supplier').html('<option selected disabled>-- Select Supplier --</option>');

            if (!mainCategoryId) {
                return;
            }

            supplierRequest = $.ajax({
                type: "get",
                url: `{{ route('options.supplier') }}/null/${mainCategoryId}`,
                success: function (response) {
                    if (currentToken !== supplierRequestToken) {
                        return;
                    }

                    $('#supplier').html(response);
                },
                complete: function () {
                    if (currentToken === supplierRequestToken) {
                        supplierRequest = null;
                    }
                }
            });
        });

        $('#supplier').change(function (e) {
            const mainCategoryId = $('#main-category').val();
            const supplierId = $(this).val();
            const currentToken = ++productRequestToken;

            abortRequest(productRequest);
            clearProductSelection();

            if (!mainCategoryId || !supplierId) {
                return;
            }

            productRequest = $.ajax({
                type: "get",
                url: "{{ route('options.product') }}",
                data: {
                    filter: {
                        main_category_id: mainCategoryId,
                        supplier_id: supplierId,
                    }
                },
                success: function (response) {
                    if (currentToken !== productRequestToken) {
                        return;
                    }

                    let options = response.replace(
                        '<option value="" disabled selected>-- Select Product SKU --</option>',
                        '');

                    $('#product-sku').html(options);
                },
                complete: function () {
                    if (currentToken === productRequestToken) {
                        productRequest = null;
                    }
                }
            });
        });

        $("#product-sku").on("select2:select", function (e) {
            const { id, element: { dataset } } = e.params.data;
            const { id: productId, sku, unitId, unitName } = dataset;
            const selectedId = id;

            addQtyForm(selectedId, sku, productId, unitId, unitName)
            addPriceForm(selectedId, sku, productId, unitName)

            $('.autonumber').autoNumeric('init');
            recalculateTotal();
        });

        function addQtyForm(selectedId, sku, productId, unitId, unitName) {
            let newElement = `<div class="form-group" id="select${selectedId}">
                                <div class="col-12 validate-input">
                                    <h6>Qty for ${sku} (${unitName})<span class="text-danger">*</span></h6>
                                    <input type="text" class="form-control form-qty autonumber" data-product-id="${productId}" name="quantity[${productId}]" autocomplete="off">
                                    <input type="hidden" name="unit[${productId}]" value="${unitId}">
                                </div>
                            </div>`;

            $('#form-qty-area').append(newElement);
        }

        function addPriceForm(selectedId, sku, productId, unitName) {
            let newElement = `<div class="form-group" id="select${selectedId}">
                                <div class="col-12 validate-input">
                                    <h6>Unit Price for ${sku} (${unitName})<span class="text-danger">*</span></h6>
                                    <input type="text" placeholder="harga satuan" data-a-sign="Rp " name="price[${productId}]"
                                        class="form-control autonumber disableEnterSubmit">
                                </div>
                            </div>`;

            $('#form-price-area').append(newElement);
        }

        function getAutoNumericValue(inputElement) {
            const element = $(inputElement);

            if (!element.length) {
                return 0;
            }

            let rawValue = element.val();

            if (typeof element.autoNumeric === 'function') {
                try {
                    rawValue = element.autoNumeric('get');
                } catch (e) {
                    rawValue = element.val();
                }
            }

            const numericValue = parseFloat(rawValue);

            return isNaN(numericValue) ? 0 : numericValue;
        }

        function recalculateTotal() {
            let total = 0;

            $('#form-add .form-qty').each(function () {
                const productId = $(this).data('product-id');
                const qty = getAutoNumericValue(this);
                const priceInput = $(`#form-add [name="price[${productId}]"]`);
                const price = getAutoNumericValue(priceInput);

                total += qty * price;
            });

            $('#form-add .autoCalculateInput').each(function () {
                total += getAutoNumericValue(this);
            });

            if (typeof $('#total').autoNumeric === 'function') {
                $('#total').autoNumeric('set', total);
            } else {
                $('#total').val(total);
            }
        }

        $("#product-sku").on("select2:unselect", function (e) {
            var unselectedOption = e.params.data;

            $(`#form-qty-area div[id="select${unselectedOption.id}"]`).remove();
            $(`#form-price-area div[id="select${unselectedOption.id}"]`).remove();
            recalculateTotal();
        });

        $('#form-add').on('keyup change input blur', '.form-qty, [name^="price["], .autoCalculateInput', function () {
            recalculateTotal();
        });

        $('#form-add').submit(function (e) {
            e.preventDefault();

            $('.autonumber').each(function (index, element) {
                let value = $(this).autoNumeric('get');
                
                $(this).val(value);
            });


            let data = $('#form-add').serializeArray();

            data.map(item => {
                if (item.name === 'unique_id' && item.value !== '') {
                    item.value = $('#id-prefix').text() + item.value
                }
            })


            submitData(data);
        });

        recalculateTotal();

        function submitData(params) {
            $.ajax({
                type: 'POST',
                url: "{{ route('purchase_orders.store') }}",
                data: params,
                success: function (data) {
                    $.toast({
                        heading: "Success!",
                        text: "Data has been saved.",
                        position: "top-right",
                        loaderBg: "#5ba035",
                        icon: "success",
                        hideAfter: 3e3,
                        stack: 1
                    });

                    window.location.reload();
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    $('.help-block').remove()

                    let response = xhr.responseJSON;

                    if (!$.isEmptyObject(response)) {

                        $.each(response.errors, function (index, value) {
                            let errorMessage = `<div class="col-12">
                                                    <span class="help-block">
                                                        <mdall>${value}</mdall>
                                                    </span>
                                                </div>`;

                            $(`#form-add`).find(`[name="${index}"]`).parent()
                                .after(errorMessage);
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
@endpush
