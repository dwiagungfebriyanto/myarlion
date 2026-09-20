<div id="editIncomeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-income-edit" action="" method="post" class="parsley-form">
                @csrf
                {{-- @method('put') --}}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit Income</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="validate-input">
                            <label for="edit-datetime">Datetime<span class="text-danger">*</span></label>
                            <input id="edit-datetime" class="form-control" type="datetime-local" name="datetime" required>
                        </div>

                        @error('datetime')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="validate-input">
                            <label for="edit-bank-account">Bank Account<span class="text-danger">*</span></label>
                            <select id="edit-bank-account" class="form-control select2" name="bank_account" required>
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
                            <label for="edit-nominal-income">Nominal ({{ $job->currency->currency_code }})<span
                                    class="text-danger">*</span></label>
                            <input id="edit-nominal-income" type="text" required
                                data-a-sign="{{ $job->currency->currency_code }} "
                                class="form-control autonumber disableEnterSubmit" name="nominal">
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

                        <input type="hidden" class="form-control" name="convertion_rate" id="edit-convertion-rate"
                            value="{{ $convertionRate }}" />

                        <div class="form-group">
                            <div class="validate-input">
                                <label for="edit-convertion">Convertion to IDR<span class="text-danger">*</span></label>
                                <input id="edit-convertion" type="text" data-a-sign="Rp "
                                    class="form-control autonumber" name="convertion" readonly>
                            </div>

                            @error('convertion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="submit-update">Update</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


@push('scripts')
<script>
    $(function () {
        $('#edit-nominal-income').keyup(function (e) {
            let convertionRate = $('#edit-convertion-rate').val();
            let nominal = $('#edit-nominal-income').autoNumeric('get') * convertionRate;

            $('#edit-convertion').val(currencyFormat(nominal));
        });

        $('#submit-update').click(function (e) {
            let nominal = $('#edit-nominal-income').autoNumeric('get');
            $('#edit-nominal-income').val(nominal)

            if ($('#edit-convertion').length) {
                convertion = $('#edit-convertion').autoNumeric('get');
                $('#edit-convertion').val(convertion)
            }

            $('#form-income-edit').submit();
        });
    });

</script>
@endpush
