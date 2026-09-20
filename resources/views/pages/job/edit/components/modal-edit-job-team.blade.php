<div id="editTeamModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="team-form-edit" action="" method="post" class="parsley-form">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit Job Team</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="employee">Employee<span class="text-danger">*</span></label>
                            <input id="employee" type="text" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="percentage">Job Percentage<span class="text-danger">*</span></label>
                            <input type="text" data-a-sign="%" data-p-sign="s" class="form-control autonumber"
                                id="percentage" name="percentage" required>

                            @error('percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
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
        $('#team-form-edit button[type="submit"]').click(function (e) {
            e.preventDefault();

            let rawPercentage = $('#team-form-edit #percentage').autoNumeric('get');
            $('#team-form-edit #percentage').val(rawPercentage)

            $('#team-form-edit').submit()
        });
    });

</script>
@endpush
