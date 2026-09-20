<!-- sample modal content -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" id="form-edit" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit {{ $pageTitle }}</h4>
                </div>

                <input type="hidden" name="id_record">

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-id">ID<span class="text-danger">*</span></label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="edit-basic-addon1">ASS</span>
                                </div>

                                <input type="text" id="edit-id" class="form-control" aria-label="Username"
                                    aria-describedby="edit-basic-addon1" data-parsley-maxlength="7"
                                    placeholder="" name="id"
                                    value="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-supplier">Supplier<span class="text-danger">*</span></label>
                            <br>
                            <select id="edit-supplier" class="form-control select2" name="supplier">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="edit-note">Notes<span class="text-danger">*</span></label>
                            <textarea id="edit-note" class="form-control" rows="3"
                                name="note"></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://code.jquery.com/jquery-3.6.4.slim.js"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4=" crossorigin="anonymous"></script>

<script>
    $(function () {
        $('#editModal').on('shown.bs.modal', function() {
            $.ajax({
                type: "get",
                url: "{{ route('asset.get_id_suggestion') }}",
                success: function (response) {
                    $('#edit-id').attr('placeholder', response);
                }
            });
        });

        $('#form-edit').submit(function (e) {
            e.preventDefault();

            let idValue = $('#edit-id').val();

            if(isNaN(idValue)) { // jika inputan tidak hanya angka
                $('.help-block').remove()

                $('#edit-id').parent().after(`<span class="help-block">
                                                <mdall>The ID field can only contain numbers.</mdall>
                                            </span>`)
            } else {
                let setValue = (isNaN(idValue))
                    ? idValue
                    : 'ASS' + idValue;

                submitData(setValue);
            }
        });

        function submitData(setValue) {
            let updateUrl = $('#form-edit').attr('action');

            $.ajax({
                type: 'POST',
                url: updateUrl,
                data: {
                    '_token'   : $('[name="_token"]').val(),
                    '_method'  : 'put',
                    'id_record': $('[name="id_record"]').val(),
                    'id'       : setValue,
                    'supplier' : $('#edit-supplier').val(),
                    'note'     : $('#edit-note').val(),
                },
                success: function (data) {
                    // Handle success response
                    $('#editModal').modal('hide');
                    $('#form-edit')[0].reset();
                    $('.help-block').remove()
                    $('#po-asset-datatable').DataTable().ajax.reload();

                    $.toast({
                        heading: "Success!",
                        text: 'PO Asset data successfully updated.',
                        position: "top-right",
                        loaderBg: "#5ba035",
                        icon: "success",
                        hideAfter: 3e3,
                        stack: 1
                    })
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

                            $(`#form-edit`).find(`[name="${index}"]`).parent().after(errorMessage);
                        });
                    }

                    $.toast({
                        heading: "Something went wrong!",
                        text: "Change a few things up and try submitting again.",
                        position: "top-right",
                        loaderBg: "#bf441d",
                        icon: "error",
                        hideAfter: 3e3,
                        stack: 1
                    })
                }
            });
        }
    });
</script>
