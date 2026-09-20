<!-- sample modal content -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="form-edit" data-url="{{ route('permission.index') }}" method="post">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-permission">Permission Name<span class="text-danger">*</span></label>
                            <input type="text" name="permission_name" class="form-control" id="edit-permission">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        id="submit-edit">Update</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://code.jquery.com/jquery-3.6.4.slim.js"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4=" crossorigin="anonymous"></script>

<script>
    $(function () {
        $('#form-edit').submit(function (e) {
            e.preventDefault();
            submitData();
        });

        $('#submit-edit').click(function (e) {
            e.preventDefault();
            submitData();
        });

        function submitData() {
            $.ajax({
                type: 'POST',
                url: $('#form-edit').attr('action'),
                data: $('#form-edit').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#editModal').modal('hide');
                    $('#form-edit')[0].reset();
                    $('.help-block').remove()
                    $('#permission-datatable').DataTable().ajax.reload();

                    $.toast({
                        heading: "Success!",
                        text: 'Permission data successfully updated.',
                        position: "top-right",
                        loaderBg: "#5ba035",
                        icon: "success",
                        hideAfter: 3e3,
                        stack: 1
                    })
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    let response = xhr.responseJSON;

                    if (!$.isEmptyObject(response)) {
                        $('#edit-permission').parents('.validate-input').children('.help-block')
                            .remove()

                        $('#edit-permission').parents('.validate-input').append(
                            '<span class="help-block"><mdall>' +
                            response.errors.permission_name +
                            '</mdall></span>')
                    }
                }
            });
        }
    });

</script>
