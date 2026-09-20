<!-- sample modal content -->
<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" id="form-create" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="warehouse-name">Warehouse Name<span class="text-danger">*</span></label>
                            <input id="warehouse-name" class="form-control" type="text" name="warehouse_name">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="status">Status<span class="text-danger">*</span></label>
                            <br>
                            <input type="checkbox" id="status" name="status" data-plugin="switchery"
                                data-color="#1bb99a" data-size="small" checked />
                            <span id="status-description">Active</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="address">Address</label>
                            <textarea id="address" class="form-control" rows="3" name="address"></textarea>
                        </div>
                    </div>

                </div>
                {{-- end modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light btn-submit">Add</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://code.jquery.com/jquery-3.6.4.slim.js"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4=" crossorigin="anonymous"></script>

<script>
    $(function () {
        $('#status').change(function (e) {
            let status = $(this).prop('checked') ? 'Active' : 'Inactive';

            $('#status-description').text(status);
        });

        $('#form-create').submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '{{ route('warehouse.store') }}',
                data: $('#form-create').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#createModal').modal('hide');
                    $('#form-create')[0].reset();
                    $('.help-block').remove()
                    $('#warehouse-datatable').DataTable().ajax.reload();

                    $.toast({
                        heading: "Success!",
                        text: 'New Warehouse data successfully added.',
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
                            let errorMessage = `<span class="help-block">
                                                    <mdall>${value}</mdall>
                                                </span>`;

                            $(`#form-create`).find(`[name="${index}"]`).parent()
                                .append(errorMessage);
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

        });

    });

</script>
