@if($job->statusPaymentIsOpen() && auth()->user()->can('add income job'))
    <button type="button" class="btn btn-success waves-effect waves-light btn-add mr-2 my-2"
        data-toggle="modal" data-target="#addIncomeModal">
        <i class="fas fa-plus-square mr-1"></i> Add Transaction</button>
@endif


<div id="addIncomeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="job-income-form" action="{{ route('job_income.store', $job) }}"
                method="post" class="parsley-form">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add Income</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="validate-input">
                            <label for="datetime">Datetime<span class="text-danger">*</span></label>
                            <input id="datetime" class="form-control" type="datetime-local" name="datetime"
                                value="{{ old('datetime') ?: date('Y-m-d H:i') }}"
                                required>
                        </div>

                        @error('datetime')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="bank-account">Bank Account<span class="text-danger">*</span></label>
                            <select id="bank-account" class="form-control select2" name="bank_account" required>
                                <option selected disabled>-- Select Bank Account --</option>

                                @foreach($bankAccounts as $bankAccount)
                                    <option value="{{ $bankAccount->id }}">
                                        {{ $bankAccount->bankAccountLabel() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('bank_account')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="nominal-income">Nominal ({{ $job->currency->currency_code }})
                                <span class="text-danger">*</span></label>

                            <input id="nominal-income" type="text" name="nominal"
                                data-a-sign="{{ $job->currency->currency_code }} "
                                class="form-control autonumber disableEnterSubmit" required>
                        </div>

                        @error('nominal')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($job->currency->currency_code !== 'IDR')
                        @php
                            $currencyCode = $job->currency->currency_code;

                            $conversionRates = [
                                'USD' => 14500,
                                'AUD' => 10000,
                                'EUR' => 15500,
                            ];

                            $convertionRate = $conversionRates[$currencyCode] ?? 1;
                        @endphp

                        <input type="hidden" class="form-control" name="convertion_rate" id="convertion-rate"
                            value="{{ $convertionRate }}" />

                        <div class="form-group">
                            <div class="validate-input">
                                <label for="convertion">Convertion to IDR<span class="text-danger">*</span></label>
                                <input id="convertion" type="text" data-a-sign="Rp " name="convertion"
                                    class="form-control autonumber disableEnterSubmit" readonly>
                            </div>

                            @error('convertion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="submit-add">Add</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        $('#nominal-income').keyup(function (e) {
            let convertionRate = $('#convertion-rate').val();
            let rawIncome = $(this).autoNumeric('get');
            let nominal = rawIncome * convertionRate;

            $('#convertion').autoNumeric('set', nominal);
        });

        $('#submit-add').click(function (e) {
            let rawIncome = $('#nominal-income').autoNumeric('get');
            $('#nominal-income').val(rawIncome)

            if ($('#convertion').length) {
                rawConvertion = $('#convertion').autoNumeric('get');
                $('#convertion').val(rawConvertion)
            }

            $('#job-income-form').submit();
        });
    });

</script>
@endpush
