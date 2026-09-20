@extends('layouts.base')
@section('title', $pageTitle)


@section('css')
{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />

<style>
    strong,
    .sorting,
    .sorting_asc,
    .sorting_desc {
        font-weight: bold !important;
    }

</style>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center px-2 flex-wrap mb-2">
                    <div class="mb-2 mb-md-0">
                        <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary btn-rounded">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('po_stock.history', $poStock->id) }}" class="btn btn-info">
                            <i class="fas fa-history mr-1"></i> History
                        </a>
                        <a href="{{ route('po_stock.export_excel', $poStock->id) }}" class="btn btn-success ml-2">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                        </a>
                        <a href="{{ route('po_stock.export_pdf', $poStock->id) }}" class="btn btn-danger ml-2" target="_blank">
                            <i class="fas fa-file-pdf mr-1"></i> Export PDF
                        </a>
                    </div>
                </div>
                <hr>

                <h4 class="header-title mb-3">{{ "PO " .ucfirst($poStock->po_type) .": $poStock->unique_id" }}</h4>

                <div class="row">
                    <div class="col-sm-12 col-md-6 mb-2">
                        <strong>Main Category:</strong>
                        {{ $poStock->mainCategory->main_category_name ?? '' }}
                    </div>
                    <div class="col-sm-12 col-md-6 mb-2">
                        <strong>Supplier:</strong>
                        {{ ($poStock->supplier) ? $poStock->supplier->code .' | ' .$poStock->supplier->supplier_name : '' }}
                    </div>
                    <div class="col-sm-12 col-md-6 mb-2">
                        <strong>Status:</strong>
                        {{ $poStock->status }}
                    </div>
                    <div class="col-sm-12 col-md-6 mb-2">
                        <strong>Shipping Cost:</strong>
                        {{ currencyFormat($poStock->shipping_cost) }}
                    </div>
                    <div class="col-sm-12 col-md-6 mb-2">
                        <strong>Additional Cost:</strong>
                        {{ currencyFormat($poStock->additional_expenses) }}
                    </div>
                    <div class="col-sm-12 col-md-6 mb-2">
                        <strong>Total:</strong>
                        {{ currencyFormat($poStock->total) }}
                    </div>
                    <div class="col-sm-12 col-md-6 mb-2">
                        <strong>Note:</strong>
                        {{ $poStock->note }}
                    </div>

                    <div class="col-12 mt-2">
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr class="text-center">
                                    <td>Product</td>
                                    <td>Quantity</td>
                                    <td>Price</td>
                                    <td>Purchase Cost</td>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < count($poStock->products); $i++)
                                    <tr>
                                        <td>{{ $poStock->products[$i]->skuFormat() }}</td>
                                        <td>{{ $poStock->poStockDetail[$i]->quantityFormat() ." | Remaining: " .$poStock->poStockDetail[$i]->quantityRemainingFormat() }}
                                        </td>
                                        <td>{{ currencyFormat($poStock->poStockDetail[$i]->price) .'/' .$poStock->poStockDetail[$i]->unit->unit_name }}
                                        </td>
                                        <td>{{ currencyFormat($poStock->poStockDetail[$i]->purchase_cost) .'/' .$poStock->poStockDetail[$i]->unit->unit_name }}
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- /.card-box --}}
        </div>
        {{-- /.col-12 --}}
    </div>
    {{-- /.row --}}
</div>
{{-- /.container-fluid --}}
@endsection


@section('js-vendor')
<script src="https://code.jquery.com/jquery-3.6.4.slim.js" crossorigin="anonymous"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4="></script>

<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $(function () {
        $("#responsive-datatable").DataTable({
            "responsive": true,
            searching: false,
            paging: false,
            info: false,
            lengthChange: false,
            pageLength: -1,
        });
    });

</script>
@endsection
