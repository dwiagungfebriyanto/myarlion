    <div class="modal-content">
        <form id="form" method="post">
            @csrf
            @method('POST')
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Add New Main Category</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="code">Code<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="code" name="code" placeholder=" "
                            value="{{ $next_code }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="main_category_name">Name<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="main_category_name" name="main_category_name"
                            placeholder="Main Category Name" value="{{ old('main_category_name') }}" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                <button type="btn" class="btn btn-primary waves-effect waves-light btn-create-submit">Add</button>
            </div>
        </form>
    </div><!-- /.modal-content -->
    <script>
        $('.btn-create-submit').on('click', function(e) {
            e.preventDefault();
            let inputData = {
                _token: $('input[name=_token]').val(),
                code: $('#code').val(),
                main_category_name: $('#main_category_name').val(),
            }
            $.ajax({
                type: 'POST',
                url: '{{ route('product.main-category.store') }}',
                data: inputData,
                success: function(data) {
                    // Handle success response
                    $('#createModal').modal('hide');
                    $('#form')[0].reset();
                    $('#main_categories-table').DataTable().ajax.reload();
                    // handle error response
                    if (data.success == false) {
                        $.toast({
                            heading: "Error!",
                            text: data.message,
                            position: "top-right",
                            loaderBg: "#ff6849",
                            icon: "error",
                            hideAfter: 3e3,
                            stack: 1
                        })
                        return;
                    } else {
                        $.toast({
                            heading: "Success!",
                            text: 'New Main Category data successfully added.',
                            position: "top-right",
                            loaderBg: "#5ba035",
                            icon: "success",
                            hideAfter: 3e3,
                            stack: 1
                        })
                    }

                },
                error: function(xhr, status, error) {
                    // Handle error response
                    let response = xhr.responseJSON;
                    if ($.isEmptyObject(response) == false) {
                        let errorFields = Object.keys(response.errors);
                        let errors = response.errors;

                        $.each(dataInput, function(key, value) {
                            $('#' + key).parents('.validate-input').children('.help-block')
                                .remove()

                            if (errorFields.includes(key)) {
                                $('#' + key).parents('.validate-input').append(
                                    '<span class="help-block"><mdall>' + errors[key] +
                                    '</mdall></span>');
                            }
                        });

                    }
                }
            });
        });
        // $('#createModal').on('hidden.bs.modal', function() {
        //     $(this).find('.has-error').removeClass('has-error');
        //     $('.help-block').remove();
        //     // $('input').val('');
        //     // $('select').val('');
        // });
    </script>
