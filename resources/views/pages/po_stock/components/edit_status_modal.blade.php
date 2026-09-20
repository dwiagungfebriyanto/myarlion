<div id="changeStatusModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="form-status" method="post">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Change Status</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="change-status">Status<span class="text-danger">*</span></label>
                            <br>
                            <select id="change-status" class="form-control select2" name="status" required>
                                {!! selectGenerate(null, $statuses, 'value', 'name') !!}
                            </select>
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
{{-- #changeStatusModal --}}


@push('scripts')
    <script>
        $(function () {
            $('#form-status').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    type: "post",
                    url: $(this).attr('action'),
                    data: $(this).serializeArray(),
                    success: function (response) {
                        $('#changeStatusModal').modal('hide');
                        $('#po-datatable').DataTable().ajax.reload();

                        toastSuccess('Data status has been updated.');
                    },
                    error: function (xhr, status, error) {
                        // Handle error response
                        $('.help-block').remove()

                        let response = xhr.responseJSON;

                        if (!$.isEmptyObject(response)) {

                            $.each(response.errors, function (index, value) {
                                let errorMessage = `<div class="col-12">
                                                    <span class="help-block">
                                                        <mdall>${value}</mdall>
                                                    </span>
                                                </div>`;

                                $(`#form-status`).find(`[name="${index}"]`).parent()
                                    .after(errorMessage);
                            });
                        }

                        toastDanger();
                    }
                });
            });
        });

    </script>
@endpush
