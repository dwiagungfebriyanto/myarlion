<!-- sample modal content -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" data-url="{{ route('role.index') }}" id="form-edit" method="post">
                @csrf
                @method('put')
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Edit {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="edit-role">Role Name<span class="text-danger">*</span></label>
                            <input type="text" name="role_name" class="form-control" id="edit-role">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row ml-2">
                            <div class="col-12">
                                <h4 class="header-title" style="text-transform: capitalize">Role Permissions</h4>
                                <div class="row validate-input" id="edit-permission">
                                </div>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="submit-edit">Update</button>
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
            updateData();
        });

        $('#submit-edit').click(function (e) {
            e.preventDefault();
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
                    $('#role-datatable').DataTable().ajax.reload();

                    $.toast({
                        heading: "Success!",
                        text: 'Role data successfully updated.',
                        position: "top-right",
                        loaderBg: "#5ba035",
                        icon: "success",
                        hideAfter: 3e3,
                        stack: 1
                    })

                    setTimeout(() => {
                        location.reload()
                    }, 2000);
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    // let response = xhr.responseJSON;

                    $.toast({
                        heading: "Something went wrong!",
                        text: "Failed to update data.",
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
