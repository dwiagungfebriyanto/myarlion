@extends('layouts.base')
@section('title', 'Dashboard')

@section('css')
    <link href="{{ asset('assets/libs/custombox/custombox.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-6">
                @include('pages.dashboard.chart.inquiry_vs_sales')
            </div>

            <div class="col-md-6">
                @include('pages.dashboard.chart.inquiry_product_chart')
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                @include('pages.dashboard.chart.junk_inquiry')
            </div>
            <div class="col-md-6">
                @include('pages.dashboard.chart.inquiry_by_channel')
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                @include('pages.dashboard.chart.customer_report')
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                @include('pages.dashboard.chart.target_achievement')
            </div>

            <div class="col-md-6">
                @include('pages.dashboard.chart.target_achievement_monthly')
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                @include('pages.dashboard.chart.cost_category')
            </div>
            {{-- <div class="col-md-6">
                @include('pages.dashboard.chart.outstanding')
            </div> --}}
        </div>

        <div class="row">
            <div class="col-md-6">
                @include('pages.dashboard.chart.est_net_profit')
            </div>

            <div class="col-md-6">
                @include('pages.dashboard.chart.actual_profit')
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                @include('pages.dashboard.chart.country_origin')
            </div>

            <div class="col-md-6">
                @include('pages.dashboard.chart.shipment_destination')
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                @include('pages.dashboard.chart.profit_by_country')
            </div>
        </div>
    </div> <!-- end container-fluid -->
@endsection

@section('js-vendor')
    <!-- Google Charts js -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

    {{-- plugins --}}
    <script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>

    <!-- Init js-->
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('assets/js/helper.js') }}"></script>
@endsection
