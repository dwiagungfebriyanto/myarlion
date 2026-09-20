<div class="modal-content">
    <form id="edit-form" method="post">
        @csrf
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myModalLabel">Update Supplier - {{ $supplier->code }}</h4>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="">Main Category<span class="text-danger">*</span></label>
                    <br>
                    <select class="form-control select2" required name="main_category" id="main_category" disabled>
                        <option value="{{ $supplier->main_category_id }}">
                            {{ "$main_category->code - $main_category->main_category_name" }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="supplier_name">Supplier Name<span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="edit-supplier_name" name="supplier_name"
                        placeholder="Supplier Name" value="{{ $supplier->supplier_name }}" required>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="address">Address</label>
                    <input class="form-control" type="text" id="edit-address" name="address"
                        placeholder="Supplier Address" value="{{ $supplier->address }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="fax">Fax</label>
                            <input class="form-control" type="text" id="edit-fax" name="fax" placeholder="fax number"
                                value="{{ $supplier->fax }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="telp">Telp/Hp</label>
                            <input class="form-control" type="text" id="edit-telp" name="telp" maxlength="15"
                                placeholder="phone number" value="{{ $supplier->telp }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="email">Email</label>
                    <input class="form-control" type="text" id="edit-email" name="email" placeholder="email address"
                        value="{{ $supplier->email }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="contact">Contact</label>
                            <input class="form-control" type="text" id="edit-contact" name="contact"
                                placeholder="name contact person" value="{{ $supplier->contact }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="pkp">PKP</label>
                            <select class="form-control select2" required name="pkp" id="pkp" value="">
                                <option value="" disabled selected>-- Select PKP --</option>
                                <option value="pkp"
                                    {{ $supplier->pkp == 'pkp' ? 'selected' : '' }}>
                                    PKP</option>
                                <option value="nonpkp"
                                    {{ $supplier->pkp == 'nonpkp' ? 'selected' : '' }}>
                                    Non PKP
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 validate-input">
                    <label for="no_rekening">No Rekening</label>
                    <textarea id="no_rekening" name="no_rekening" class="form-control no_rekening" maxlength="225"
                        rows="3"
                        placeholder="Nama Bank ( Kurs ) : Nomor Rekening ( Atas Nama )">{{ $supplier->no_rekening }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="note">Note</label>
                    <textarea id="textarea" name="note" class="form-control" maxlength="225" rows="3"
                        placeholder="Add note...">{{ $supplier->note }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light waves-effect btn-close" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light btn-edit-submit">Update</button>
        </div>
    </form>
</div>
<!-- /.modal-content -->


<script>
    // store data Edit supplier
    $('.btn-edit-submit').on('click', function (e) {
        e.preventDefault();
        let dataInput = $('#edit-form').serialize();

        $.ajax({
            type: 'PUT',
            url: '{{ route('supplier.update', $supplier) }}',
            data: dataInput,
            success: function (data) {
                // Handle success response
                $('#editModal').modal('hide');
                $('#edit-form')[0].reset();
                $('#supplier-table').DataTable().ajax.reload();

                $.toast({
                    heading: "Success!",
                    text: 'Supplier data successfully updated.',
                    position: "top-right",
                    loaderBg: "#5ba035",
                    icon: "success",
                    hideAfter: 3e3,
                    stack: 1
                });
            },
            error: function (xhr, status, error) {
                // Handle error response
                let response = xhr.responseJSON;

                if ($.isEmptyObject(response) == false) {
                    let errorFields = Object.keys(response.errors);
                    let errors = response.errors;

                    $.each(dataInput, function (key, value) {
                        $('#edit-' + key).parents('.edit-validate-input')
                            .children('.help-block')
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
