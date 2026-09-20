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
                        @can('add inventory out')
                            @include('pages.inventory_out.components.create-modal')
                        @endcan
                    </div>
                </div>
                <br>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 10%">ID</th>
                            <th>Datetime</th>
                            <th>Type</th>
                            <th>PO</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Warehouse</th>
                            <th>Original Warehouse</th>
                            <th>Quantity</th>
                            <th>Purchase Cost</th>
                            <th>Purchase Cost per Unit</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryOuts as $inventoryOut)
                        @php
                            $inventoryType = $inventoryOut->originalStock->getStockBucket() === 'sample' ? 'Sample' : 'Stock';
                            $poStock = $inventoryOut->getPoStock();
                            $supplier = $inventoryOut->getDisplaySupplier();
                        @endphp
                            <tr>
                                <td>{{ $inventoryOut->id }}</td>
                                <td>{{ $inventoryOut->datetime }}</td>
                                <td>{{ ucfirst($inventoryType) }}</td>
                                <td>
                                    @if($poStock)
                                        <a href="{{ route('po_stock.detail', $poStock->id) }}" target="_blank">
                                            {{ $poStock->unique_id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $inventoryOut->product?->skuFormat() }}</td>
                                <td>{{ $supplier?->supplier_name ?? '-' }}</td>
                                <td>{{ $inventoryOut->status }}</td>
                                <td>{{ $inventoryOut->warehouse->warehouse_name }}</td>
                                <td>{{ $inventoryOut->originalWarehouse()->warehouse_name }}</td>
                                <td>{{ $inventoryOut->quantityValue() }}</td>
                                <td>{{ currencyFormat($inventoryOut->purchase_cost) }}</td>
                                <td>{{ currencyFormat($inventoryOut->purchase_cost_per_unit) }}</td>
                                <td>
                                    @include('pages.inventory_out.components.action-button-2')
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

@include('pages.inventory_out.components.edit-modal')
@include('pages.inventory_out.components.detail-modal')

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
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>

<!-- Init js-->
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-masks.init.js') }}"></script>

<script src="{{ asset('assets/js/helper.js') }}"></script>

@if(session('success'))
    <script>
        toastSuccess({{ session('success') }});

    </script>
@endif

@if(session('danger'))
    <script>
        toastDanger({{ session('danger') }});

    </script>
@endif

@endsection


@push('scripts')
    <script>
        $(function () {
            $('#responsive-datatable').dataTable();
        });

        function getDetailData(url) {
            $.ajax({
                type: "get",
                url: url,
                dataType: "json",
                success: function (response) {
                    let {
                        productSkuFormat, 
                        inventoryOut, 
                        quantityValue, 
                        originalStock, 
                        warehouse, 
                        originalWarehouse, 
                        purchaseCost, 
                        purchaseCostPerUnit
                    } = response;

                    let inventoryType = originalStock.stock_bucket === 'sample' ? 'Sample' : 'Stock';
                    
                    $('#detail-product').text(productSkuFormat);
                    $('#detail-status').text(inventoryOut.status);
                    $('#detail-datetime').text(inventoryOut.datetime);
                    $('#detail-quantity').text(quantityValue);
                    $('#detail-type').text(inventoryType);
                    $('#detail-warehouse').text(warehouse.warehouse_name);
                    $('#detail-original-warehouse').text(originalWarehouse.warehouse_name);
                    $('#detail-notes').text(inventoryOut.notes ?? '-');
                    $('#detail-purchase-cost').text(purchaseCost);
                    $('#detail-purchase-cost-per-unit').text(purchaseCostPerUnit);
                }
            });
        }

        function getEditData(url) {
            $.ajax({
                type: "get",
                url: url,
                success: function (response) {
                    let inventoryOut = response.inventoryOut;

                    $('#form-update').attr('action', $('#form-update').data('url') +
                        '/' + inventoryOut.id);
                    $('#edit-inventory-stock').val(response.inventoryStockSku);
                    $('[name="inventory_stock"]').val(inventoryOut.original_stock_id);
                    $('#edit-status').val(inventoryOut.status);
                    $('#edit-datetime').val(response.inventoryOutDatetime);
                    $('#edit-unit').val(response.inventoryUnit.unit_name);
                    $('#edit-quantity').val(response.inventoryOutQuantity);
                    $('#edit-cost-per-unit').val(response.purchaseCostPerUnit);
                    $('#edit-warehouse').val(response.originalWarehouse.warehouse_name);
                    $('#edit-destination-warehouse').html(response.warehouseOptions);
                    $(`#edit-destination-warehouse option[value="${inventoryOut.warehouse_id}"]`)
                        .prop('selected', true)
                    $('#edit-notes').val(inventoryOut.notes);
                }
            });
        }

    </script>
@endpush
