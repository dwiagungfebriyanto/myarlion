@extends('layouts.base')
@section('title', 'Edit Job')


@section('css')
<!-- Plugins css -->
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/tooltipster/tooltipster.bundle.min.css') }}" rel="stylesheet"
    type="text/css" />
{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />

<style>
    .format-numb {
        all: unset;
        width: 100%;
    }

    .total-label {
        font-weight: bold;
        background-color: yellow
    }

</style>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title">Edit Job</h4>

                @include('pages.job.components.edit.navs')

                <div class="tab-content">
                    <div class="tab-pane show active">
                        @switch(Route::currentRouteName())
                            @case('job.list.edit')
                                @include('pages.job.edit.tabs.edit-job')
                                @break

                            @case('job_statement.index')
                                @include('pages.job_statement.index')
                                @break

                            @default
                                @yield('datatable')
                        @endswitch
                    </div>
                    {{-- /.tab-pane --}}
                </div>
                {{-- /.tab-content --}}
            </div>
            {{-- /.card-box --}}
        </div>
        {{-- /.col-12 --}}
    </div>
    <!-- end row -->
</div>
<!-- end container-fluid -->
@endsection

@section('js-vendor')
<!-- Plugins js -->
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/tooltipster/tooltipster.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<!-- Init js-->
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-masks.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/tooltipster.init.js') }}"></script>

<script>
    $(function () {
        $(".parsley-form").parsley()
    });
</script>

@if (session('success'))
    <script>
        toastSuccess('{{ session("success") }}');
    </script>
@endif

@if (session('danger'))
    <script>
        toastDanger('{{ session("danger") }}');
    </script>
@endif

@endsection


@push('scripts')
    @yield('datatable_scripts')
@endpush
