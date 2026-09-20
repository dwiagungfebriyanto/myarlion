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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex mb-2">
                    <h4 class="header-title mb-3">{{ $pageTitle }}</h4>

                    <div class="ml-auto">
                        @can('add free sample')
                            @include('pages.free_sample.components.modal_add')
                        @endcan
                    </div>
                </div>

                <form action="" id="formFilter" method="get" class="mb-1">
                    <div class="row justify-content-md-end justify-content-sm-center">
                        <div class="col-3">
                            <select id="filterWarehouse" name="warehouse" class="form-control select2">
                                {!! selectGenerate('All Warehouse', $warehouses, 'id', 'warehouse_name',
                                request('warehouse')) !!}
                            </select>
                        </div>
                    </div>
                </form>

                <table id="datatable" class="table table-bordered table-bordered dt-responsive responsive-datatable"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Delivered Date</th>
                            <th>Inquiry Date</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Warehouse</th>
                            <th>Source Inventory</th>
                            <th>PO Number</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($freeSamples as $freeSample)
                            @php
                                $inventoryStock = $freeSample->inventoryStock;
                                $poStock = $inventoryStock?->getPoStock();
                                $bucketLabel = ucfirst($inventoryStock?->getStockBucket() ?? '-');
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($freeSample->getRawOriginal('date'))->format('d/m/Y') }}</td>
                                <td>
                                    @if ($freeSample->inquiry?->date)
                                        {{ \Carbon\Carbon::parse($freeSample->inquiry->date)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $freeSample->recipientName() }}</td>
                                <td>{{ $freeSample->product()->skuFormat() }}</td>
                                <td class="text-center">{{ $freeSample->unitFormatted() }}</td>
                                <td>{{ $freeSample->warehouse()->warehouse_name }}</td>
                                <td>{{ $bucketLabel }}</td>
                                <td>
                                    @if ($poStock)
                                        <a href="{{ route('po_stock.detail', $poStock->id) }}" target="_blank">
                                            {{ $poStock->unique_id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $freeSample->created_at }}</td>
                                <td>{{ $freeSample->updated_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>

    <script>
        $(function () {
            $('#filterWarehouse').change(function (e) {
                e.preventDefault();
                $('#formFilter').submit();
            });
        });

    </script>
@endpush
