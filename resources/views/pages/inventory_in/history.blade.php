@extends('layouts.base')
@section('title', $pageTitle)


@section('css')
<style>
    .timeline-box {
        overflow: auto;
    }

    .detail-title {
        font-weight: bold;
    }

</style>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ back()->getTargetUrl() }}" class="btn btn-primary btn-rounded waves-effect waves-light">
                        <i class="mdi mdi-arrow-left mr-1"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <span class="detail-title">Datetime:</span>
                            {{ date('d F Y | H:i', strtotime($inventoryIn->datetime)) }}<br>

                            <span class="detail-title">Warehouse:</span>
                            {{ $inventoryIn->warehouse->warehouse_name }}<br>

                            <span class="detail-title">Product:</span>
                            {{ $inventoryIn->product->skuFormat() }}<br>

                            <span class="detail-title">Quantity:</span>
                            {{ $inventoryIn->quantityValue() }}<br>

                            <span class="detail-title">Current Stock:</span>
                            {{ optional($inventoryIn->inventoryStock)->quantityValue() ?? '-' }}<br>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            <span class="detail-title">Status:</span>
                            {{ $inventoryIn->status }}<br>

                            <span class="detail-title">Note:</span>
                            {{ $inventoryIn->notes }}<br>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="bg-white pl-3 pr-3 pb-1">
                <div class="timeline" dir="ltr">
                    <article class="timeline-item alt">
                        <div class="text-right">
                            <div class="time-show first">
                                <a href="#" class="btn btn-primary width-lg" style="font-weight: bold">
                                    History <br>
                                    Inventory <i class="fas fa-arrow-right"></i> Job <br>
                                    (total:
                                    {{ $inventoryToJobHistories->sum('quantity') .' ' .$inventoryIn->unit->unit_name }})
                                </a>
                            </div>
                        </div>
                    </article>

                    @if(count($inventoryToJobHistories) > 0)
                        @php
                            $leftPosition = true;
                        @endphp
                        @foreach($inventoryToJobHistories as $history)
                            <article
                                class="timeline-item {{ $leftPosition ? 'alt' : '' }}">
                                <div class="timeline-desk">
                                    <div class="panel">
                                        <div class="timeline-box">
                                            <span
                                                class="arrow{{ $leftPosition ? '-alt' : '' }}"></span>

                                            <span class="timeline-icon bg-primary">
                                                <i class="mdi mdi-adjust"></i></span>

                                            <h4 class="text-primary">
                                                {{ $history->created_at->diffForHumans() }}
                                            </h4>

                                            <p class="timeline-date text-muted">
                                                <small>{{ $history->created_at }}</small></p>
                                            <p>
                                                <ul style="font-weight: bold">
                                                    <li>
                                                        <span class="text-success">Job:</span>
                                                        @if($history->job)
                                                            <a href="{{ route('job_statement.index', $history->job->id) }}" target="_blank">
                                                                {{ $history->job->jobDesc() }}
                                                            </a>
                                                        @else
                                                            Job tidak ditemukan
                                                        @endif
                                                    </li>

                                                    <li>
                                                        <span class="text-success">Quantity:</span>
                                                        {{ "$history->quantity " .$inventoryIn->unit->unit_name }}
                                                    </li>
                                                </ul>
                                            </p>
                                        </div>
                                        {{-- /.timeline-box --}}
                                    </div>
                                    {{-- /.panel --}}
                                </div>
                                {{-- /.timeline-desk --}}
                            </article>

                            @php
                                $leftPosition = !$leftPosition;
                            @endphp
                        @endforeach
                    @else
                        <article class="timeline-item alt">
                            <div class="timeline-desk">
                                <div class="panel">
                                    <div class="timeline-box">
                                        <span class="arrow-alt"></span>
                                        <span class="timeline-icon bg-primary"><i class="mdi mdi-adjust"></i></span>
                                        <p>No history.</p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endif
                </div>
                {{-- /.timeline --}}
            </div>
            {{-- /.bg-white --}}
        </div>
        {{-- /col-12 --}}
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
