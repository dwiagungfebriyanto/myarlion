<div class="modal-content">

    <form id="edit-form" action="{{ route('customer.update', $customer) }}" method="post">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

            <h4 class="modal-title" id="myModalLabel">Update Customer - {{ $customer->code }}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="name">Name<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="edit-name" name="name"
                                placeholder="Customer Name" value="{{ $customer->name }}" required
                                autocomplete="off" data-parsley-required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="country">Country</label>
                            <select class="form-control select2 country" name="country" id="edit-country">
                                {!! selectGenerate('Country', $countries, 'id', ['country_code', 'country_name'], $customer->country_id) !!}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="address">Address</label>
                    <input class="form-control" type="text" id="edit-address" name="address"
                        placeholder="Customer Address" value="{{ $customer->address }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="fax">Fax</label>
                            <input class="form-control" type="text" id="edit-fax" name="fax"
                                placeholder="fax number" value="{{ $customer->fax }}" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="telp">Telp/Hp</label>
                            <input class="form-control" type="text" id="edit-telp" name="telp" maxlength="15"
                                placeholder="phone number" value="{{ $customer->telp }}" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="email">Email</label>
                    <input class="form-control" type="text" id="edit-email" name="email"
                        placeholder="email address" value="{{ $customer->email }}" autocomplete="off">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 edit-validate-input">
                            <label for="contact">Contact</label>
                            <input class="form-control" type="text" id="edit-contact" name="contact"
                                placeholder="name contact person" value="{{ $customer->contact }}" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="tax">Tax</label>
                            <select class="form-control select2" required name="tax" id="edit-tax" value="">
                                <option value="" selected>-- Select Tax --</option>
                                <option value="tax" {{ $customer->tax == 'tax' ? 'selected' : '' }}>Tax</option>
                                <option value="nontax" {{ $customer->tax == 'nontax' ? 'selected' : '' }}>Non Tax
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 validate-input">
                    <label for="no_rekening">No Rekening</label>
                    <textarea id="edit-no_rekening" name="no_rekening" class="form-control no_rekening" maxlength="225" rows="3"
                        placeholder="Nama Bank ( Kurs ) : Nomor Rekening ( Atas Nama )">{{ $customer->no_rekening }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 edit-validate-input">
                    <label for="note">Note</label>
                    <textarea id="edit-note" name="note" class="form-control" maxlength="225" rows="3"
                        placeholder="Add note...">{{ $customer->note }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light waves-effect btn-close" data-dismiss="modal">Close</button>

            {{-- <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button> --}}
            <button type="submit" class="btn btn-primary waves-effect waves-light btn-edit-submit">Update</button>
        </div>
    </form>
</div><!-- /.modal-content -->

<script>
    // store data Edit supplier
    $('.btn-edit-submit').on('click', function(e) {
        e.preventDefault();
        let dataInput = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            name: $('#edit-name').val(),
            address: $('#edit-address').val(),
            country: $('#edit-country').val(),
            fax: $('#edit-fax').val(),
            telp: $('#edit-telp').val(),
            email: $('#edit-email').val(),
            contact: $('#edit-contact').val(),
            tax: $('#edit-tax').val(),
            no_rekening: $('#edit-no_rekening').val(),
            note: $('#edit-note').val(),
        };
        $.ajax({
            type: 'PUT',
            url: '{{ route('customer.update', $customer) }}',
            data: dataInput,
            success: function(data) {
                // Handle success response
                $('#editModal').modal('hide');
                $('#edit-form')[0].reset();
                $('#customers-table').DataTable().ajax.reload();
                $.toast({
                    heading: "Success!",
                    text: 'Supplier data successfully updated.',
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

                toastDanger();

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
