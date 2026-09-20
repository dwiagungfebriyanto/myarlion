<div id="modalChangeStatus" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Change Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formChangeStatus" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control select2">
                            {!! selectGenerate('Status', config('constant.edit_amount_approval_status'), 'value', 'key')
                            !!}
                        </select>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light"
                    form="formChangeStatus">Save</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        function setChangeStatusModal(el) {
            const data = $(el).data();

            $('#formChangeStatus').attr('action', data.url);

            $('#formChangeStatus #status').find(`option[value="${data.status}"]`)
                .prop('selected', true)
                .trigger('change');
        }

    </script>
@endpush
