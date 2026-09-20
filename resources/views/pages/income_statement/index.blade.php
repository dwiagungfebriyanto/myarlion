@extends('layouts.base')

@section('title', 'Income Statement')

@section('css')
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #printArea,
        #printArea * {
            visibility: visible;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
        }
    }

</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card card-body">
                <form action="{{ route('accounting.income_statement.index') }}" id="period-form"
                    class="form-horizontal" method="get">
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="period">Period</label>
                        <div class="col-md-10">
                            <input class="form-control" id="period" type="month" name="period"
                                value="{{ $inputPeriod }}"
                                placeholder="{{ date('Y-m') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-12" id="printArea">
            <div class="card-box" style="padding-right: 40px; padding-left: 40px">
                <div class="row mt-3">
                    <div class="col-4">
                        <img src="{{ asset('assets/images/logo-indococo.png') }}" class="mt-1"
                            width="200">
                    </div>

                    <div class="col-5">
                        <p style="font-size: 9px">
                            <span style="font-weight: bold">PT D&W Internasional</span> <br>
                            <span style="font-style: italic">Office :</span> <br>
                            Jl Jend A Yani (By Pass) Kawasan Grage City <br>
                            Biz Center Oasis Block A VII / 9 Cirebon 45113, West Java - Indonesia <br>
                            Tel. +62 231 8802888, 8802788 <br>
                            Fax. +62 231 8491546 <br>
                            Email. info@dw-corporation.com <br>
                            www.dw-corporation.com
                        </p>
                    </div>

                    <div class="col-3">
                        <p style="font-size: 9px" class="mt-2">
                            <span style="font-style: italic">Factory :</span> <br>
                            Lengkong Wetan, Majalengka
                            <br> West Java - Indonesia West Java - INDONESIA
                        </p>
                    </div>
                </div>

                <hr style="border: 3px solid black;" class="mt-0">

                <h5 class="mt-0 d-flex justify-content-center">INCOME STATEMENT</h5>
                <h5 class="mt-0 d-flex justify-content-center">
                    {{ $documentMonth }}
                </h5>

                <h5 class="mt-3" style="text-decoration: underline">SALES</h5>
                <h5>Gross Profit:</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Date</th>
                                        <th>Job</th>
                                        <th>Amount (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($salesData['sales']) > 0)
                                        @foreach($salesData['sales'] as $sales)
                                            <tr>
                                                <td>{{ "$loop->iteration." }}</td>
                                                <td>{{ date('Y-m', strtotime($sales->period_job)) }}</td>
                                                <td>
                                                    <a href="{{ route('job_statement.index', $sales->id) }}" target="_blank" class="text-dark">
                                                        {{ $sales->jobDesc() }}</a>
                                                </td>
                                                <td>{{ currencyFormat($sales->gross_profit) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">There is no data.</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td colspan="3" class="text-right" style="font-weight: bold; width: 75%">
                                            TOTAL SALES :
                                        </td>
                                        <td style="font-weight: bold">
                                            {{ currencyFormat($salesData['totalSalesGP']) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <h5 class="mt-3" style="text-decoration: underline">EXPENSES</h5>
                <ol type="I" style="font-weight: bold">
                    @foreach($expenseData as $outcomeGroup)
                        <li>
                            <h5>{{ $outcomeGroup['group_name'] }}</h5>
                        </li>

                        <ol type="A" style="font-weight: bold">
                            @foreach ($outcomeGroup['types'] as $outcomeType)
                                <li>{{ $outcomeType['type_name'] }}</li>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table" style="font-weight: normal">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Date</th>
                                                        <th>Description</th>
                                                        <th>Amount (IDR)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($outcomeType['items'] as $item)
                                                        <tr>
                                                            <td>{{ "$loop->iteration." }}</td>
                                                            <td>{{ $item['date'] }}</td>
                                                            <td>
                                                                <a href="{{ $item['edit_url'] }}" target="_blank" class="text-dark">
                                                                    {{ $item['description'] ?? '-' }}
                                                                </a>
                                                            </td>
                                                            @php
                                                                $amountPrefix = $item['source'] === 'other_income' ? '-' : '';
                                                            @endphp
                                                            <td>{{ currencyFormat($item['amount'], $amountPrefix) }}</td>
                                                        </tr>
                                                    @endforeach

                                                    <tr>
                                                        <td colspan="3" class="text-right"
                                                            style="font-weight: bold; width: 75%">
                                                            TOTAL :
                                                        </td>
                                                        <td style="font-weight: bold">
                                                            {{ currencyFormat($outcomeType['total']) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </ol>

                        @php
                            $totalExpenses += $outcomeGroup['grand_total'];
                        @endphp
                    @endforeach
                </ol>

                <h5 class="mt-3" style="text-decoration: underline">PROFIT</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody style="font-weight: bold">
                                    <tr>
                                        <td class="text-right" style="width: 75%">
                                            TOTAL SALES :
                                        </td>
                                        <td>
                                            {{ currencyFormat($salesData['totalSalesGP']) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="text-right">
                                            TOTAL EXPENSES :
                                        </td>
                                        <td>
                                            {{ currencyFormat($totalExpenses) }}
                                        </td>
                                    </tr>

                                    <tr style="text-decoration: underline">
                                        <td class="text-right">NET/LOSS PROFIT :</td>
                                        <td>
                                            {{ currencyFormat($salesData['totalSalesGP'] - $totalExpenses) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr style="border: 1px solid black;" class="mt-0">

                <p class="mt-3">Yogyakarta, {{ $documentMonth }}</p>
                <h5 class="mt-5" style="text-decoration: underline; font-weight: bold">
                    Fajar Stevano
                </h5>
                <span class="mt-0" style="font-style: italic">Marketing Director</span>


                <div class="hidden-print mt-4">
                    <div class="text-right d-print-none">
                        <a href="javascript:window.print()" class="btn btn-blue waves-effect waves-light">
                            <i class="fa fa-print mr-1"></i> Print</a>
                        <a href="{{ route('accounting.income_statement.get_excel', ['periode' => $inputPeriod]) }}"
                            class="btn btn-success waves-effect waves-light">
                            <i class="fa fa-print mr-1"></i> Excel</a>
                    </div>
                </div>
            </div>

        </div>

    </div> <!-- end row -->


</div> <!-- end container-fluid -->
@endsection

@section('js-vendor')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
    integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $(function () {
        $('#period').datepicker({
            format: 'yyyy-mm',
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

        $('#period').change(function (e) {
            $('#period-form').submit()
        });
    });

</script>
@endsection
