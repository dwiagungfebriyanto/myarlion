<div class="modal-content">

    <form id="edit-form" method="post">
        @csrf
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <h4 class="modal-title" id="myModalLabel">Update Vendor - {{ $vendor->code }}</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="vendor_name">Vendor Name<span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="edit-vendor_name" name="vendor_name"
                        placeholder="Vendor Name" value="{{ $vendor->vendor_name }}" required>

                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="address">Address</label>
                    <input class="form-control" type="text" id="edit-address" name="address"
                        placeholder="Vendor Address" value="{{ $vendor->address }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="fax">Fax</label>
                            <input class="form-control" type="text" id="edit-fax" name="fax"
                                placeholder="fax number" value="{{ $vendor->fax }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="telp">Telp/Hp</label>
                            <input class="form-control" type="text" id="edit-telp" name="telp" maxlength="15"
                                placeholder="phone number" value="{{ $vendor->telp }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="email">Email</label>
                    <input class="form-control" type="text" id="edit-email" name="email"
                        placeholder="email address" value="{{ $vendor->email }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="contact">Contact</label>
                            <input class="form-control" type="text" id="edit-contact" name="contact"
                                placeholder="name contact person" value="{{ $vendor->contact }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="pkp">PKP</label>
                            <select class="form-control" required name="pkp" id="pkp" value="">
                                <option value="" selected>-- Select PKP --</option>
                                <option value="pkp" {{ $vendor->pkp == 'pkp' ? 'selected' : '' }}>PKP</option>
                                <option value="nonpkp" {{ $vendor->pkp == 'nonpkp' ? 'selected' : '' }}>Non PKP
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 validate-input">
                    <label for="no_rekening">No Rekening</label>
                    <textarea id="no_rekening" name="no_rekening" class="form-control no_rekening" maxlength="225" rows="3"
                        placeholder="Nama Bank ( Kurs ) : Nomor Rekening ( Atas Nama )">{{ $vendor->no_rekening }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="note">Note</label>
                    <textarea id="textarea" name="note" class="form-control" maxlength="225" rows="3" placeholder="Add note...">{{ $vendor->note }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light waves-effect btn-close" data-dismiss="modal">Close</button>

            <button type="button" class="btn btn-primary waves-effect waves-light btn-edit-submit">Update</button>
        </div>
    </form>
</div><!-- /.modal-content -->
<script>
    // store data Edit vendor
    $('.btn-edit-submit').on('click', function(e) {
        e.preventDefault();
        let dataInput = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            vendor_name: $('#edit-vendor_name').val(),
            address: $('#edit-address').val(),
            fax: $('#edit-fax').val(),
            telp: $('#edit-telp').val(),
            email: $('#edit-email').val(),
            contact: $('#edit-contact').val(),
            pkp: $('#pkp').val(),
            no_rekening: $('#no_rekening').val(),
            note: $('#textarea').val(),
        }
        $.ajax({
            type: 'PUT',
            url: '{{ route('vendors.update', $vendor) }}',
            data: $('#edit-form').serialize(),
            success: function(data) {
                // Handle success response
                $('#editModal').modal('hide');
                $('#edit-form')[0].reset();
                $('#vendors-table').DataTable().ajax.reload();

                $.toast({
                    heading: "Success!",
                    text: 'Vendor data successfully updated.',
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
                // console.log(response);

                if ($.isEmptyObject(response) == false) {
                    // console.log("empty" + response);

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

<script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

<!-- Init js-->
<script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>
