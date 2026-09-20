<div id="editSalesCommission" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCommissionForm" action="" method="post" class="parsley-form">
                @csrf
                @method('put')

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit Sales Commission</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-release-date">Release Date<span class="text-danger">*</span></label>
                            <input id="edit-release-date" type="date" class="form-control" name="release_date" required>

                            @error('release_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-marketing">Marketing<span class="text-danger">*</span></label>
                            <select name="marketing" id="edit-marketing" class="form-control select2" required>
                                <option disabled selected>-- Select Marketing --</option>
                                @foreach($jobTeams as $jobTeam)
                                    <option value="{{ $jobTeam->marketing->id }}"
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
                            <label for="edit-ten-percent">10% from Gross Profit</label>
                            <input type="text" id="edit-ten-percent" class="form-control ten-percent autonumber"
                                readonly>

                            @error('ten_percent')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-percentage">Percentage<span class="text-danger">*</span></label>
                            <input type="text" data-a-sign="%" data-p-sign="s" class="form-control autonumber"
                                id="edit-percentage" name="percentage" required>

                            @error('ten_percent')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-nominal">Nominal<span class="text-danger">*</span></label>
                            <input type="text" name="nominal" class="form-control autonumber" id="edit-nominal"
                                required>

                            @error('nominal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-note">Note</label>
                            <textarea name="note" class="form-control" id="edit-note" cols="30" rows="10"></textarea>

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
                        id="btnEditCommission">Update</button>
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
            $('#editSalesCommission').on('shown.bs.modal', function () {
                let grossProfit = $('#gross-profit').autoNumeric('get');
                let tenPercent = Math.round((10 / 100) * grossProfit);

                $('#editCommissionForm #edit-ten-percent').autoNumeric('set', tenPercent);
            });

            $('#editCommissionForm #edit-percentage').keyup(function (e) {
                let percentage = $(this).autoNumeric('get');
                let profitTenPercent = $('#edit-ten-percent').autoNumeric('get');
                let calculate = Math.round((percentage / 100) * profitTenPercent);

                $('#editCommissionForm #edit-nominal').autoNumeric('set', calculate);
            });

            $('#btnEditCommission').click(function (e) {
                $('#editCommissionForm .autonumber').each(function (index, element) {
                    let rawValue = $(this).autoNumeric('get');

                    $(this).val(rawValue);
                });

                $('#editCommissionForm').submit();
            });

        });

    </script>
@endpush
