@extends('layouts.base')
@section('title', 'Add Product Job')

@section('css')

    {{-- datatables --}}
    <link href="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />

    <link href="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <link href="{{ URL::to('/') }}/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"
        type="text/css" />
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
                        <h4 class="header-title mb-3">Add Product to Job</h4>
                    </div>
                    <div class="col-12 text-right">
                        <button type="button" class="btn btn-success waves-effect waves-light btn-add-product"
                            data-toggle="modal" data-target="#createProductModal"
                            data-url="{{ route('job.list.product.create.modal', $job_id) }}"><i class="mdi mdi-plus mr-1">
                            </i> Add New Product</button>
                    </div>
                    <br>
                    {{ $dataTable->table(
                        attributes: [
                            'class' => 'table table-bordered table-bordered dt-responsive nowrap',
                            'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
                        ],
                    ) }}
                    <!-- end row -->
                    <div class="col-12 text-right">
                        <a href="{{route('job.list.index', $request = 'success')}}" class="btn btn-primary waves-effect waves-light mt-4 btn-Next"><i
                                class="mdi mdi-arrow-collapse-right mr-2"></i> Submit All</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

    </div> <!-- end container-fluid -->

    <!-- CREATE product MODAL -->
    <div id="createProductModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">

        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- END CREATE product MODAL -->

    <!-- EDIT product MODAL -->
    <div id="editProductModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">

        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- END EDIT product MODAL -->

@endsection

@section('js-vendor')

    <script>
        $('.btn-add-product').click(function(e) {
            let data = $(this).data();
            $.ajax({
                method: "get",
                url: data.url,
                success: function(response) {
                    $('#createProductModal').find('.modal-dialog').html(response);
                }
            })
        });
    </script>

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
    @elseif (request()->has('success'))
        <script>
            $(document).ready(function() {
                $.toast({
                    heading: "Success!",
                    text: "Data Job has been saved successfully",
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
    @elseif (session('danger'))
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
    <!-- Required datatable js -->
    <script src="{{ URL::to('/') }}/assets/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Responsive examples -->
    <script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush
