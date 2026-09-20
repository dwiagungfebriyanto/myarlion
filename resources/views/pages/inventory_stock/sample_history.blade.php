@extends('layouts.base')
@section('title', $pageTitle)

@section('css')
<style>
    .sample-history-table td,
    .sample-history-table th {
        vertical-align: middle;
        white-space: nowrap;
    }

    .sample-history-table .col-description,
    .sample-history-table .col-remark {
        white-space: normal;
        min-width: 220px;
    }

    .sample-row-in {
        background-color: #fff7b8;
    }

    .sample-row-out {
        background-color: #c9f0f0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ back()->getTargetUrl() }}" class="btn btn-primary btn-rounded waves-effect waves-light">
                        <i class="mdi mdi-arrow-left mr-1"></i> Back
                    </a>
                    <h4 class="mb-0 ml-2">{{ $pageTitle }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <p class="mb-1"><strong>PO:</strong> {{ $poStock->unique_id }}</p>
                            <p class="mb-1"><strong>Supplier:</strong> {{ $supplier->supplier_name }}</p>
                            <p class="mb-1"><strong>Product:</strong> {{ $product->skuFormat() }}</p>
                            <p class="mb-1"><strong>Warehouse:</strong> {{ $warehouse->warehouse_name }}</p>
                            <p class="mb-0"><strong>Unit:</strong> {{ $unit->unit_name }}</p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p class="mb-1"><strong>Current Stock:</strong> {{ $currentStockDisplay }} {{ $unit->unit_name }}</p>
                            <p class="mb-1">
                                <strong>Ledger Ending Stock:</strong>
                                {{ $ledgerEndingStockDisplay }} {{ $unit->unit_name }}
                                <button type="button"
                                    class="btn btn-xs btn-secondary ml-1 py-0 px-1"
                                    data-toggle="popover"
                                    data-trigger="focus"
                                    data-placement="top"
                                    data-content="Stock akhir hasil akumulasi seluruh event pada tabel history sample ini. Nilainya idealnya sama dengan Current Stock untuk scope supplier, product, dan warehouse yang sama.">
                                    <i class="mdi mdi-information-outline"></i>
                                </button>
                            </p>
                            <p class="mb-0"><strong>Anchor Sample ID:</strong> {{ $inventoryStock->id }}</p>
                        </div>
                    </div>

                    @if($hasBalanceMismatch)
                        <div class="alert alert-warning mt-3 mb-0">
                            Ledger ending stock belum sama dengan current stock. Periksa event histori sample terkait scope ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-sm table-bordered sample-history-table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>SKU</th>
                                <th>Date</th>
                                <th class="col-description">Description</th>
                                <th>Qty</th>
                                <th>Stock</th>
                                <th>Value</th>
                                <th>Reference</th>
                                <th class="col-remark">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr class="{{ $event['direction'] === 'in' ? 'sample-row-in' : 'sample-row-out' }}">
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $event['date_display'] }}</td>
                                    <td class="col-description">{{ $event['description'] }}</td>
                                    <td class="{{ $event['direction'] === 'in' ? 'text-success' : 'text-danger' }}">
                                        {{ $event['qty_display'] }} {{ $unit->unit_name }}
                                    </td>
                                    <td>{{ $event['running_stock_display'] }} {{ $unit->unit_name }}</td>
                                    <td>{{ $event['value_display'] }}</td>
                                    <td>
                                        @if(!empty($event['reference_url']))
                                            <a href="{{ $event['reference_url'] }}" target="_blank">
                                                {{ $event['reference'] }}
                                            </a>
                                        @else
                                            {{ $event['reference'] }}
                                        @endif
                                    </td>
                                    <td class="col-remark">{{ $event['remark'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No history.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="popover"]').popover();
    });
</script>
@endpush
