<button type="button" class="btn btn-primary waves-effect waves-light btn-rounded btn-add" data-toggle="modal"
    data-target="#createModal" data-url="{{ route('customer.create') }}">
    <i class="mdi mdi-plus mr-1"></i> Add New
</button>

<div id="createModal" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Add New Customer</h4>
            </div>

            <div class="modal-body">
                <form id="formAdd" action="{{ route('customer.store') }}" method="post"
                    class="form-parsley" data-modal="#createModal" data-datatable="#customers-table">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="name" name="name"
                                        placeholder="Customer Name" value="{{ old('name') }}"
                                        required>

                                    <span class="text-danger"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="country">Country</label>
                                    <select class="form-control select2 country" name="country" id="country">
                                        {!! selectGenerate('Country', $countries, 'id', ['country_code',
                                        'country_name'], old('country')) !!}
                                    </select>

                                    <span class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="address">Address</label>
                            <input class="form-control" type="text" id="address" name="address"
                                placeholder="Customer Address" value="{{ old('address') }}">

                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="fax">Fax</label>
                                    <input class="form-control" type="string" id="fax" name="fax"
                                        placeholder="fax number" maxlength="15"
                                        value="{{ old('fax') }}">

                                    <span class="text-danger"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="telp">Telp/Hp</label>
                                    <input class="form-control" type="text" id="telp" name="telp" maxlength="15"
                                        placeholder="phone number" value="{{ old('telp') }}">

                                    <span class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="email">Email</label>
                            <input class="form-control" type="email" id="email" name="email" placeholder="email address"
                                value="{{ old('email') }}">

                            <span class="text-danger"></span>
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

                                    <span class="text-danger"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="tax">Tax</label>
                                    <select class="form-control select2" name="tax" id="tax">
                                        <option value="" selected>-- Select Tax --</option>
                                        <option value="tax">Tax</option>
                                        <option value="nontax">Non Tax</option>
                                    </select>

                                    <span class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="no_rekening">No Rekening</label>
                            <textarea id="no_rekening" name="no_rekening" class="form-control" maxlength="225" rows="3"
                                placeholder="Nama Bank ( Kurs ) : Nomor Rekening ( Atas Nama )">{{ old('no_rekening') }}</textarea>

                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="note">Note</label>
                            <textarea id="note" name="note" class="form-control" maxlength="225" rows="3"
                                placeholder="Add note...">{{ old('note') }}</textarea>

                            <span class="text-danger"></span>
                        </div>
                    </div>
                </form>
            </div>
            {{-- /.modal-body --}}

            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light" form="formAdd">Add</button>
            </div>
        </div>
        {{-- /.modal-content --}}
    </div>
    {{-- /.modal-dialog --}}
</div>
{{-- /.modal --}}


@push('scripts')
    <script>
        $(function () {
            $('#formAdd').submit(function (e) {
                e.preventDefault();
                submitModalRequest(this);
            });
        });

    </script>
@endpush
