@extends('layouts.base')
@section('title', 'Inquiry')

@section('css')

<link href="{{ URL::to('/') }}/assets/libs/select2/select2.min.css" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::to('/') }}/assets/libs/bootstrap-select/bootstrap-select.min.css"
    rel="stylesheet" type="text/css" />
<link href="{{ URL::to('/') }}/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css"
    rel="stylesheet" type="text/css" />

<link href="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet"
    type="text/css" />


{{-- datatables --}}
<link href="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.css"
    rel="stylesheet" type="text/css" />
<link href="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.css"
    rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />

<style>
    .help-block {
        color: red
    }

    #form-import,
    .bootstrap-filestyle {
        display: inline !important;
    }

</style>

@endsection

@push('js-head')
@endpush

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="container row">
                    <h4 class="header-title col-12 mb-2">Inquiry List</h4>

                    <div>
                        @can('add inquiry')
                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-info waves-effect waves-light btn-rounded"
                                        href="{{ route('inquiry.create') }}"><i
                                            class="mdi mdi-plus mr-1"></i>Add New</a>
                                </div>

                                <div class="mt-1 col">
                                    <form action="{{ route('inquiry.inquiryProduct.import') }}"
                                        method="post" id="form-import" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" class="filestyle" data-input="false"
                                            data-btnClass="btn-success btn-rounded"
                                            data-text="<i class='mdi mdi-file-excel'></i> Import Excel"
                                            name="excel_file">
                                    </form>

                                    <a class="btn btn-success waves-effect waves-light btn-rounded"
                                        href="{{ route('inquiry.inquiryProduct.import_template') }}">
                                        <i class="mdi mdi-file-document mr-1"></i>Get Template</a>
                                </div>
                            </div>
                        @endcan
                    </div>

                    <div class="ml-auto">
                        <form action="{{ route('inquiry.index') }}" id="filter-inquiry"
                            class="form-horizontal" role="form" method="get">

                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="filter-month">Filter:</label>
                                <div class="col-md-10">
                                    <input class="form-control" name="filter_month" id="filter-month">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-10 ml-auto">
                                    <select name="filter_marketing" id="filter-marketing" class="form-control select2">
                                        {!! selectGenerate('all marketing', $marketings, 'id', 'name',
                                        request('filter_marketing')) !!}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-10 ml-auto">
                                    <select name="filter_status" id="filter-status" class="form-control select2">
                                        {!! selectGenerate('all status', config('constant.inquiry_status'), 'value', 'key',
                                        request('filter_status')) !!}
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>

                {{ $dataTable->table(
                        attributes: [
                            'class' => 'table table-bordered table-bordered dt-responsive nowrap',
                            'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
                        ],
                    ) }}

                <!-- end row -->
            </div>
        </div>
    </div>
    <!-- end row -->



</div> <!-- end container-fluid -->

<!-- SHOW MODAL-->
@include('pages.inquiry.modals.view-modal')
<!-- END SHOW MODAL-->

{{-- VALIDASI MENGGUNAKAN PARSLEY JS SAJA --}}

<!-- CREATE MODAL -->
{{-- <div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">

        </div><!-- /.modal-dialog -->
    </div><!-- /.modal --> --}}
<!-- END CREATE MODAL -->

<!-- EDIT MODAL-->
{{-- <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">

        </div><!-- /.modal-dialog -->
    </div><!-- /.modal --> --}}
<!--END EDIT MODAL-->

@endsection

@section('js-vendor')
<!-- Required datatable js -->
<script src="{{ URL::to('/') }}/assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Responsive examples -->
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.min.js"></script>

<script src="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::to('/') }}/assets/libs/bootstrap-filestyle2/bootstrap-filestyle.min.js">
</script>
<script src="https://jsuites.net/v4/jsuites.js"></script>

<script>
    $(function () {
        $('.select2').select2();
    });

    $('.btn-add').click(function (e) {
        let data = $(this).data();
        $.ajax({
            method: "get",
            url: data.url,
            success: function (response) {
                $('#createModal').find('.modal-dialog').html(response);
            }
        })
    });

    $('[name="excel_file"]').change(function (e) {
        $('#form-import').submit();
    });

    // FILTER BY MONTH & MARKETING
    let requestMonth = "{{ request('filter_month') }}"
    let setMonth = (requestMonth == '') ? '' : requestMonth + '-01';

    jSuites.calendar(document.getElementById('filter-month'), {
        controls: false,
        type: 'year-month-picker',
        format: 'YYYY-MM',
    }).setValue(setMonth);


    $('#filter-marketing, #filter-status').change(function (e) {
        e.preventDefault();
        let filterMonth = $('#filter-month').val()
        let filterMarketing = $('#filter-marketing').val()
        let filterStatus = $('#filter-status').val()

        $('#filter-inquiry').submit();
    });
    // end of FILTER BY MONTH & MARKETING

</script>

@if(request()->has('success'))
    <script>
        $(document).ready(function () {
            $.toast({
                heading: "Success!",
                text: "Data Inquiry has been saved successfully",
                position: "top-right",
                loaderBg: "#5ba035",
                icon: "success",
                hideAfter: 3e3,
                stack: 1
            })
            if (window.location.search.substring(1) == 'success') {
                // remove the parameter from the URL
                var newUrl = window.location.href.replace('?success', '');
                history.pushState({}, '', newUrl);
            };
        });

    </script>
@elseif(request()->has('update'))
    <script>
        $(document).ready(function () {
            $.toast({
                heading: "Success!",
                text: "Data Inquiry has been updated successfully",
                position: "top-right",
                loaderBg: "#5ba035",
                icon: "success",
                hideAfter: 3e3,
                stack: 1
            })
            if (window.location.search.substring(1) == 'update') {
                // remove the parameter from the URL
                var newUrl = window.location.href.replace('?update', '');
                history.pushState({}, '', newUrl);
            };
        });

    </script>
@endif

@if(session('success'))
    <input type="hidden" id="session-success" value="{{ session('success') }}">
    <script>
        $(document).ready(function () {
            $.toast({
                heading: "Success!",
                text: $('#session-success').val(),
                position: "top-right",
                loaderBg: "#5ba035",
                icon: "success",
                hideAfter: 3e3,
                stack: 1
            })
        });

    </script>
@endif

@if(session('danger'))
    <input type="hidden" id="session-danger" value="{{ session('danger') }}">
    <script>
        $(document).ready(function () {
            $.toast({
                heading: "Failed!",
                text: $('#session-danger').val(),
                position: "top-right",
                loaderBg: "#bf441d",
                icon: "error",
                hideAfter: 3e3,
                stack: 1
            })
        })

    </script>
@endif

@endsection


@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>

    {{ $dataTable->scripts() }}
@endpush
