@extends('layouts.base')
@section('title', 'Create Inquiry')

@section('css')
    <!-- Plugins css -->
    <link href="{{ URL::to('/') }}/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    {{-- datatables --}}
    <link href="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />

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
                    <h4 class="header-title">Create Inquiry</h4>

                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <button data-toggle="tab" aria-expanded="false" class="create-inq nav-link active" disabled>
                                <i class="far fa-edit"></i><span class="d-none d-sm-inline-block ml-2">Create Inquiry</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button data-toggle="tab" aria-expanded="true" class="product nav-link" disabled>
                                <i class="mdi mdi-cash-multiple"></i> <span class="d-none d-sm-inline-block ml-2">Add
                                    Product</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="create-inq">
                            @include('pages.inquiry.components.create.create-content')
                        </div>
                        <div class="tab-pane " id="product">

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- end row -->


    </div> <!-- end container-fluid -->
@endsection

@section('js-vendor')
    <script>
        $(document).ready(function() {
            $("#datepicker-autoclose").datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
            });
        });
        $('.channel').change(function() {
            var channel = $(this).val();
            if (channel == 19) {
                $('.platform-class').css('display', 'block')
            } else {
                $('.platform-class').css('display', 'none')
                $('#platform').val('');
            }
        });
        $('.customer_category').change(function() {
            var customer_category = $(this).val();
            if (customer_category == 'exis_customer') {
                $('.text_customer').css('display', 'none');
                $('.select_customer').css('display', 'block');
                $('.customer_name').attr("required", "required");
                $('.name').removeAttr("required");
            } else {
                $('.select_customer').css('display', 'none');
                $('.text_customer').css('display', 'block');
                $('.customer_name').removeAttr("required");
                $('.name').attr("required", "required");
            }
        });
    </script>

    @include('pages.inquiry.js.submitInquiryForm')

    @if (session('success'))
        <input type="hidden" id="session-success" value="{{ session('success') }}">
        <script>
            $(document).ready(function() {
                // console.log($('#session-success').val())
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


    {{-- <!-- Required datatable js -->
    <script src="{{ URL::to('/') }}/assets/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Responsive examples -->
    <script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.min.js"></script> --}}

    <!-- Plugins js -->
    <script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/parsleyjs/parsley.min.js"></script>


    <!-- Init js-->
    <script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pages/form-pickers.init.js"></script>
@endsection
