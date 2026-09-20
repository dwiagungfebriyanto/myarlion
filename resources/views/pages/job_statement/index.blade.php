@include('pages.job_statement.components.sections.detail_section')
@include('pages.job_statement.components.sections.income_section')
@include('pages.job_statement.components.sections.expense_section')
@include('pages.job_statement.components.sections.sample_section')
@include('pages.job_statement.components.sections.stock_section')
@include('pages.job_statement.components.sections.po_stock_section')

<div class="container">
    <div class="row">
        <div class="col-4">
            <div class="form-group row total-label">
                <label class="col-4 col-form-label">TOTAL INCOME:</label>
                <div class="col-8 mt-auto mb-auto">
                    <input type="text" class="form-control autonumber" data-a-sign="Rp "
                        value="{{ $job->totalIncome() }}" readonly>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="form-group row total-label">
                <label class="col-4 col-form-label">OVERALL EXPENSES:</label>
                <div class="col-8 mt-auto mb-auto">
                    <input type="text" class="form-control autonumber" data-a-sign="Rp " value="{{ $overallExpenses }}"
                        readonly>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="form-group row total-label">
                <label class="col-4 col-form-label">GROSS PROFIT:</label>
                <div class="col-8 mt-auto mb-auto">
                    <input type="text" id="gross-profit" class="form-control autonumber" data-a-sign="Rp "
                        value="{{ $grossProfit }}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>

@include('pages.job_statement.components.sections.commission_section')

<div class="container">
    <div class="row justify-content-end">
        <div class="col-4">
            <div class="form-group row total-label">
                <label class="col-4 col-form-label">NET PROFIT:</label>
                <div class="col-8 mt-auto mb-auto">
                    <input type="text" class="form-control autonumber" data-a-sign="Rp "
                        value="{{ $netProfit }}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>

<div class="container">
    @php
        $showCloseBtn = ($job->statusIsOpen() && $job->commissions()->exists());
        $btnColor = $showCloseBtn ? 'btn-primary' : 'btn-secondary';

        $btnTitle = $job->statusIsOpen() ? 'close' : 'open';
        $disableCloseBtn = $job->statusPaymentIsOpen() ? 'disabled' : '';
        $disableCloseHoverTxt = $job->statusPaymentIsOpen()
        ? 'Button is disabled because payment/income is still open'
        : '';
    @endphp

    @can("job statement $btnTitle job")
        <form action="{{ route('job_statement.change_status', $job) }}" id="formChangeStatus" method="get">
            <input type="hidden" name="total_expenses" value="{{ $overallExpenses }}">
            <input type="hidden" name="gross_profit" value="{{ $grossProfit }}">
            <input type="hidden" name="sales_commission" value="{{ $totalCommission }}">
            <input type="hidden" name="net_profit" value="{{ $netProfit }}">
        </form>

        <button type="button" class="btn {{ $btnColor }} text-capitalize" data-toggle="tooltip" data-placement="top" title=""
            data-original-title="{{ $disableCloseHoverTxt }}" id="closeJobBtn"
            {{ $disableCloseBtn }}>{{ "$btnTitle job" }}</button>
    @endcan
</div>

@push('scripts')
    <script>
        $(function () {
            $('#closeJobBtn').click(function() {
                if(!$(this).is(':disabled')) {
                    $('#formChangeStatus').submit();
                }
            });
        });
    </script>
@endpush
