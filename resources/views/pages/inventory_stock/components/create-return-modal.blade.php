<div id="returnModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/product/return-stock" id="form-return" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add Return Stock</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="inventory_stock_id" id="return-inventory-stock-id">
                    <input type="hidden" name="warehouse_id" id="return-warehouse-id">
                    <input type="hidden" name="unit_id" id="return-qty-unit-id">

                    <div class="form-group col-12">
                        <label for="return-warehouse">Warehouse<span class="text-danger">*</span></label>
                        <input id="return-warehouse" name="warehouse_name" class="form-control" type="text" readonly>
                    </div>

                    <div class="form-group col-12">
                        <label for="return-inventory-stock">Inventory Stock<span class="text-danger">*</span></label>
                        <input id="return-inventory-stock" name="inventory_stock_name" class="form-control" type="text" readonly>
                    </div>

                    <div class="form-group col-12 validate-input">
                        <label for="return-qty">Quantity<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input id="return-qty" type="text" class="form-control autonumeric" name="qty" maxlength="10" autocomplete="off">
                            <div class="input-group-append">
                                <span class="input-group-text" id="return-unit"></span>
                            </div>
                        </div>
                        <span class="help-block"><mdall id="return-qty-error"></mdall></span>
                    </div>

                    <div class="form-group col-12">
                        <label for="return-price">Nominal<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">IDR</span>
                            </div>
                            <input id="return-price" name="price_display" class="form-control" type="text" readonly>
                            <input type="hidden" id="return-price-per-unit" name="price_per_unit">
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <label for="return-total">Total<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">IDR</span>
                            </div>
                            <input id="return-total" class="form-control" type="text" name="total" readonly>
                        </div>
                        <span class="help-block"><mdall id="return-total-error"></mdall></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="loading-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:none;justify-content:center;align-items:center;">
    <div style="background:white;padding:20px;border-radius:5px;"><h4>Processing...</h4><p>Please wait</p></div>
</div>

@push('scripts')
<script>
$(function() {
    const $csrf = $('meta[name="csrf-token"]').attr('content');
    const $modal = $('#returnModal');
    const $form = $('#form-return');
    const $overlay = $('#loading-overlay');

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $csrf } });

    const fmt = n => new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);

    const initNumeric = (unitId) => {
        if (typeof AutoNumeric !== 'undefined' && !AutoNumeric.getAutoNumericElement('#return-qty')) {
            new AutoNumeric('#return-qty', {
                decimalPlaces: unitId == 1 ? 3 : 0,
                decimalCharacter: '.',
                digitGroupSeparator: ',',
                unformatOnSubmit: true
            });
        }
    };

    const getQty = () => {
        if (typeof AutoNumeric !== 'undefined') {
            const an = AutoNumeric.getAutoNumericElement('#return-qty');
            return an ? an.getNumber() : parseFloat($('#return-qty').val().replace(/,/g, '') || '0');
        }
        return parseFloat($('#return-qty').val().replace(/,/g, '') || '0');
    };

    const calcTotal = () => {
        const qty = getQty();
        const price = parseFloat($('#return-price-per-unit').val() || '0');
        $('#return-qty-error').text("");
        $('#return-total').val(fmt(qty * price));
        return true;
    };

    $('body').on('click', '.btn-return', function() {
        const $this = $(this);
        const unitId = $this.data('unit-id');

        $('#return-inventory-stock-id').val($this.data('id'));
        $('#return-warehouse-id').val($this.data('warehouse-id'));
        $('#return-warehouse').val($this.data('warehouse-name'));
        $('#return-inventory-stock').val($this.data('product'));
        $('#return-unit').text($this.data('unit'));
        $('#return-price-per-unit').val($this.data('price'));
        $('#return-price').val(fmt($this.data('price')));
        $('#return-qty-unit-id').val(unitId);

        $('#return-qty, #return-total').val('');
        $('.help-block mdall').text('');

        initNumeric(unitId);
        $modal.modal('show');
        $modal.one('shown.bs.modal', calcTotal);
    });

    $('#return-qty').on('keypress', e => {
        const c = e.which || e.keyCode;
        const unitId = $('#return-qty-unit-id').val();
        return !(c > 31 && (c < 48 || c > 57) && (unitId != 1 || c !== 46));
    }).on('input', function() {
        const unitId = $('#return-qty-unit-id').val();
        let v = $(this).val().replace(unitId == 1 ? /[^0-9.]/g : /[^0-9]/g, '');

        if (unitId == 1) {
            const p = v.split('.');
            v = p.length > 2 ? p[0] + '.' + p.slice(1).join('') : v;
            if (p.length === 2 && p[1].length > 3) v = p[0] + '.' + p[1].substring(0, 3);
        }

        if (v !== $(this).val()) $(this).val(v);
        calcTotal();
    });

    $form.submit(function(e) {
        e.preventDefault();
        $('.help-block mdall').text('');

        const qty = getQty();
        const unitId = $('#return-qty-unit-id').val();

        if (unitId == 1) {
            const qtyStr = qty.toString();
            if (qtyStr.includes('.') && qtyStr.split('.')[1].length > 3) {
                $('#return-qty-error').text("Maximum 3 decimal places allowed.");
                return false;
            }
        }

        const fd = new FormData(this);
        fd.set('_token', $csrf || '');
        fd.set('qty', qty);
        fd.set('total', parseFloat($('#return-total').val().replace(/,/g, '') || '0'));
        fd.set('unit_id', unitId);

        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).text('Processing...');
        $overlay.css('display', 'flex');

        $.ajax({
            type: 'POST',
            url: '/product/return-stock',
            data: fd,
            processData: false,
            contentType: false,
            success: function(data) {
                $overlay.css('display', 'none');
                window.location.href = data.redirect || location.href;
            },
            error: function(xhr) {
                $overlay.css('display', 'none');
                $btn.prop('disabled', false).text('Add');
                $modal.modal('show');

                const res = xhr.responseJSON;
                if (res && res.errors) {
                    $.each(res.errors, (f, m) => $(`#return-${f}-error`).text(m[0]));
                } else if (res && res.message) {
                    $('#return-qty-error').text(res.message);
                } else {
                    $('#return-qty-error').text("An error occurred. Please try again.");
                }
            }
        });
    });
});
</script>
@endpush
