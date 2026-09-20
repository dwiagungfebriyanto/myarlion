<!-- sample modal content -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="form-edit" method="post">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-year">Year<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="year" id="edit-year" placeholder="Year"
                                min="2010" step="1" value="{{ date('Y') }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-marketing">Marketing<span class="text-danger">*</span></label>
                            <select name="marketing" id="edit-marketing" class="form-control select2" disabled>
                                {!! selectGenerate('Marketing', $marketings, 'id', 'name') !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-target">Target</label>
                            <input type="text" class="form-control autonumeric" name="target" id="edit-target"
                                placeholder="target">
                        </div>
                    </div>
                </div>
                {{-- /.modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                </div>
            </form>
        </div>
        {{-- /.modal-content --}}
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script src="https://code.jquery.com/jquery-3.6.4.slim.js"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4=" crossorigin="anonymous"></script>

<script>
    $(function () {
        $('#form-edit').submit(function (e) {
            e.preventDefault();

            let target = $('#edit-target').autoNumeric('get');
            $('#edit-target').val(target);

            updateData();
        });

        function updateData() {
            $.ajax({
                type: 'POST',
                url: $('#form-edit').attr('action'),
                data: $('#form-edit').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#editModal').modal('hide');
                    $('#form-edit')[0].reset();
                    $('.help-block').remove()
                    $('#salestarget-table').DataTable().ajax.reload();

                    toastSuccess('Sales target data successfully updated.')
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    let response = xhr.responseJSON;

                    if (!$.isEmptyObject(response)) {
                        $('.help-block').remove();

                        $.each(response.errors, function (indexInArray, valueOfElement) {
                            $(`#${indexInArray}`).parents('.validate-input').append(
                                `<span class="help-block"><mdall>${valueOfElement[0]}</mdall></span>`
                            )
                        });
                    }
                }
            });
        }
    });

</script>
