<div class="modal-content">

    <form id="edit-form" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <h4 class="modal-title" id="myModalLabel">Update Main Category - {{ $main_category->code }}</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="main_category_name">Name<span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="edit-main_category_name" name="main_category_name"
                        placeholder="Main Category Name" value="{{ $main_category->main_category_name }}">
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
            main_category_name: $('#edit-main_category_name').val(),
        };
        $.ajax({
            type: 'PUT',
            url: '{{ route('product.main-category.update', $main_category) }}',
            data: dataInput,
            success: function(data) {
                // Handle success response
                $('#editModal').modal('hide');
                $('#edit-form')[0].reset();
                $('#main_categories-table').DataTable().ajax.reload();
                $.toast({
                    heading: "Success!",
                    text: 'Main Category data successfully updated.',
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
                        $('#edit-' + key).parents('.edit-validate-input').children('.help-block')
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
