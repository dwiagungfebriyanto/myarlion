@extends('layouts.base')
@section('title', $pageTitle)

@section('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />

<style>
    #form-import,
    .bootstrap-filestyle {
        display: inline !important;
    }

</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="container">
                    <h4 class="header-title col-12 mb-2">
                        <a href="{{ route('accounting.cost.index') }}" class="mr-2" title="Back">
                            <i class="fas fa-arrow-left"></i></a>

                        {{ $pageTitle }}
                    </h4>
                    <hr class="mb-4">

                    <form action="{{ route('accounting.cost.update', $cost) }}" id="form-edit"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="form-group">
                            <div class="col-12">
                                <label for="date">Date<span class="text-danger">*</span></label>
                                <input id="date" class="form-control" type="date" name="date"
                                    value="{{ $cost->date }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <label for="bank-account">Bank Account<span class="text-danger">*</span></label>
                                <br>
                                <select id="bank-account" class="form-control select2" name="bank_account">
                                    <option value="" disabled selected>-- Select Bank Account --</option>

                                    @foreach($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}"
                                            {{ $bankAccount->id == $cost->bank_account_id ? 'selected' : '' }}>
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
                                    {!! selectGenerate('Category', $categories, 'id', 'name', $cost->outcome_type_id)
                                    !!}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            @php
                                $isClosedJobCost = ($cost->code_type === 'job' && $cost->job->status === 'closed');
                            @endphp

                            <div class="col-12">
                                <label for="code">Code<span class="text-danger">*</span></label>
                                <br>

                                <input type="hidden" id="code-type" name="code_type" value="{{ $cost->code_type }}">

                                @if(!$isClosedJobCost)
                                    <select id="code" class="form-control select2" name="code" {!! $isClosedJobCost
                                        ? 'disabled' : 'required' !!}>
                                        {!! $codeOptions !!}
                                    </select>
                                @else
                                    <input type="text" class="form-control" value="{{ $cost->job->jobDesc() }}"
                                        disabled>
                                    <input type="hidden" name="code" value="{{ $cost->code }}">

                                    <span class="text-sm text-muted">The option does not appear and cannot be changed
                                        because the Job for this Cost has been Closed.</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group" id="po-stock-field">
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
                                    class="form-control autonumber disableEnterSubmit" name="amount"
                                    value="{{ $cost->amount }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <label for="supplier">Recipient<span class="text-danger">*</span></label>
                                <div>
                                    <div class="custom-control custom-radio col-6" style="display: inline">
                                        <input type="radio" id="supplier" name="recipient_type" value="supplier"
                                            class="custom-control-input"
                                            {{ $cost->recipient_type == 'supplier' ? 'checked' : '' }}>

                                        <label class="custom-control-label" for="supplier">Supplier</label>
                                    </div>

                                    <div class="custom-control custom-radio col-6" style="display: inline">
                                        <input type="radio" id="vendor" name="recipient_type" value="vendor"
                                            class="custom-control-input"
                                            {{ $cost->recipient_type == 'vendor' ? 'checked' : '' }}>

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
                                <textarea id="note" class="form-control" rows="3"
                                    name="note">{{ $cost->note }}</textarea>
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

                        <div class="col-12 px-0 row justify-content-end">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                        </div>
                    </form>
                </div>
                {{-- /.container --}}
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
<!-- Plugins js -->
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-mask-plugin/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-filestyle2/bootstrap-filestyle.min.js') }}">
</script>

<!-- Init js-->
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-masks.init.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<script>
    $(function () {
        $('#code').change(function (e) {
            let type = $(this).find(':selected').data('code-type')

            $('#code-type').val(type);
        });

        $('#category, #code').change(function (e) {
            let isProductionCost = $('#category').val() == 213001;
            let code = $('#code').val();
            let codeType = $('#code-type').val();
            const poStockField = $('#po-stock-field');

            if (isProductionCost && codeType === 'job') {
                $.ajax({
                    type: "get",
                    data: {
                        po_stock_id: '{{ $cost->po_stock_id }}',
                    },
                    url: "{{ route('po_stock.get_po_options') }}",
                    success: function (response) {
                        $('#reference-po-stock').html(response);
                        poStockField.show();
                    }
                });
            } else {
                poStockField.hide();
            }
        });

        let refPoAction = $('#reference-po-stock').change(function (e) {
            let total = $(this).find('option:selected').data('total');
            let supplierID = $(this).find('option:selected').data('supplier-id');

            $('#amount').val(total);
            $('#supplier').prop('checked', true);

            getRecipientTypeOptions('{{ route("options.supplier") }}', supplierID)
            $(`#recipient option[value="${supplierID}"]`).attr('selected', 'selected');
        });

        $('[name="recipient_type"]').change(function (e) {
            let value = $(this).val();
            let oldSelected = '{{ $cost->recipient_id }}';
            let url = (value === 'supplier') ?
                '{{ route("options.supplier") }}' :
                '{{ route("options.vendor") }}';

            getRecipientTypeOptions(url, oldSelected)
        });

        function getRecipientTypeOptions(url, selected = null) {
            url = (selected == null) ? url : url + '/' + selected

            $.ajax({
                type: "get",
                url: url,

                success: function (response) {
                    $('#recipient').html(response);
                }
            });
        }

        $('#form-edit').submit(function (e) {
            e.preventDefault();
            setAutoNumericRawValue();

            // gunakan FormData agar input file terkirim
            let formData = new FormData(this); // Buat objek FormData dari form

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                processData: false, // Jangan proses data (agar FormData bekerja)
                contentType: false, // Jangan set content-type secara otomatis
                success: function (response) {
                    // Handle success response
                    $('.help-block').remove();

                    toastSuccess('Cost successfully updated.');

                    setTimeout(() => {
                        location.reload();
                    }, 200);
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    $('.help-block').remove();

                    let response = xhr.responseJSON;

                    if (!$.isEmptyObject(response)) {

                        $.each(response.errors, function (index, value) {
                            let errorMessage = `<span class="help-block">
                                                    <mdall>${value}</mdall>
                                                </span>`;

                            $(`#form-edit`).find(`[name="${index}"]`).parent()
                                .append(
                                    errorMessage);
                        });
                    }

                    toastDanger();
                }
            });
        });


        // first load
        $('#category, #code').change();
        $('[name="recipient_type"][value="{{ $cost->recipient_type }}"]').change();

        if ('{{ $cost->po_stock_id }}' !== '') {
            refPoAction;
        } else {
            $('[name="recipient_type"]').change();
        }
    });

</script>


@if(session('success'))
    <script>
        toastSuccess('{{ session("success") }}');

    </script>
@endif

@if(session('danger'))
    <script>
        toastDanger('{{ session("danger") }}');

    </script>
@endif

@endsection
