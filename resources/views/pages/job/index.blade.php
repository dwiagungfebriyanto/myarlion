@extends('layouts.base')
@section('title', 'Job List')

@section('css')

{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />

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
                <div class="row">
                    <h4 class="header-title col-12 mb-3">Job List</h4>

                    @can('add job')
                    <div class="col-6">
                        <a href="{{ route('job.list.create') }}"
                            class="btn btn-primary waves-effect waves-light btn-rounded btn-add"><i
                                class="mdi mdi-plus mr-1">
                            </i>Add New</a>
                    </div>

                    <div class="ml-auto">
                        <form action="{{ route('job.import') }}" method="post" id="form-import"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="file" class="filestyle" data-input="false"
                                data-btnClass="btn-success btn-rounded"
                                data-text="<i class='mdi mdi-file-excel'></i> Import Excel" name="excel_file">
                        </form>

                        <a class="btn btn-success waves-effect waves-light btn-rounded"
                            href="{{ route('job.import_template') }}">
                            <i class="mdi mdi-file-document mr-1"></i>Get Template</a>
                    </div>
                    {{-- /.ml-auto --}}
                    @endcan
                </div>
                {{-- /.row --}}

                <h6 class="mt-3">Filter by:</h6>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 mb-1">
                        <select id="customerFilter" class="form-control select2">
                            <option value="">All Customer</option>
                            {!! selectGenerate(null, $customers, 'code', ['code', 'name'], request('customer')) !!}
                        </select>
                    </div>

                    <div class="col-xs-12 col-sm-6 mb-1">
                        <select id="marketingFilter" class="form-control select2">
                            <option value="">All Marketing</option>
                            {!! selectGenerate(null, $marketings, 'id', 'name', request('marketing')) !!}
                        </select>
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

@endsection

@section('js-vendor')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>


<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-filestyle2/bootstrap-filestyle.min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<script>
    $(function () {
        $('[name="excel_file"]').change(function (e) {
            $('#form-import').submit();
        });

        $('#customerFilter, #marketingFilter').change(function (e) {
            let customerCode = $('#customerFilter').val();
            let marketingId = $('#marketingFilter').val();
            let url = '{{ url()->current() }}?customer=' + `${customerCode}&marketing=${marketingId}`;

            window.location.href = url;
        });
        
    });

</script>

@if(session('success'))
<script>
    $(document).ready(function () {
        toastSuccess('{{ session("success") }}');
    });

</script>

@elseif(request()->has('success'))
<script>
    $(document).ready(function () {
        toastSuccess('{{ session("success") }}');

        if (window.location.search.substring(1) == 'success') {
            // remove the parameter from the URL
            var newUrl = window.location.href.replace('?success', '');
            history.pushState({}, '', newUrl);
        };
    });

</script>

@elseif(session('danger'))
<script>
    $(document).ready(function () {
        toastDanger('{{ session("danger") }}');
    })

</script>
@endif

@endsection

@push('scripts')
{{ $dataTable->scripts() }}
@endpush
