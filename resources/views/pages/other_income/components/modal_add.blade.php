<button type="button" class="btn btn-success btn-rounded" data-toggle="modal" data-target="#addModal">
    + Add New
</button>


<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Other Income</h5>

                <button type="button" class="close" id="closeBtn">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="form-add" action="{{ route('other_income.store') }}" class="form-parsley"
                    method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="date" class="col-form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date"
                            value="{{ old('date') ?? date('Y-m-d') }}"
                            required>

                        @error('date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="bank-account" class="col-form-label">Bank Account</label>
                        <select class="form-control select2" id="bank-account" name="bank_account"
                            data-parsley-errors-container="#bank-account-error" required>
                            <option selected disabled>-- Select Bank Account --</option>
                            @foreach($bankAccounts as $bankAccount)
                                <option value="{{ $bankAccount->id }}"
                                    {{ old('bank_account') == $bankAccount->id ? 'selected' : '' }}>
                                    {{ $bankAccount->bankAccountLabel() }}
                                </option>
                            @endforeach
                        </select>

                        <span id="bank-account-error" class="text-danger">
                            @error('bank_account') {{ $message }} @enderror
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="category" class="col-form-label">Category</label>
                        <select class="form-control select2" id="category" name="category"
                            data-parsley-errors-container="#category-error" required>
                            {!! selectGenerate('Category', $categories, 'id', 'category_name') !!}
                        </select>

                        <span id="category-error" class="text-danger">
                            @error('category') {{ $message }} @enderror
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="cost-category" class="col-form-label">Cost Category</label>
                        <select class="form-control select2" id="cost-category" name="cost_category"
                            data-parsley-errors-container="#cost-category-error" required>
                            {!! selectGenerate('Cost Category', $costCategories, 'id', 'name') !!}
                        </select>

                        <span id="cost-category-error" class="text-danger">
                            @error('cost_category') {{ $message }} @enderror
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="code" class="col-form-label">Code</label>

                        <select class="form-control select2" id="code" name="code"
                            data-parsley-errors-container="#code-error" required>
                            {!! costCodeOptions() !!}
                        </select>
                        <input type="hidden" id="codeType" name="code_type" value="">

                        <span id="code-error" class="text-danger">
                            @error('code') {{ $message }} @enderror
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-form-label">Amount</label>
                        <input type="text" class="form-control autoNumeric" id="amount" name="amount" autocomplete="off"
                            value="{{ old('amount') ?? (session('pending_return_stock.total') ?? '') }}"
                            {{ session()->has('pending_return_stock') ? 'readonly' : '' }}
                            required>

                        @error('amount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="recipient">Recipient<span class="text-danger">*</span></label>
                        <div class="mb-2">
                            <div class="custom-control custom-radio col-6" style="display: inline">
                                <input type="radio" id="recipientSupplier" name="recipient_type"
                                    class="custom-control-input recipientType" value="supplier" required>
                                <label class="custom-control-label" for="recipientSupplier">Supplier</label>
                            </div>
                            
                            <div class="custom-control custom-radio col-6" style="display: inline">
                                <input type="radio" id="recipientVendor" name="recipient_type"
                                    class="custom-control-input recipientType" value="vendor" required>
                                <label class="custom-control-label" for="recipientVendor">Vendor</label>
                            </div>
                        </div>

                        <select id="recipient" class="form-control select2 mt-2"
                            data-parsley-errors-container="#recipient-error" name="recipient" required>
                        </select>

                        <span id="recipient-error" class="text-danger">
                            @error('recipient') {{ $message }} @enderror
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            required>{{ old('description') }}</textarea>

                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>
            {{-- /.modal-body --}}

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeBtnFooter">Close</button>
                <button type="submit" class="btn btn-primary" form="form-add">Add</button>
            </div>
        </div>
        {{-- /.modal-content --}}
    </div>
    {{-- /.modal-dialog --}}
</div>
{{-- /.modal --}}


@push('scripts')
    <script>
        $(document).ready(function () {
            $('<style>').prop('type', 'text/css')
                .html(`
                    .swal2-container {
                        z-index: 10000 !important;
                    }
                `)
                .appendTo('head');

            @if(session()->has('pending_return_stock'))

            var isModalSubmitted = false;

            $('#addModal').modal({
                backdrop: 'static',
                keyboard: false
            });

            function handleCloseModal() {
                Swal.fire({
                    title: 'Pending Stock Return',
                    text: 'You have a pending stock return. Do you want to delete this data?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then(function (result) {
                    if (result.value === true) {
                        $.ajax({
                            url: '{{ route("product.return-stock.clear-session") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function () {
                                location.reload();
                            },
                            error: function () {
                                location.reload();
                            }
                        });
                    }
                });
            }

            $('#closeBtn, #closeBtnFooter').on('click', function (e) {
                if (!isModalSubmitted) {
                    e.preventDefault();
                    e.stopPropagation();
                    handleCloseModal();
                    return false;
                }
            });

            $('#addModal').on('hide.bs.modal', function (e) {
                if (!isModalSubmitted) {
                    e.preventDefault();
                    e.stopPropagation();

                    handleCloseModal();
                    return false;
                }
            });
            @else
            $('#closeBtn, #closeBtnFooter').attr('data-dismiss', 'modal');
            @endif


            $('select[name="code"]').change(function (e) {
                const $codetypeInput = $(this).siblings('input[name="code_type"]');
                const codeTypeValue = $(this).find('option:selected').data('code-type');
                $codetypeInput.val(codeTypeValue);
            });

            $('#form-add').submit(function (e) {
                e.preventDefault();
                setAutoNumericRawValue();

                if ($(this).parsley().isValid()) {
                    isModalSubmitted = true;

                    var form = $(this);
                    var formData = new FormData(form[0]);

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('#addModal').modal('hide');
                            toastSuccess('Other Income successfully saved.');

                            @if(session()->has('pending_return_stock'))
                            $.ajax({
                                url: '{{ route("product.return-stock.save-from-session") }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (returnResponse) {
                                    if (returnResponse.success) {
                                        toastSuccess(
                                            'Stock Return successfully saved.'
                                        );
                                    } else {
                                        toastDanger(returnResponse.message ||
                                            'Failed to save stock return.');
                                    }
                                    setTimeout(function () {
                                        location.reload();
                                    }, 1000);
                                },
                                error: function (xhr) {
                                    let errorMessage =
                                        'An error occurred while processing stock return';
                                    if (xhr.responseJSON && xhr.responseJSON
                                        .message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    toastDanger(errorMessage);
                                    setTimeout(function () {
                                        location.reload();
                                    }, 1000);
                                }
                            });
                            @else
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            @endif
                        },
                        error: function (xhr) {
                            isModalSubmitted = false;

                            let errorMessage = 'An error occurred';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastDanger(errorMessage);
                        }
                    });
                }
            });
        });

    </script>

    {{-- # Recipient Type Change --}}
    <script>
        $(function () {
            $('.recipientType').click(function (e) {
                const recipientType = $(this).val();
                const vendors = @json($vendors);
                const suppliers = @json($suppliers);
                let options = `<option selected disabled>-- Select ${recipientType} --</option>`;

                if (recipientType === 'vendor') {
                    vendors.forEach(vendor => {
                        options +=
                            `<option value="${vendor.id}">${vendor.code} | ${vendor.vendor_name}</option>`;
                    });
                } else if (recipientType === 'supplier') {
                    suppliers.forEach(supplier => {
                        const formattedName =
                            `${supplier.main_category.main_category_name} | ${supplier.code} | ${supplier.supplier_name}`;
                        options += `<option value="${supplier.id}">${formattedName}</option>`;
                    });
                }

                $('#recipient').html(options);
            });
        });

    </script>
@endpush
