@extends('layouts.base')
@section('title', $pageTitle)

@section('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"
    type="text/css" />

{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex">
                    <h4 class="header-title mb-3">{{ $pageTitle }}</h4>
                    <div class="ml-auto">
                        @can('add inventory lost')
                            @include('pages.inventory_lost.components.create-modal')
                        @endcan
                    </div>
                </div>
                <br>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 10%">#</th>
                            <th>Datetime</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>From Inventory</th>
                            <th>Quantity</th>
                            <th>Warehouse</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($inventoryLosts as $inventoryLost)
                            @php
                                $sourceInventory = optional($inventoryLost->inventoryStock)->getStockBucket() === 'sample'
                                    ? 'Sample'
                                    : 'Stock';
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inventoryLost->datetime }}</td>
                                <td>{{ $inventoryLost->product->skuFormat() }}</td>
                                <td>{{ $inventoryLost->status }}</td>
                                <td>{{ "{$sourceInventory} #$inventoryLost->inventory_stock_id" }}</td>
                                <td>{{ $inventoryLost->quantityValue() }}</td>
                                <td>{{ $inventoryLost->warehouse->warehouse_name }}</td>
                                <td>
                                    @include('pages.inventory_lost.components.action-button')
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- end row -->
            </div>
        </div>
    </div>
    <!-- end row -->
</div> <!-- end container-fluid -->

@include('pages.inventory_lost.components.edit-modal')
@include('pages.inventory_lost.components.detail-modal')

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
<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>

<!-- Init js-->
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-masks.init.js') }}"></script>

<script src="{{ asset('assets/js/helper.js') }}"></script>

<script>
    $(function () {
        $('#responsive-datatable').dataTable()
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
