<div id="editPoStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditPoStock" method="post" action="" class="parsley-form">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit PO Stock</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="edit-po-stock">PO Stock<span class="text-danger">*</span></label>
                            <select name="po_stock" id="edit-po-stock" class="form-control" disabled>
                                {!! $poStockOptions !!}
                            </select>

                            @error('po_stock')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="edit-po-product">Product SKU<span class="text-danger">*</span></label>
                            <select name="product" id="edit-po-product" class="form-control select2" required disabled>
                            </select>

                            @error('product')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <input type="hidden" name="po_detail_id">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="edit-qty">Quantity<span class="text-danger">*</span></label>
                            <input type="text" id="edit-qty" class="form-control autonumber" data-a-sep="."
                                data-a-dec="," name="quantity" required>

                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="edit-amount">Amount<span class="text-danger">*</span></label>
                            <input type="text" id="edit-amount" class="form-control autonumber" data-a-sep="."
                                data-a-dec="," name="amount" required>

                            @error('amount')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="edit-note">Note</label>
                            <textarea name="note" class="form-control" id="edit-note" cols="30" rows="10"></textarea>

                            @error('note')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="submitEditPoStock">Update</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div>
</div>


@push('scripts')
<script>
    $(function () {
        $('#formEditPoStock #edit-qty').keyup(function (e) {
            const qty = $('#formEditPoStock #edit-qty').autoNumeric('get');
            const price = $('#edit-po-product').find("option:selected").data('purchase-cost');

            $('#formEditPoStock #edit-amount').autoNumeric('set', price * qty)
        });

        $('#submitEditPoStock').click(function (e) {
            let qty = $('#formEditPoStock #edit-qty').autoNumeric('get');
            let amount = $('#formEditPoStock #edit-amount').autoNumeric('get');

            $('#formEditPoStock #edit-qty').val(qty);
            $('#formEditPoStock #edit-amount').val(amount);

            $('#formEditPoStock').submit();
        });
    });

</script>
@endpush
