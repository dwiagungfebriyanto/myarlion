<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Edit {{ $pageTitle }}</h4>
            </div>

            <div class="modal-body">
                <form id="formEdit" action="" method="post" data-modal="#editModal"
                    data-datatable="#salestargetmonthly-table">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="year">Year<span class="text-danger">*</span></label>
                            <input type="month" id="month" class="form-control" placeholder="Month" name="month"
                                value="" readonly>

                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-marketing">Marketing<span class="text-danger">*</span></label>
                            <select id="edit-marketing" class="form-control select2" name="marketing" disabled>
                                {!! selectGenerate('Marketing', $marketings, 'id', 'name') !!}
                            </select>

                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="target">Sales Target<span class="text-danger">*</span></label>
                            <input type="text" id="target" class="form-control autonumeric" placeholder="Sales target"
                                name="target" required>

                            <span class="text-danger"></span>
                        </div>
                    </div>
                </form>
            </div>
            {{-- /.modal-body --}}

            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light" form="formEdit">Submit</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


@push('scripts')
    <script>
        const setEditModalData = (element) => {
            const formEdit = $('#formEdit');
            const data = $(element).data();

            formEdit.attr('action', data.url);
            formEdit.find('#month').val(data.month);
            formEdit.find('#edit-marketing').val(data.marketing).trigger('change');
            formEdit.find('#target').autoNumeric('set', data.salesTarget);
        }

        $(function () {
            $('#formEdit').submit(function (e) {
                e.preventDefault();
                submitModalRequest(this);
            });
        });

    </script>
@endpush
