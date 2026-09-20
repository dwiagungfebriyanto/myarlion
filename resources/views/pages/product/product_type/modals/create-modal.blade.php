    <div class="modal-content">
        <form id="form" method="post">
            @csrf
            @method('POST')
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Add New Product Type</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="main_category_id">Main Category<span class="text-danger">*</span></label>
                        <select class="form-control select2" required name="main_category_id" id="main_category_id">
                            {!! selectGenerate(
                                'Main Category',
                                $main_category_id,
                                'id',
                                ['code', 'main_category_name'],
                                old('main_category_id'),
                            ) !!}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="code">Code<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="code" name="code" maxlength="3" placeholder="000"
                            value="" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="brand_name">Name<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="product_type_name" name="product_type_name"
                            placeholder="Prouduct Type Name" value="{{ old('product_type_name') }}" required>
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
        $(document).ready(function() {
            $('#main_category_id').on('change', function() {
                var main_category_id = $(this).val();
                $.ajax({
                    url: "{{ route('product_type.fetchMainCategory') }}",
                    type: "POST",
                    data: {
                        main_category_id: main_category_id,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(response) {
                        $('#code').val(response.product_type_code);
                    }
                })
            })
        })
    </script>
    <script>
        $('.btn-create-submit').on('click', function(e) {
            e.preventDefault();
            dataInput = {
                _token: $('input[name=_token]').val(),
                main_category_id: $('#main_category_id').val(),
                code: $('#code').val(),
                product_type_name: $('#product_type_name').val(),
            }
            $.ajax({
                type: 'POST',
                url: '{{ route('product.product-type.store') }}',
                data: dataInput,
                success: function(data) {
                    // Handle success response
                    $('#createModal').modal('hide');
                    $('#form')[0].reset();
                    $('#product_types-table').DataTable().ajax.reload();
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
                            text: 'New Product Type data successfully added.',
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
    </script>

    <script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

    <!-- Init js-->
    <script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>
