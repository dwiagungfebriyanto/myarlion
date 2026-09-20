@extends('layouts.base')
@section('title', $pageTitle)

@section('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"
    type="text/css" />

{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">

<style>
    .long-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 200px;
        /* Atur lebar sesuai kebutuhan Anda */
        cursor: pointer;
    }

    .long-text.expanded {
        max-width: none;
        white-space: normal;
    }

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
                <div class="container row">
                    <h4 class="header-title col-12 mb-2">{{ $pageTitle }}</h4>

                    <div>
                        @can('add cost')
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary waves-effect waves-light btn-rounded"
                                        data-toggle="modal" data-target="#createModal">
                                        <i class="mdi mdi-plus mr-1"></i>Add New</button>
                                </div>

                                <div class="mt-1 col">
                                    <form action="{{ route('cost.import') }}" method="post"
                                        id="form-import" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" class="filestyle" data-input="false"
                                            data-btnClass="btn-success btn-rounded"
                                            data-text="<i class='mdi mdi-file-excel'></i> Import Excel"
                                            name="excel_file">
                                    </form>

                                    <a class="btn btn-success waves-effect waves-light btn-rounded"
                                        href="{{ route('cost.import_template') }}">
                                        <i class="mdi mdi-file-document mr-1"></i>Get Template</a>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
                {{-- /.container --}}

                <br>
                {{ $dataTable->table(
                        attributes: [
                            'class' => 'table table-bordered table-bordered dt-responsive',
                            'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
                        ],
                    ) }}
            </div>
            {{-- /.card-box --}}
        </div>
        {{-- /.col-12 --}}
    </div>
    {{-- /.row --}}
</div>
{{-- /.container-fluid --}}

@include('pages.cost.components.create-modal')
@include('pages.cost.components.detail-modal')

@endsection


@section('js-vendor')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

<!-- Plugins js -->
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-mask-plugin/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-filestyle2/bootstrap-filestyle.min.js') }}">
</script>

<!-- Init js-->
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-masks.init.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<script>
    $(function () {
        $('#cost-datatable').on('click', '.long-text', function () {
            $(this).toggleClass('expanded');
        });

        $('[name="excel_file"]').change(function (e) {
            $('#form-import').submit();
        });
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


@push('scripts')
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

    {{ $dataTable->scripts() }}
@endpush
