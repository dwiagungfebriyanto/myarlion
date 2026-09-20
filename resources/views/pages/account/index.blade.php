@extends('layouts.base')
@section('title', 'Account')

@section('css')

    {{-- datatables --}}
    <link href="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />

    <link href="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/tooltipster/tooltipster.bundle.min.css" rel="stylesheet" type="text/css" >


@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <div class="d-flex">
                        <h4 class="header-title mb-3">Account List</h4>
                        <div class="ml-auto">
                            @can('add account')
                            <a href="{{ route('account.create') }}"
                                class="btn btn-success btn-rounded waves-effect waves-light">
                                <i class="mdi mdi-plus mr-1"></i> Add New
                            </a>
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

    @include('pages.account.components.show-signature')
    @include('pages.account.components.permission-modal')

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
    <script src="{{ URL::to('/') }}/assets/libs/tooltipster/tooltipster.bundle.min.js"></script>

    @if (session('success'))
        <input type="hidden" id="session-success" value="{{ session('success') }}">
        <script>
            $(document).ready(function() {
                $(".btn-action").tooltipster()

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

    @if (request()->has('success'))
        <script>
            $(document).ready(function() {
                $.toast({
                    heading: "Success!",
                    text: 'Signature and all user data successfully added.',
                    position: "top-right",
                    loaderBg: "#5ba035",
                    icon: "success",
                    hideAfter: 3e3,
                    stack: 1
                });
                if (window.location.search.substring(1) == 'success') {
                    // remove the parameter from the URL
                    var newUrl = window.location.href.replace('?success', '');
                    history.pushState({}, '', newUrl);
                };
            });
        </script>
    @endif

    @if (request()->has('successNotSign'))
        <script>
            $(document).ready(function() {
                $.toast({
                    heading: "Success!",
                    text: 'All user data without signature successfully added.',
                    position: "top-right",
                    loaderBg: "#5ba035",
                    icon: "success",
                    hideAfter: 3e3,
                    stack: 1
                });
                if (window.location.search.substring(1) == 'successNotSign') {
                    // remove the parameter from the URL
                    var newUrl = window.location.href.replace('?successNotSign', '');
                    history.pushState({}, '', newUrl);
                };
            });
        </script>
    @endif


    @if (request()->has('updated'))
        <script>
            $(document).ready(function() {
                $.toast({
                    heading: "Updated!",
                    text: 'Signature and all user data successfully updated.',
                    position: "top-right",
                    loaderBg: "#5ba035",
                    icon: "success",
                    hideAfter: 3e3,
                    stack: 1
                });
                if (window.location.search.substring(1) == 'updated') {
                    // remove the parameter from the URL
                    var newUrl = window.location.href.replace('?updated', '');
                    history.pushState({}, '', newUrl);
                };
            });
        </script>
    @endif

    @if (request()->has('updatedNotSign'))
        <script>
            $(document).ready(function() {
                $.toast({
                    heading: "Updated!",
                    text: 'All user data without signature successfully updated.',
                    position: "top-right",
                    loaderBg: "#5ba035",
                    icon: "success",
                    hideAfter: 3e3,
                    stack: 1
                });
                if (window.location.search.substring(1) == 'updatedNotSign') {
                    // remove the parameter from the URL
                    var newUrl = window.location.href.replace('?updatedNotSign', '');
                    history.pushState({}, '', newUrl);
                };
            });
        </script>
    @endif

@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush
