<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="form-create" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="date">Date<span class="text-danger">*</span></label>
                            <input id="date" class="form-control" type="date" name="date"
                                value="{{ old('date') ?: date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="bank-account">Bank Account<span class="text-danger">*</span></label>
                            <br>
                            <select id="bank-account" class="form-control select2" name="bank_account">
                                <option selected disabled>-- Select Bank Account --</option>

                                @foreach($bankAccounts as $bankAccount)
                                    <option value="{{ $bankAccount->id }}">
                                        {{ $bankAccount->bankAccountLabel() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="category">Category<span class="text-danger">*</span></label>
                            <br>
                            <select id="category" class="form-control select2" name="category">
                                {!! selectGenerate('Category', $categories, 'id', 'name') !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="code">Code<span class="text-danger">*</span></label>
                            <br>
                            <input type="hidden" id="code-type" name="code_type" value="">
                            <select id="code" class="form-control select2" name="code">
                                {!! costCodeOptions() !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="po-stock-field" style="display: none">
                        <div class="col-12">
                            <label for="reference-po-stock">Reference - PO Stock</label>
                            <br>
                            <select id="reference-po-stock" class="form-control select2" name="po_stock">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="amount">Amount (IDR)<span class="text-danger">*</span></label>
                            <input id="amount" type="text" placeholder="" data-a-sign="Rp "
                                class="form-control autonumber disableEnterSubmit" name="amount" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="recipient-type">Recipient<span class="text-danger">*</span></label>
                            <div>
                                <div class="custom-control custom-radio col-6" style="display: inline">
                                    <input type="radio" id="supplier" name="recipient_type" value="supplier" class="custom-control-input">
                                    <label class="custom-control-label" for="supplier">Supplier</label>
                                </div>
                                <div class="custom-control custom-radio col-6" style="display: inline">
                                    <input type="radio" id="vendor" name="recipient_type" value="vendor" class="custom-control-input">
                                    <label class="custom-control-label" for="vendor">Vendor</label>
                                </div>
                            </div>

                            <br>
                            <select id="recipient" class="form-control select2" name="recipient">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="note">Note<span class="text-danger">*</span></label>
                            <textarea id="note" class="form-control" rows="3" name="note"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="receipt-file-1">Receipt file (image/pdf)</label>
                            <input type="file" id="receipt-file-1" class="form-control" name="receipt_file_1">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="receipt-file-2">Receipt file (image/pdf)</label>
                            <input type="file" id="receipt-file-2" class="form-control" name="receipt_file_2">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light btn-submit">Add</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@push('scripts')
<script>
    $(function () {
        const vendors = @json($vendors);
        const suppliers = @json($suppliers);

        $('#po-stock-field').hide();

        $('#code').change(function (e) {
            let type = $(this).find(':selected').data('code-type');

            $('#code-type').val(type);
        });

        $('#category, #code').change(function (e) {
            let category = $('#category').val();
            let codeType = $('#code-type').val();

            if (category == 213001 && codeType === 'job') {
                $.ajax({
                    type: "get",
                    url: "{{ route('po_stock.get_po_options') }}",
                    data: "data",
                    success: function (response) {
                        $('#reference-po-stock').html(response);
                        $('#po-stock-field').show();
                    }
                });
            } else {
                $('#po-stock-field').hide();
            }
        });

        $('#reference-po-stock').change(function (e) {
            let total = $(this).find('option:selected').data('total');
            let supplierID = $(this).find('option:selected').data('supplier-id');

            $('#amount').val(total);
            $('#supplier').prop('checked', true).trigger('change');
            renderRecipientOptions('supplier', supplierID);
        });

        $('[name="recipient_type"]').change(function (e) {
            renderRecipientOptions($(this).val());
        });

        function renderRecipientOptions(recipientType, selected = null) {
            const $recipient = $('#recipient');
            let options = `<option selected disabled>-- Select ${recipientType} --</option>`;

            if (recipientType === 'vendor') {
                vendors.forEach(vendor => {
                    options += `<option value="${vendor.id}">${vendor.code} | ${vendor.vendor_name}</option>`;
                });
            } else if (recipientType === 'supplier') {
                suppliers.forEach(supplier => {
                    const mainCategoryName = supplier.main_category?.main_category_name ?? '-';
                    const formattedName = `${mainCategoryName} | ${supplier.code} | ${supplier.supplier_name}`;

                    options += `<option value="${supplier.id}">${formattedName}</option>`;
                });
            }

            if ($recipient.hasClass('select2-hidden-accessible')) {
                $recipient.select2('destroy');
            }

            $recipient.html(options);

            if (selected !== null) {
                $recipient.val(String(selected));
            }

            $recipient.select2().trigger('change');
        }

        $('#form-create').submit(function (e) {
            e.preventDefault();

            unformatNumeric($('#amount'));

            let formData = new FormData(this);

            submitData(formData);
        });

        function submitData(formData) {
            $.ajax({
                type: 'POST',
                url: '{{ route('accounting.cost.store') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#createModal').modal('hide');
                    $('#form-create')[0].reset();
                    $('select').val(null).trigger('change');
                    $('.help-block').remove();
                    $('#cost-datatable').DataTable().ajax.reload();

                    toastSuccess('New Cost data successfully added.');
                },
                error: function (xhr, status, error) {
                    $('.help-block').remove();

                    let response = xhr.responseJSON;

                    if (!$.isEmptyObject(response)) {
                        $.each(response.errors, function (index, value) {
                            let errorMessage = `<span class="help-block">
                                                    <mdall>${value}</mdall>
                                                </span>`;

                            $(`#form-create`).find(`[name="${index}"]`).parent().append(errorMessage);
                        });
                    }

                    toastDanger();
                }
            });
        }
    });
</script>
@endpush
