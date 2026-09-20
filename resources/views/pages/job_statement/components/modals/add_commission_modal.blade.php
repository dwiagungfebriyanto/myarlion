<div id="addSalesCommission" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addCommissionForm" method="post" class="parsley-form"
                action="{{ route('job_statement.store_job_commission', $job) }}">
                @csrf

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add Sales Commission</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="release-date">Release Date<span class="text-danger">*</span></label>
                            <input id="release-date" type="date" class="form-control" name="release_date"
                                value="{{ old('release_date') ?? date('Y-m-d') }}"
                                required>

                            @error('release_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="marketing">Marketing<span class="text-danger">*</span></label>
                            <select name="marketing" id="marketing" class="form-control select2" required>
                                <option disabled selected>-- Select Marketing --</option>
                                @foreach($jobTeams as $jobTeam)
                                    <option value=" {{ $jobTeam->marketing->id }}"
                                        data-percentage="{{ $jobTeam->job_percentage }}">
                                        {{ $jobTeam->marketing->name ." | Job percentage: $jobTeam->job_percentage%" }}
                                    </option>
                                @endforeach
                            </select>

                            @error('marketing')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="ten-percent">10% from Gross Profit</label>
                            <input type="text" id="ten-percent" class="form-control ten-percent autonumber"
                                value="{{ old('ten_percent') }}" readonly>

                            @error('ten_percent')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="percentage">Percentage<span class="text-danger">*</span></label>
                            <input type="text" data-a-sign="%" data-p-sign="s" class="form-control autonumber"
                                id="percentage" name="percentage" value="{{ old('percentage') }}"
                                required>

                            @error('ten_percent')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="nominal">Nominal<span class="text-danger">*</span></label>
                            <input type="text" name="nominal" class="form-control autonumber" id="nominal"
                                value="{{ old('nominal') }}" required>

                            @error('nominal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="note">Note</label>
                            <textarea name="note" class="form-control" id="note" cols="30"
                                rows="10">{{ old('note') }}</textarea>

                            @error('note')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- /.modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        id="btnAddCommission">Add</button>
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
            $('#addSalesCommission').on('shown.bs.modal', function () {
                let grossProfit = $('#gross-profit').autoNumeric('get');
                let tenPercent = Math.round((10 / 100) * grossProfit);

                $('#addCommissionForm #ten-percent').autoNumeric('set', tenPercent);
            });

            $('#addCommissionForm #percentage').keyup(function (e) {
                let percentage = $(this).autoNumeric('get');
                let profitTenPercent = $('#ten-percent').autoNumeric('get');
                let calculate = Math.round((percentage / 100) * profitTenPercent);

                $('#addCommissionForm #nominal').autoNumeric('set', calculate);
            });

            $('#btnAddCommission').click(function (e) {
                $('#addCommissionForm .autonumber').each(function (index, element) {
                    let rawValue = $(this).autoNumeric('get');

                    $(this).val(rawValue);
                });

                $('#addCommissionForm').submit();
            });

        });

    </script>
@endpush
