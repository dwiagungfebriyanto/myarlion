<div class="modal-content">

    <form id="edit-form" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <h4 class="modal-title" id="myModalLabel">Update Packaging - {{ $packaging->code }}</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="main_category">Main Category<span class="text-danger">*</span></label>
                    <select class="form-control select2" required name="main_category" id="edit-main_category" disabled>
                        <option value="{{$packaging->main_category_id}}">{{$main_category->code . ' - ' . $main_category->main_category_name}}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="packaging_name">Name<span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="edit-packaging_name" name="packaging_name"
                        placeholder="Packaging Name" value="{{ $packaging->packaging_name }}">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light waves-effect btn-close" data-dismiss="modal">Close</button>

            {{-- <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button> --}}
            <button type="submit" class="btn btn-primary waves-effect waves-light btn-edit-submit"
                id="btn-edit-submit">Update</button>
        </div>
    </form>
</div><!-- /.modal-content -->

<script>
    // store data Edit Main Category
    $('#btn-edit-submit').on('click', function(e) {
        e.preventDefault();
        let dataInput = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            main_category_id: $('#edit-main_category').val(),
            // code: $('#code').val(),
            packaging_name: $('#edit-packaging_name').val(),
        };
        $.ajax({
            type: 'PUT',
            url: '{{ route('product.packaging.update', $packaging) }}',
            data: dataInput,
            success: function(data) {
                // Handle success response
                $('#editModal').modal('hide');
                $('#edit-form')[0].reset();
                $('#packagings-table').DataTable().ajax.reload();
                $.toast({
                    heading: "Success!",
                    text: 'Packaging data successfully updated.',
                    position: "top-right",
                    loaderBg: "#5ba035",
                    icon: "success",
                    hideAfter: 3e3,
                    stack: 1
                })
            },
            error: function(xhr, status, error) {
                // Handle error response
                let response = xhr.responseJSON;
                if ($.isEmptyObject(response) == false) {

                    let errorFields = Object.keys(response.errors);
                    let errors = response.errors;

                    $.each(dataInput, function(key, value) {
                        $('#edit-' + key).parents('.edit-validate-input').children(
                                '.help-block')
                            .remove()

                        if (errorFields.includes(key)) {
                            $('#edit-' + key).parents('.edit-validate-input').append(
                                '<span class="help-block"><mdall>' + errors[key] +
                                '</mdall></span>');
                        }
                    });
                }
            }
        });
    });
</script>

<script src="{{ URL::to('/') }}/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

<!-- Init js-->
<script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>

<!-- Plugin js-->
<script src="{{ URL::to('/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
