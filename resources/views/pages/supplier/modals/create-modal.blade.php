<button type="button" class="btn btn-primary waves-effect waves-light btn-rounded" data-toggle="modal"
    data-target="#createModal" data-url="{{ route('supplier.create') }}">
    <i class="mdi mdi-plus mr-1"></i> Add New
</button>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add Supplier - <span id="code"></span></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="">Main Category<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2" required name="main_category" id="main_category">
                                {!! selectGenerate('Main Category', $mainCategories, 'id', ['code',
                                'main_category_name'], old('main_category')) !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="name">Name<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="supplier_name" name="supplier_name"
                                placeholder="Supplier Name" value="{{ old('supplier_name') }}"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="address">Address</label>
                            <input class="form-control" type="text" id="address" name="address"
                                placeholder="Supplier Address" value="{{ old('address') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="fax">Fax</label>
                                    <input class="form-control" type="text" id="fax" name="fax" placeholder="fax number"
                                        value="{{ old('fax') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="telp">Telp/Hp</label>
                                    <input class="form-control" type="text" id="telp" name="telp" maxlength="15"
                                        placeholder="phone number" value="{{ old('telp') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="email">Email</label>
                            <input class="form-control" type="text" id="email" name="email" placeholder="email address"
                                value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="contact">Contact</label>
                                    <input class="form-control" type="text" id="contact" name="contact"
                                        placeholder="name contact person"
                                        value="{{ old('contact') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="pkp">PKP</label>
                                    <select class="form-control select2" required name="pkp" id="pkp">
                                        <option value="" selected>-- Select PKP --</option>
                                        <option value="pkp">PKP</option>
                                        <option value="nonpkp">Non PKP</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="no_rekening">No Rekening</label>
                            <textarea id="no_rekening" name="no_rekening" class="form-control" maxlength="225" rows="3"
                                placeholder="Nama Bank ( Kurs ) : Nomor Rekening ( Atas Nama )">{{ old('no_rekening') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="note">Note</label>
                            <textarea id="note" name="note" class="form-control" maxlength="225" rows="3"
                                placeholder="Add note...">{{ old('') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light btn-create-submit"
                        id="formSubmit">Add</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#main_category').on('change', function () {
                var main_category_id = $(this).val();

                $.ajax({
                    url: "{{ route('supplier.fetchMainCategory') }}",
                    type: "POST",
                    data: {
                        main_category: main_category_id,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function (response) {
                        $('#code').text(response.supplier_code);
                    }
                })
            });

            $('.btn-create-submit').on('click', function (e) {
                e.preventDefault();
                let dataInput = {
                    _token: '{{ csrf_token() }}',
                    main_category: $('#main_category').val(),
                    supplier_name: $('#supplier_name').val(),
                    address: $('#address').val(),
                    fax: $('#fax').val(),
                    telp: $('#telp').val(),
                    email: $('#email').val(),
                    contact: $('#contact').val(),
                    pkp: $('#pkp').val(),
                    no_rekening: $('#no_rekening').val(),
                    note: $('#note').val(),
                }
                
                $.ajax({
                    type: 'POST',
                    url: '{{ route('supplier.store') }}',
                    data: dataInput,
                    success: function (data) {
                        // Handle success response
                        $('#createModal').modal('hide');
                        $('#form')[0].reset();
                        $('#supplier-table').DataTable().ajax.reload();

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
                                text: 'New Brand data successfully added.',
                                position: "top-right",
                                loaderBg: "#5ba035",
                                icon: "success",
                                hideAfter: 3e3,
                                stack: 1
                            })
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle error response
                        let response = xhr.responseJSON;

                        if ($.isEmptyObject(response) == false) {
                            let errorFields = Object.keys(response.errors);
                            let errors = response.errors;

                            $.each(dataInput, function (key, value) {
                                $('#' + key).parents('.validate-input').children(
                                        '.help-block')
                                    .remove()

                                if (errorFields.includes(key)) {
                                    $('#' + key).parents('.validate-input').append(
                                        '<span class="help-block"><mdall>' +
                                        errors[key] +
                                        '</mdall></span>');
                                }
                            });
                        }
                    }
                });
            });
        });

    </script>
@endpush
