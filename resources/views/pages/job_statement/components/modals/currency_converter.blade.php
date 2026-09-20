<div id="converterModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white" id="myModalLabel">Currency Converter (to IDR)</h4>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Date</th>
                                <th>Payment</th>
                                <th>Currency Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <form id="converter-form" method="post" class="parsley-form"
                                action="{{ route('job_statement.convert_income', $job) }}">
                                @csrf

                                @foreach($job->jobIncomes as $jobIncome)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ date('Y-m-d H:i', strtotime($jobIncome->date)) }}
                                        </td>
                                        <td>
                                            {{ currencyFormat($jobIncome->payment, $jobIncome->currency->currency_code) }}
                                        </td>
                                        <td>
                                            <input type="text" class="form-control autonumber convert-form"
                                                name="rate[{{ $jobIncome->id }}]" required
                                                value="{{ currencyFormat($jobIncome->to_idr, '') }}">

                                            @error("rate[$jobIncome->id]")
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </form>
                        </tbody>
                    </table>
                </div>
                {{-- /.table-responsive --}}
            </div>
            {{-- /.modal-body --}}

            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect" id="btnSubmitConvert">Submit</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


@push('scripts')
    <script>
        $(function () {
            $('#btnSubmitConvert').click(function (e) {
                $('.convert-form').each(function (index, element) {
                    let rawValue = $(element).autoNumeric('get');

                    $(element).val(rawValue);
                });

                $('#converter-form').submit();
            });
        });

    </script>

@endpush
