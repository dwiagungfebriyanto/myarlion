@extends('layouts.base')
@section('title', 'Product')

@section('css')

    <link href="{{ URL::to('/') }}/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />

    <link href="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />


    {{-- datatables --}}
    <link href="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />

    <style>
        .help-block {
            color: red
        }

        .sku-info-list {
            padding-left: 1.25rem;
            margin-bottom: 0;
        }

        .sku-example {
            font-weight: 600;
            letter-spacing: 0.04em;
            word-break: break-all;
        }

        .sku-example span {
            display: inline-block;
        }

        .sku-part-main-category {
            color: #c0392b;
        }

        .sku-part-sub-category {
            color: #d68910;
        }

        .sku-part-brand {
            color: #1e8449;
        }

        .sku-part-product-type {
            color: #148f77;
        }

        .sku-part-specification {
            color: #2471a3;
        }

        .sku-part-packaging {
            color: #7d3c98;
        }

        .sku-part-supplier {
            color: #566573;
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
                    <div class="d-flex">
                        <h4 class="header-title mb-3">Product List</h4>
                        <div class="ml-auto">
                            @can('add product')
                            <button type="button" class="btn btn-success waves-effect waves-light btn-rounded btn-add"
                                data-toggle="modal" data-target="#createModal"
                                data-url="{{ route('product.list.create') }}"><i class="mdi mdi-plus mr-1"></i> Add
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

                    <div class="alert alert-info mt-3 mb-0" role="alert">
                        <h5 class="alert-heading mb-2">Informasi SKU</h5>
                        <ul class="sku-info-list">
                            <li>
                                Struktur SKU saat ini adalah
                                <code>main_category_id + sub_category.code + brand.code + product_type.code + specification.code + packaging.code + supplier.code</code>.
                                Segmen pertama berasal dari <code>main_category_id</code> yang dikirim dari form, sehingga yang tampil di SKU adalah angka ID Main Category, bukan
                                <code>main_categories.code</code>.
                            </li>
                            <li>
                                Contoh SKU:
                                <span class="sku-example d-inline-block mt-1">
                                    <span class="sku-part-main-category">3</span><span class="sku-part-sub-category">4</span><span class="sku-part-brand">0</span><span class="sku-part-product-type">126</span><span class="sku-part-specification">016</span><span class="sku-part-packaging">0025</span><span class="sku-part-supplier">008</span>
                                </span>
                                <span class="d-block mt-2">
                                    <span class="sku-part-main-category">3 = Main Category ID</span>,
                                    <span class="sku-part-sub-category">4 = Sub Category Code</span>,
                                    <span class="sku-part-brand">0 = Brand Code</span>,
                                    <span class="sku-part-product-type">126 = Product Type Code</span>,
                                    <span class="sku-part-specification">016 = Specification Code</span>,
                                    <span class="sku-part-packaging">0025 = Packaging Code</span>,
                                    <span class="sku-part-supplier">008 = Supplier Code</span>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- end row -->
                </div>
            </div>
        </div>
        <!-- end row -->



    </div> <!-- end container-fluid -->

    {{-- VALIDASI MENGGUNAKAN PARSLEY JS SAJA --}}

    <!-- CREATE MODAL -->
    <div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">

        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- END CREATE MODAL -->

    <!-- EDIT MODAL-->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">

        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!--END EDIT MODAL-->

    <!-- SHOW MODAL-->
    @include('pages.product.list.modals.view-modal')
    <!-- END SHOW MODAL-->


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

    <script>
        $('.btn-add').click(function(e) {
            let data = $(this).data();
            $.ajax({
                method: "get",
                url: data.url,
                success: function(response) {
                    $('#createModal').find('.modal-dialog').html(response);
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
