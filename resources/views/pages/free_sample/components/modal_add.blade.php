<button type="button" class="btn btn-success btn-rounded waves-effect waves-light" data-toggle="modal"
    data-target="#addModal">
    + Add New
</button>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Free Sample for Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @php
                    $oldRecipientType = old('recipient_type', 'inquiry');
                @endphp
                <form action="" id="formAdd" method="post" class="form-parsley">
                    @csrf
                    @if (session('error') || $errors->any())
                        <div class="alert alert-danger">
                            @if (session('error'))
                                <div>{{ session('error') }}</div>
                            @endif

                            @if ($errors->any())
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="warehouse">Warehouse<span class="text-danger">*</span></label>
                        <select id="warehouse" name="warehouse" class="form-control select2">
                            {!! selectGenerate('Warehouse', $warehouses, 'id', 'warehouse_name', old('warehouse')) !!}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="supplier">Supplier<span class="text-danger">*</span></label>
                        <select id="supplier" name="supplier" class="form-control select2">
                            {!! selectGenerate('supplier', $suppliers, 'id', 'supplier_name', old('supplier')) !!}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="inventoryStock">Inventory<span class="text-danger">*</span></label>
                        <select id="inventoryStock" name="inventory_stock_id" class="form-control select2"
                            required disabled></select>
                    </div>

                    <div class="form-group">
                        <label for="qty">Quantity<span class="text-danger">*</span></label>
                        <input type="hidden" id="sampleStock" value="">

                        <div class="input-group">
                            <input id="qty" type="text" class="form-control autonumeric" name="quantity" maxlength="10"
                                data-parsley-errors-container="#qty-error" autocomplete="off" required
                                value="{{ old('quantity') }}">

                            <div class="input-group-append">
                                <span class="input-group-text" id="sampleUnit"></span>
                            </div>
                        </div>

                        <span id="qty-error" class="help-block"></span>
                    </div>

                    <div class="form-group">
                        <label>Recipient Type<span class="text-danger">*</span></label>
                        <div class="mt-1">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="recipientTypeInquiry" name="recipient_type" value="inquiry"
                                    class="custom-control-input" {{ $oldRecipientType === 'inquiry' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="recipientTypeInquiry">By Inquiry</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="recipientTypeCustomer" name="recipient_type" value="customer"
                                    class="custom-control-input" {{ $oldRecipientType === 'customer' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="recipientTypeCustomer">By Customer</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group recipient-by-inquiry">
                        <label for="inquiry">Inquiry<span class="text-danger">*</span></label>
                        <select name="inquiry" id="inquiry" class="form-control select2" required>
                            <option value="" disabled selected>-- Select Inquiry --</option>
                            @foreach ($inquiries as $inquiry)
                                <option value="{{ $inquiry->id }}"
                                    {{ (string) old('inquiry') === (string) $inquiry->id ? 'selected' : '' }}
                                    data-customer-id="{{ $inquiry->customer_id }}"
                                    data-customer-name="{{ $inquiry->customer ? $inquiry->customer->code . ' - ' . $inquiry->customer->name : $inquiry->name }}">
                                    {{ \Carbon\Carbon::parse($inquiry->date)->format('d/m/Y') }} - {{ $inquiry->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group recipient-by-inquiry">
                        <label for="customerDisplay">Recipient</label>
                        <input type="hidden" id="inquiryCustomer">
                        <input type="text" id="customerDisplay" class="form-control" readonly
                            placeholder="Penerima akan terisi otomatis dari inquiry">
                    </div>

                    <div class="form-group recipient-by-customer" style="display: none;">
                        <label for="customerSelect">Customer<span class="text-danger">*</span></label>
                        <select name="customer" id="customerSelect" class="form-control select2" disabled>
                            {!! selectGenerate('Customer', $customers, 'id', ['code', 'name'], old('customer')) !!}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date">Date<span class="text-danger">*</span></label>
                        <input type="date" id="date" name="date" class="form-control" required value="{{ old('date') }}">
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="formAdd">Submit</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function () {
            const oldValues = {
                warehouse: @json(old('warehouse')),
                supplier: @json(old('supplier')),
                inventoryStockId: @json(old('inventory_stock_id')),
                recipientType: @json(old('recipient_type', 'inquiry')),
                inquiry: @json(old('inquiry')),
                customer: @json(old('customer')),
                hasErrors: @json($errors->any() || session()->has('error')),
            };

            const loadInventoryOptions = (selectedInventory = null) => {
                let warehouseId = $('#warehouse').val();
                let supplierId = $('#supplier').val();

                if (!warehouseId) {
                    $('#inventoryStock')
                        .prop('disabled', true)
                        .html('<option value="" disabled selected>-- Select Inventory --</option>');
                    $('#sampleUnit').text('');
                    $('#sampleStock').val('');
                    return;
                }

                $.ajax({
                    type: "post",
                    url: "{{ route('api.inventory_stock.ready_stock_options') }}",
                    data: {
                        warehouse_id: warehouseId,
                        supplier_id: supplierId,
                        show_bucket: 1,
                    },
                    dataType: "json",
                    success: function (response) {
                        $('#inventoryStock')
                            .prop('disabled', false)
                            .html(response.options);

                        if (selectedInventory && $('#inventoryStock option[value="' + selectedInventory + '"]').length) {
                            $('#inventoryStock').val(String(selectedInventory)).trigger('change');
                        } else {
                            $('#inventoryStock').val(null).trigger('change');
                            $('#sampleUnit').text('');
                            $('#sampleStock').val('');
                        }
                    },
                    error: function () {
                        $('#inventoryStock')
                            .prop('disabled', true)
                            .html('<option value="" disabled selected>-- Select Inventory --</option>');
                        $('#sampleUnit').text('');
                        $('#sampleStock').val('');
                        toastDanger('Inventory gagal dimuat untuk warehouse yang dipilih.');
                    }
                });
            };

            const toggleRecipientFields = (shouldReset = true) => {
                const recipientType = $('[name="recipient_type"]:checked').val();
                const isInquiry = recipientType === 'inquiry';

                $('.recipient-by-inquiry').toggle(isInquiry);
                $('.recipient-by-customer').toggle(!isInquiry);

                $('#inquiry').prop('disabled', !isInquiry).prop('required', isInquiry);
                $('#customerSelect').prop('disabled', isInquiry).prop('required', !isInquiry);

                if (!shouldReset) {
                    if (isInquiry && $('#inquiry').val()) {
                        $('#inquiry').trigger('change');
                    }

                    return;
                }

                if (isInquiry) {
                    $('#customerSelect').val(null).trigger('change');
                    $('#inquiry').trigger('change');
                } else {
                    $('#inquiry').val(null).trigger('change');
                    $('#inquiryCustomer').val('');
                    $('#customerDisplay').val('');
                }
            };

            $('#warehouse, #supplier').change(function () {
                loadInventoryOptions();
            });

            $('#inventoryStock').change(function () {
                let data = $(this).find(':selected').data() || {};

                $('#sampleUnit').text(data.unitName || '');
                $('#sampleStock').val(data.stock || '');
            });

            $('#inquiry').change(function () {
                let data = $(this).find(':selected').data() || {};

                $('#inquiryCustomer').val(data.customerId || '');
                $('#customerDisplay').val(data.customerName || '');
            });

            $('[name="recipient_type"]').change(function () {
                toggleRecipientFields(true);
            });

            $('#formAdd').submit(function (e) {
                e.preventDefault();
                let qty = $('#qty').autoNumeric('get');
                let sampleStock = $('#sampleStock').val();

                if (parseFloat(qty) > parseFloat(sampleStock)) {
                    $('#qty-error').html(`Quantity must be less or equal to ${sampleStock}`);
                    return;
                }

                $('#qty-error').html('');
                setAutoNumericRawValue();
                $(this).unbind('submit').submit();
            });

            toggleRecipientFields(false);

            if (oldValues.warehouse) {
                loadInventoryOptions(oldValues.inventoryStockId);
            }

            if (oldValues.hasErrors) {
                $('#addModal').modal('show');
            }
        });

    </script>
@endpush
