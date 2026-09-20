@extends('layouts.base')
@section('title', $pageTitle)


@section('css')
<style>
    .po-history-table td,
    .po-history-table th {
        vertical-align: middle;
        white-space: nowrap;
    }

    .po-history-table .col-description,
    .po-history-table .col-reference,
    .po-history-table .col-remark {
        white-space: normal;
        min-width: 220px;
    }

    .po-history-row-inventory {
        background-color: #fff7b8;
    }

    .po-history-row-job {
        background-color: #d9f2e6;
    }

    .po-history-layout {
        align-items: flex-start;
    }

    .po-history-sidebar {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        background-color: #f8fafc;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .po-history-sidebar-body {
        padding: 0.75rem;
    }

    .po-history-sidebar-header {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        color: #334155;
        background-color: #eef2f7;
    }

    .po-history-tabs {
        padding: 0;
        gap: 0.625rem;
    }

    .po-history-tabs .nav-link {
        border: 1px solid #dbe4ee;
        border-radius: 0.5rem;
        padding: 0.875rem 1rem;
        background-color: #fff;
        color: #475569;
        font-weight: 600;
        transition: all 0.15s ease-in-out;
    }

    .po-history-tabs .nav-link:hover {
        border-color: #94a3b8;
        color: #1e293b;
    }

    .po-history-tabs .nav-link.active {
        background-color: #e0f2fe;
        border-color: #38bdf8;
        color: #0f172a;
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.12);
    }

    .po-history-tab-title,
    .po-history-tab-meta {
        display: block;
    }

    .po-history-tab-title {
        white-space: normal;
        word-break: break-word;
        line-height: 1.4;
    }

    .po-history-tab-meta {
        margin-top: 0.35rem;
        font-size: 0.85rem;
        color: #64748b;
    }

    .po-history-summary {
        padding: 1rem 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        background-color: #fff;
    }

    @media (min-width: 992px) {
        .po-history-sidebar-body {
            max-height: 520px;
            overflow-y: auto;
        }
    }

    @media (max-width: 991.98px) {
        .po-history-sidebar-body {
            max-height: 320px;
            overflow-y: auto;
        }
    }
</style>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('purchase_orders.index') }}"
                        class="btn btn-primary btn-rounded waves-effect waves-light">
                        <i class="mdi mdi-arrow-left mr-1"></i> Back
                    </a>
                    <h4 class="mb-0 ml-2">{{ $pageTitle }}</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <p class="mb-1"><strong>PO:</strong> {{ $poStock->unique_id }}</p>
                            <p class="mb-1"><strong>Type:</strong> {{ ucfirst($poStock->po_type) }}</p>
                            <p class="mb-1"><strong>Supplier:</strong> {{ $poStock->supplier->supplier_name }}</p>
                            <p class="mb-0"><strong>Status:</strong> {{ ucfirst($poStock->status) }}</p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p class="mb-0"><strong>Item Count:</strong> {{ $itemCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($productHistories->isNotEmpty())
                        <div class="row po-history-layout">
                            <div class="col-12 col-lg-4 col-xl-3">
                                <div class="po-history-sidebar">
                                    <div class="po-history-sidebar-header">Products</div>
                                    <div class="po-history-sidebar-body">
                                        <div class="nav flex-column nav-pills po-history-tabs" role="tablist" aria-orientation="vertical">
                                            @foreach($productHistories as $productHistory)
                                                <a href="#{{ $productHistory['tab_id'] }}"
                                                    data-toggle="tab"
                                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                    class="nav-link {{ $loop->first ? 'active' : '' }}">
                                                    <span class="po-history-tab-title">
                                                        {{ $productHistory['tab_product_display'] }} ({{ $productHistory['unit_name'] }})
                                                    </span>
                                                    <span class="po-history-tab-meta">
                                                        SKU: {{ $productHistory['sku'] }} | Remaining: {{ $productHistory['remaining_qty_display'] }} {{ $productHistory['unit_name'] }}
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-8 col-xl-9">
                                <div class="tab-content">
                                    @foreach($productHistories as $productHistory)
                                        <div class="tab-pane {{ $loop->first ? 'show active' : '' }}" id="{{ $productHistory['tab_id'] }}">
                                            <div class="po-history-summary mb-3">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <p class="mb-1"><strong>Total PO Qty:</strong> {{ $productHistory['total_qty_display'] }} {{ $productHistory['unit_name'] }}</p>
                                                        <p class="mb-0"><strong>Remaining Qty:</strong> {{ $productHistory['remaining_qty_display'] }} {{ $productHistory['unit_name'] }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered po-history-table mb-0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Date</th>
                                                            <th class="col-description">Description</th>
                                                            <th>Qty</th>
                                                            <th>Stock</th>
                                                            <th>Value</th>
                                                            <th class="col-reference">Reference</th>
                                                            <th class="col-remark">Remark</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($productHistory['events'] as $event)
                                                            <tr class="{{ $event['row_variant'] === 'inventory' ? 'po-history-row-inventory' : 'po-history-row-job' }}">
                                                                <td>{{ $event['date_display'] }}</td>
                                                                <td class="col-description">{{ $event['description'] }}</td>
                                                                <td class="{{ $event['row_variant'] === 'inventory' ? 'text-success' : 'text-danger' }}">
                                                                    {{ $event['qty_display'] }} {{ $event['unit_name'] }}
                                                                </td>
                                                                <td>{{ $event['running_stock_display'] }} {{ $event['unit_name'] }}</td>
                                                                <td>{{ $event['value_display'] }}</td>
                                                                <td class="col-reference">
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
                                                                <td colspan="7" class="text-center">No history.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="mb-0 text-center">No history.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- /.row --}}
</div>
{{-- /.container-fluid --}}
@endsection


@section('js-vendor')
<script src="{{ asset('assets/js/helper.js') }}"></script>
@endsection


@section('scripts')
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
