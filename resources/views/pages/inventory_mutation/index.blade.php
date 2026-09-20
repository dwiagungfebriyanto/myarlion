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
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}"
    rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex">
                    <h4 class="header-title mb-3">{{ $pageTitle }}</h4>
                    <div class="ml-auto">
                        @can('add inventory mutation')
                            @include('pages.inventory_mutation.components.create-modal')
                        @endcan
                    </div>
                </div>
                <br>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>New SKU</th>
                            <th>Warehouse</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Original SKU</th>
                            <th>Original Warehouse</th>
                            <th>Quantity Mutation</th>
                            <th>Original Stock ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($inventoryMutations as $inventoryMutation)
                            <tr>
                                <td>{{ $inventoryMutation->id }}</td>
                                <td>{{ $inventoryMutation->product?->skuFormat() }}</td>
                                <td>{{ $inventoryMutation->inventoryStock?->warehouse?->warehouse_name ?? '-' }}</td>
                                <td>{{ $inventoryMutation->quantityValue() }}</td>
                                <td>{{ currencyFormat($inventoryMutation->price) }}</td>
                                <td>{{ $inventoryMutation->originalSku?->skuFormat() }}</td>
                                <td>{{ $inventoryMutation->originalStock?->warehouse?->warehouse_name ?? '-' }}</td>
                                <td>{{ $inventoryMutation->quantityMutationValue() }}</td>
                                <td>{{ "$inventoryMutation->original_stock_type #$inventoryMutation->original_stock_id" }}</td>
                                <td>
                                    @include('pages.inventory_mutation.components.action-button')
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end row -->
</div> <!-- end container-fluid -->

@include('pages.inventory_mutation.components.edit-modal')

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
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>

<!-- Init js-->
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<script>
    $(function () {
        $('#responsive-datatable').dataTable();
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
