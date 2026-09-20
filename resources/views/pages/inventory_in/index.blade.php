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

<script src="https://code.jquery.com/jquery-3.6.4.slim.js" crossorigin="anonymous"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4="></script>

<script src="{{ asset('assets/js/helper.js') }}"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex">
                    <h4 class="header-title mb-3">{{ $pageTitle }}</h4>
                    <div class="ml-auto">
                        @can('add inventory in')
                            @include('pages.inventory_in.components.create-modal')
                        @endcan
                    </div>
                </div>
                <br>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive display"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 10%">ID</th>
                            <th>Datetime</th>
                            <th>PO</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Quantity</th>
                            <th>Warehouse</th>
                            <th>Purchase Cost</th>
                            <th>Purchase Cost per Unit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventoryIns as $inventoryIn)
                        <tr>
                            <td>{{ $inventoryIn->id }}</td>
                            <td>{{ $inventoryIn->datetime }}</td>
                            <td>
                                @if ($inventoryIn->poStock)
                                    {!! $inventoryIn->poStock->po_type . ': <a href="' .route('po_stock.detail', $inventoryIn->poStock->id) .'" target="_blank">' ."{$inventoryIn->poStock->unique_id}</a>" !!}
                                @endif
                            </td>
                            <td>{{ !is_null($inventoryIn->product) ? $inventoryIn->product->skuFormat() : '' }}</td>
                            <td>{{ $inventoryIn->status }}</td>
                            <td>{{ $inventoryIn->quantityValue() }}</td>
                            <td>{{ $inventoryIn->warehouse->warehouse_name }}</td>
                            <td>{{ $inventoryIn->purchase_cost }}</td>
                            <td>{{ $inventoryIn->purchase_cost_per_unit }}</td>
                            <td>
                                @include('pages/inventory_in/components/action-button-2')
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- end row -->
            </div>
        </div>
        {{-- /.col-12 --}}
    </div>
    {{-- /.row --}}
</div>
{{-- /.container-fluid --}}

@include('pages.inventory_in.components.edit-modal')
@include('pages.inventory_in.components.detail-modal')

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
<script src="{{ URL::to('/') }}/assets/libs/jquery-mask-plugin/jquery.mask.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/autonumeric/autoNumeric-min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.js"></script>
<script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>

<!-- Init js-->
<script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{ URL::to('/') }}/assets/js/pages/form-masks.init.js"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>


<script>
    $(function () {
        $('#responsive-datatable').DataTable();

        setTimeout(() => {
            $('.autonumber').autoNumeric('init');
        }, 1000);
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
