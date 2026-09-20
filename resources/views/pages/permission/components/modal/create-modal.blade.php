<!-- sample modal content -->
<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('permission.store') }}" id="form-add" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="permission">Permission Name<span class="text-danger">*</span></label>
                            <input type="text" name="permission_name" class="form-control" id="permission">
                        </div>
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
                url: '{{ route('permission.store') }}',
                data: $('#form-add').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#createModal').modal('hide');
                    $('#form-add')[0].reset();
                    $('.help-block').remove()
                    $('#permission-datatable').DataTable().ajax.reload();

                    $.toast({
                        heading: "Success!",
                        text: 'New Permission data successfully added.',
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
                        $('#permission').parents('.validate-input').children('.help-block')
                            .remove()

                        $('#permission').parents('.validate-input').append(
                            '<span class="help-block"><mdall>'
                            + response.errors.permission_name
                            + '</mdall></span>')
                    }
                }
            });
        }
    });

</script>
