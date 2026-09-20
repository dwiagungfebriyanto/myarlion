<div class="modal-content">

    <form id="edit-form" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <h4 class="modal-title" id="myModalLabel">Update Product Type - {{ $product_type->code }}</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <div class="col-12 validate-input">
                    <label for="main_category">Main Category<span class="text-danger">*</span></label>
                    <select class="form-control select2" required name="main_category" id="main_category" disabled>
                        <option value="{{$product_type->main_category_id}}">{{$main_category->code . ' - ' . $main_category->main_category_name}}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="code">Code<span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="edit-code" name="code" maxlength="3"
                        placeholder="000" value="{{$product_type->code}}" required>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="product_type_name">Name<span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="edit-product_type_name" name="product_type_name"
                        placeholder="Brand Name" value="{{ $product_type->product_type_name }}">
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
            // main_category_id: $('#main_category').val(),
            code: $('#edit-code').val(),
            product_type_name: $('#edit-product_type_name').val(),
        };
        $.ajax({
            type: 'PUT',
            url: '{{ route('product.product-type.update', $product_type) }}',
            data: dataInput,
            success: function(data) {
                // Handle success response
                $('#editModal').modal('hide');
                $('#edit-form')[0].reset();
                $('#product_types-table').DataTable().ajax.reload();
                $.toast({
                    heading: "Success!",
                    text: 'Product Type data successfully updated.',
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
