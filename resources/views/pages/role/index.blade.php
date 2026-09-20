@extends('layouts.base')
@section('title', $pageTitle)

@section('css')
<link href="{{ URL::to('/') }}/assets/libs/select2/select2.min.css" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet"
    type="text/css" />
{{-- datatables --}}
<link href="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.css"
    rel="stylesheet" type="text/css" />
<link href="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.css"
    rel="stylesheet" type="text/css" />

    <style>
        .help-block {
            color: red
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <div class="d-flex">
                        <h4 class="header-title mb-3">{{ $pageTitle }}</h4>
                        <div class="ml-auto">
                            @can('add role')
                            <button type="button" class="btn btn-success waves-effect waves-light btn-rounded"
                                data-toggle="modal" data-target="#createModal"><i class="mdi mdi-plus mr-1"></i> Add
                                New</button>
                            @endcan
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

@include('pages.role.components.modal.create-modal')
@include('pages.role.components.modal.detail-modal')
@include('pages.role.components.modal.edit-modal')

@endsection

@section('js-vendor')
    <!-- Required datatable js -->
    <script src="{{ URL::to('/') }}/assets/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Responsive examples -->
    <script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.min.js"></script>

    <!-- Plugins js -->
    <script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/parsleyjs/parsley.min.js"></script>

    <!-- Init js-->
    <script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>

    @if (session('success'))
        <input type="hidden" id="session-success" value="{{ session('success') }}">
        <script>
            $(document).ready(function() {
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

    @if (session('danger'))
        <input type="hidden" id="session-danger" value="{{ session('danger') }}">
        <script>
            $(document).ready(function() {
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
    {{ $dataTable->scripts() }}
@endpush
