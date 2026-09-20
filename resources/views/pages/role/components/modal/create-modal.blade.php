<!-- sample modal content -->
<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('role.store') }}" id="form-add" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="role">Role Name<span class="text-danger">*</span></label>
                            <input type="text" name="role_name" class="form-control" id="role">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row ml-2">
                            <div class="col-12">
                                <h4 class="header-title" style="text-transform: capitalize">Role Permissions</h4>
                                <div class="row validate-input">
                                    @foreach($permissions as $index => $permission)
                                        <div class="custom-control custom-checkbox col-md-6 mt-1">
                                            <input type="checkbox" class="custom-control-input" name="permissions[]"
                                                id="permissions.{{ $index }}" value="{{ $permission->id }}">

                                            <label class="custom-control-label"
                                                for="permissions.{{ $index }}">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="submit-add">Add</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://code.jquery.com/jquery-3.6.4.slim.js"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4=" crossorigin="anonymous"></script>

<script>
    $(function () {
        $('#form-add').submit(function (e) {
            e.preventDefault();
            submitData();
        });

        $('#submit-add').click(function (e) {
            e.preventDefault();
            submitData();
        });

        function submitData() {
            $.ajax({
                type: 'POST',
                url: '{{ route('role.store') }}',
                data: $('#form-add').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#createModal').modal('hide');
                    $('#form-add')[0].reset();
                    $('.help-block').remove()
                    $('#role-datatable').DataTable().ajax.reload();

                    $.toast({
                        heading: "Success!",
                        text: 'New Role data successfully added.',
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
                        $('#role').parents('.validate-input').children('.help-block')
                            .remove()

                        $('#role').parents('.validate-input').append(
                            '<span class="help-block"><mdall>'
                            + response.errors.role_name
                            + '</mdall></span>')
                    }
                }
            });
        }
    });

</script>
