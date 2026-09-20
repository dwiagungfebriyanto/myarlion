@extends('layouts.base')
@section('title', $pageTitle)


@section('css')
{{-- datatables --}}
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}"
    type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}"
    type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/buttons.bootstrap4.css') }}"
    type="text/css" />

<link rel="stylesheet" href="{{ asset('assets/libs/select2/select2.min.css') }}"
    type="text/css" />
<style>
    .show-all-filter-label {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .inventory-filter-stack {
        max-width: 320px;
        margin-left: auto;
        position: relative;
        z-index: 10;
    }

    .inventory-filter-stack .select2-container {
        width: 100% !important;
    }
</style>
@endsection


@section('content')
@php
    $isSamplePage = request('type') === 'sample';
    $showAllData = request()->boolean('show_all');
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex mb-3 align-items-start">
                    <h4 class="header-title mb-0">{{ $pageTitle }}</h4>
                </div>

                <form action="{{ url()->current() }}" id="formFilter" method="get" class="mb-3">
                    @if ($isSamplePage)
                        <input type="hidden" name="type" value="sample">
                    @endif

                    <div class="row justify-content-md-end justify-content-sm-center">
                        <div class="col-sm-12 col-md-4 col-lg-3">
                            <div class="inventory-filter-stack">
                                <select class="form-control select2" id="filterWarehouse" name="warehouse">
                                    {!! selectGenerate('All Warehouse', $warehouses, 'id', 'warehouse_name',
                                    request('warehouse')) !!}
                                </select>

                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox"
                                        class="custom-control-input"
                                        id="showAllData"
                                        name="show_all"
                                        value="1"
                                        {{ $showAllData ? 'checked' : '' }}>
                                    <label class="custom-control-label show-all-filter-label" for="showAllData">
                                        <span>Show All Data</span>
                                        <button type="button"
                                            class="btn btn-xs btn-secondary py-0 px-1"
                                            data-toggle="popover"
                                            data-trigger="focus"
                                            data-placement="top"
                                            data-content="Jika dicentang, halaman ini menampilkan semua row inventory untuk filter saat ini, termasuk stock 0 dan stock negatif bila ada anomali saldo. Jika tidak dicentang, hanya stock dengan qty lebih dari 0 yang ditampilkan.">
                                            <i class="mdi mdi-information-outline"></i>
                                        </button>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <table id="stockDataTable" class="table table-bordered table-bordered dt-responsive"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 10%">ID</th>
                            <th>Stock</th>
                            <th>Product</th>
                            <th>PO</th>
                            <th>Supplier</th>
                            <th>Warehouse</th>
                            <th>Purchase Cost</th>
                            <th>Purchase Cost per Unit</th>
                            <th>Stock Bucket</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($inventoryStocks as $inventoryStock)
                            @php
                                $poStock = $inventoryStock->getPoStock();
                            @endphp
                            <tr>
                                <td>{{ $inventoryStock->id }}</td>
                                <td>{{ $inventoryStock->getStock() ?? '' }}</td>
                                <td>
                                    {{ $inventoryStock->product?->skuFormat() ?? '' }}
                                </td>
                                <td>
                                    @if(!is_null($poStock))
                                        <a href="{{ route('po_stock.detail', $poStock->id) }}"
                                            target="_blank">
                                            {{ $poStock->unique_id }}
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $poStock?->supplier?->supplier_name ?? '-' }}</td>
                                <td>{{ $inventoryStock->warehouse->warehouse_name }}</td>
                                <td>{{ currencyFormat($inventoryStock->purchase_cost) }}</td>
                                <td>{{ currencyFormat($inventoryStock->purchase_cost_per_unit) }}</td>
                                <td>{{ ucfirst($inventoryStock->getStockBucket()) }}</td>
                                <td>{{ humanizeDate($inventoryStock->created_at) }}</td>
                                <td>{{ humanizeDate($inventoryStock->updated_at) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if(request('type') === 'sample' && !is_null($poStock))
                                            <a href="{{ route('product.inventory_stock.sample_history', $inventoryStock) }}"
                                                class="btn btn-sm btn-info mr-1"
                                                title="Sample History">
                                                <i class="mdi mdi-history"></i>
                                            </a>
                                        @elseif(request('type') !== 'sample' && !is_null($poStock))
                                            <a href="{{ route('product.inventory_stock.history', $inventoryStock) }}"
                                                class="btn btn-sm btn-info mr-1"
                                                title="Stock History">
                                                <i class="mdi mdi-history"></i>
                                            </a>
                                        @endif

                                        @if($inventoryStock->unitValue() > 0)
                                            <button type="button" 
                                                class="btn btn-sm btn-warning btn-return"
                                                title="Return Stock"
                                                data-id="{{ $inventoryStock->id }}"
                                                data-warehouse-id="{{ $inventoryStock->warehouse_id }}"
                                                data-warehouse-name="{{ $inventoryStock->warehouse->warehouse_name }}"
                                                data-product="{{ $inventoryStock->product?->skuFormat() ?? '' }}"
                                                data-unit="{{ $inventoryStock->unit->unit_name }}"
                                                data-price="{{ $inventoryStock->purchase_cost_per_unit }}"
                                                data-unit-id="{{ $inventoryStock->unit_id }}">
                                                <i class="mdi mdi-undo"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- /.card-box --}}
        </div>
        {{-- /.col-12 --}}
    </div>
    <!-- /.row -->
</div>
@include('pages.inventory_stock.components.create-return-modal')
<!-- /.container-fluid -->
@endsection


@push('scripts')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

    {{-- datatable server side --}}
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>


    <script>
        $(function () {
            $("#stockDataTable").DataTable({
                order: [
                    [10, "asc"]
                ],
            });

            $('#filterWarehouse').off('change.inventoryStockFilter').on('change.inventoryStockFilter', function () {
                $('#formFilter').submit();
            });

            $('#showAllData').off('change.inventoryStockFilter').on('change.inventoryStockFilter', function () {
                $('#formFilter').submit();
            });

            $('[data-toggle="popover"]').popover();
        });

    </script>
@endpush
