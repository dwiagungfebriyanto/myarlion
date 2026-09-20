<button type="button" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#createModal">+ Add New</button>

<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add Vendor</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="name">Name<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="vendor_name" name="vendor_name"
                                placeholder="Vendor Name" value="{{ old('vendor_name') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="address">Address</label>
                            <input class="form-control" type="text" id="address" name="address"
                                placeholder="Vendor Address" value="{{ old('address') }}">
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
                                    <select class="form-control" required name="pkp" id="pkp">
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
                                placeholder="Add note...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light btn-create-submit"
                        id="formSubmit">Add</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@push('scripts')
    <script>
        $(document).ready(function () {
            $('.btn-create-submit').off('click').on('click', function (e) {
                e.preventDefault();
                let dataInput = $('#form').serialize();

                $.ajax({
                    type: 'POST',
                    url: '{{ route('vendors.store') }}',
                    data: dataInput,
                    success: function (data) {
                        // Handle success response
                        $('#createModal').modal('hide');
                        $('#form')[0].reset();
                        $('#vendors-table').DataTable().ajax.reload();

                        toastSuccess('New vendor data successfully added.');
                    },
                    error: function (xhr, status, error) {
                        // Handle error response
                        let response = xhr.responseJSON;

                        if ($.isEmptyObject(response) == false) {
                            let errorFields = Object.keys(response.errors);
                            let errors = response.errors;

                            $.each(errorFields, function (key, value) {
                                $('#' + value).parents('.validate-input').children(
                                        '.help-block')
                                    .remove()

                                if (errorFields.includes(value)) {
                                    $('#' + value).parents('.validate-input')
                                        .append(
                                            `<span class="help-block"><mdall>${errors[value]}</mdall></span>`
                                        );
                                }
                            });
                        }
                    }
                });
            });
        });

    </script>
@endpush
