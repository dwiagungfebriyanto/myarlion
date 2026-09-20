@extends('layouts.base')
@section('title', 'Edit Other Income')


@section('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet"
    type="text/css" />
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex">
                    <h4 class="header-title">
                        <a href="{{ route('other_income.index') }}" title="Back">
                            <i class="fa fa-arrow-left mr-2"></i>
                        </a>
                        Other Income
                    </h4>
                </div>
                <hr>
                {{-- /.d-flex --}}

                <div class="card-body">
                    <form id="form-edit"
                        action="{{ route('other_income.update', $otherIncome) }}"
                        class="form-parsley" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="edit-date" class="col-form-label">Date</label>
                            <input type="date" class="form-control" id="edit-date" name="date"
                                value="{{ $otherIncome->date }}" required>

                            @error('date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="edit-account" class="col-form-label">Bank Account</label>
                            <select class="form-control select2" id="edit-account" name="bank_account"
                                data-parsley-errors-container="#bank-account-error" required>
                                <option selected disabled>-- Select Bank Account --</option>
                                @foreach($bankAccounts as $bankAccount)
                                    <option value="{{ $bankAccount->id }}"
                                        {{ $bankAccount->id === $otherIncome->bank_account_id ? 'selected' : '' }}>
                                        {{ $bankAccount->bankAccountLabel() }}
                                    </option>
                                @endforeach
                            </select>

                            <span id="bank-account-error" class="text-danger">
                                @error('bank_account') {{ $message }} @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="edit-category" class="col-form-label">Category</label>
                            <select class="form-control select2" id="edit-category" name="category"
                                data-parsley-errors-container="#category-error" required>
                                {!! selectGenerate('Category', $categories, 'id', 'category_name',
                                $otherIncome->other_income_category_id) !!}
                            </select>

                            <span id="category-error" class="text-danger">
                                @error('category') {{ $message }} @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="cost-category" class="col-form-label">Cost Category</label>
                            <select class="form-control select2" id="cost-category" name="cost_category"
                                data-parsley-errors-container="#cost-category-error" required>
                                {!! selectGenerate('Cost Category', $costCategories, 'id', 'name',
                                $otherIncome->outcome_type_id) !!}
                            </select>

                            <span id="cost-category-error" class="text-danger">
                                @error('cost_category') {{ $message }} @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="code" class="col-form-label">Code</label>

                            <select class="form-control select2" id="code" name="code"
                                data-parsley-errors-container="#code-error" required>
                                {!! costCodeOptions($otherIncome->code, $otherIncome->code_type) !!}
                            </select>
                            <input type="hidden" id="codeType" name="code_type" value="">

                            <span id="code-error" class="text-danger">
                                @error('code') {{ $message }} @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="edit-amount" class="col-form-label">Amount</label>
                            <input type="text" class="form-control autoNumeric" id="edit-amount" name="amount"
                                autocomplete="off" value="{{ $otherIncome->amount }}" required>

                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="recipient">Recipient<span class="text-danger">*</span></label>
                            @php
                                $currentType = $otherIncome->recipient_type_alias;
                            @endphp
                            <div class="mb-2">
                                <div class="custom-control custom-radio col-6" style="display: inline">
                                    <input type="radio" id="recipientSupplier" name="recipient_type"
                                        class="custom-control-input recipientType" value="supplier" 
                                        @if ($currentType === 'supplier') checked @endif required>
                                    <label class="custom-control-label" for="recipientSupplier">Supplier</label>
                                </div>
                                
                                <div class="custom-control custom-radio col-6" style="display: inline">
                                    <input type="radio" id="recipientVendor" name="recipient_type"
                                        class="custom-control-input recipientType" value="vendor" 
                                        @if ($currentType === 'vendor') checked @endif required>
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
                            <label for="edit-description" class="col-form-label">Description</label>
                            <textarea class="form-control" id="edit-description" name="description" rows="3"
                                required>{{ $otherIncome->description }}</textarea>

                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row justify-content-end px-2">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- /.card-box --}}
        </div>
        {{-- /.col-12 --}}
    </div>
    {{-- /.row --}}
</div>
{{-- /.container-fluid --}}
@endsection


@section('js-vendor')
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>
@endsection


@push('scripts')
    <script>
        $(function () {
            $('select[name="code"]').change(function (e) {
                const $codetypeInput = $(this).siblings('input[name="code_type"]');
                const codeTypeValue = $(this).find('option:selected').data('code-type');
                $codetypeInput.val(codeTypeValue);
            });

            $('#form-edit').submit(function (e) {
                e.preventDefault();
                setAutoNumericRawValue();

                $(this).unbind('submit').submit();
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

    {{-- On Page Load --}}
    <script>
        $(function () {
            // Trigger click to load options
            $('.recipientType:checked').trigger('click');

            // Set selected recipient
            const currentRecipientId = '{{ $otherIncome->recipient_id }}';
            if (currentRecipientId) {
                $('#recipient').val(currentRecipientId).trigger('change');
            }

            // Trigger change to set code type input value
            $('select[name="code"]').trigger('change');
        });
    </script>

    @if (session('success'))
        <script>
            $(function () {
                toastSuccess('{{ session("success") }}')
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            $(function () {
                toastDanger('{{ session("error") }}')
            });
        </script>
    @endif
@endpush
