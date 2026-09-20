<div id="addPoStock" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addPoStockForm" method="post" class="parsley-form"
                action="{{ route('job_statement.store_job_po_stock', $job) }}">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add PO Stock</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="validate-input">
                            <label for="po-stock">PO Stock<span class="text-danger">*</span></label>
                            <select name="po_stock" id="po-stock" class="form-control select2" required>
                                {!! $poStockOptions !!}
                            </select>

                            @error('po_stock')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="po-product">Product SKU<span class="text-danger">*</span></label>
                            <select name="product" id="po-product" class="form-control select2" required>
                            </select>

                            @error('product')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <input type="hidden" name="po_detail_id">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="qty">Quantity<span class="text-danger">*</span></label>
                            <input type="text" id="qty" class="form-control autonumber" data-a-sep="." data-a-dec=","
                                name="quantity" required>
                            <span class="text-disabled text-danger" id="po-product-price" style="display: block"></span>

                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="amount">Amount<span class="text-danger">*</span></label>
                            <input type="text" id="amount" class="form-control autonumber" data-a-sep="." data-a-dec=","
                                name="amount" required>

                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="note">Note</label>
                            <textarea name="note" class="form-control" id="note" cols="30" rows="10"></textarea>

                            @error('note')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- /.modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        id="submitAddPoStock">Add</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{-- /.modal --}}


@push('scripts')
    <script>
        $('#po-stock').change(function (e) {
            $('#addPoStockForm #po-product-price').text('');

            $.ajax({
                type: "get",
                url: "{{ route('po_stock.get_po_products') }}",
                data: {
                    po_stock_id: $(this).val()
                },
                success: function (response) {
                    $('#po-product').html(response.options);
                }
            });
        });

        $('#po-product').change(function (e) {
            const poProductData = $(this).find("option:selected").data();
            const remainingQty = poProductData.remainingQty;
            const poDetailId = poProductData.poDetailId;
            const price = poProductData.purchaseCost;

            $('[name="po_detail_id"]').val(poDetailId);
            $('#addPoStockForm #qty').val(remainingQty);
            $('#addPoStockForm #amount').val(price * remainingQty);

            if (price) {
                $('#addPoStockForm #po-product-price').text('*Unit price: ' + currencyFormat(price));
            }
        });

        $('#addPoStockForm #qty').keyup(function (e) {
            const qty = $('#addPoStockForm #qty').val();
            const price = $('#po-product').find("option:selected").data('purchase-cost');

            $('#addPoStockForm #amount').autoNumeric('set', price * qty)
        });

        $('#submitAddPoStock').click(function (e) {
            let qty = $('#addPoStockForm #qty').autoNumeric('get');
            let amount = $('#addPoStockForm #amount').autoNumeric('get');

            $('#addPoStockForm #qty').val(qty);
            $('#addPoStockForm #amount').val(amount);

            $('#addPoStockForm').submit();
        });

    </script>
@endpush
