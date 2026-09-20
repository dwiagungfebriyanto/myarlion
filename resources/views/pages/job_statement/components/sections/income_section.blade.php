@include('pages.job_statement.components.modals.currency_converter')

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <h4 class="header-title">Income</h4>

            <div class="row">
                <div class="col-md">
                    @if($job->statusIsOpen() && auth()->user()->can('job statement edit currency'))
                        <button type="button" class="btn btn-success waves-effect waves-light btn-add mr-2 my-2"
                            data-toggle="modal" data-target="#converterModal" data-url="">
                            <i class="fas fa-money-bill-alt mr-1"></i> Currency Converter
                        </button>
                    @endif
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Payment</th>
                        <th>Currency Rate</th>
                        <th>Amount (IDR)</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($job->jobIncomes as $jobIncome)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('Y-m-d H:i', strtotime($jobIncome->date)) }}</td>
                            <td>
                                {{ currencyFormat($jobIncome->payment, $jobIncome->currency->currency_code) }}
                            </td>
                            <td>{{ currencyFormat($jobIncome->to_idr) }}</td>
                            <td>{{ currencyFormat($jobIncome->nominal) }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="6" class="text-right">
                            <span class="total-label">TOTAL INCOME: {{ currencyFormat($job->totalIncome()) }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- /.table-responsive --}}
    </div>
    {{-- /.col-md-12 --}}
</div>
{{-- /.row --}}
<hr class="mb-4">
