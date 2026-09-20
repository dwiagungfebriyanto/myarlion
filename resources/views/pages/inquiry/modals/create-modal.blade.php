<div class="modal-content">
    <form action="{{ route('inquiry.store') }}" method="post" class="parsley-examples">
        @csrf
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myModalLabel">Add Inquiry</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Date<span class="text-danger">*</span></label>
                            <div>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="dd/mm/yyyy"
                                        id="datepicker-autoclose" id="date" name="date">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Channel<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 channel" required name="channel" id="channel">
                                {!! selectGenerate('Channel', $channels, 'id', ['id', 'channel_name'], old('channel')) !!}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12">
                    <label for="name">Name<span class="text-danger">*</span></label>
                    <input type="text" name="name" parsley-trigger="change" required
                        placeholder="Enter customer name" class="form-control" id="name">
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Country<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 country" required name="country" id="country">
                                {!! selectGenerate('Country', $countries, 'id', ['country_code', 'country_name'], old('country')) !!}
                            </select>

                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="city">City<span class="text-danger">*</span></label>
                            <input type="text" name="city" parsley-trigger="change" required
                                placeholder="Enter customer city" class="form-control" id="city">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12">
                    <label for="">Product<span class="text-danger">*</span></label>
                    <br>
                    <select class="form-control select2 product" required name="product[]" id="product" multiple >
                        @foreach ($products as $product)
                            <option value="{{ $product->sku }}">
                                {{ $product->sku . ' - ' . $product->product_type->product_type_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="phone">Phone<span class="text-danger">*</span></label>
                            <input type="text" data-parsley-type="number" data-parsley-length="[10,13]"
                                name="phone" parsley-trigger="change" required
                                placeholder="Enter customer phone number" class="form-control" id="phone">
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="email">Email<span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" required
                                parsley-type="email" placeholder="Enter a valid e-mail" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12">
                    <label for="note">Note</label>
                    <textarea id="note" name="note" class="form-control" maxlength="225" rows="3"
                        placeholder="Add note...">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary waves-effect waves-light" id="formSubmit">Add</button>
        </div>
    </form>
</div><!-- /.modal-content -->

<script>
    $(document).ready(function() {
        $("#datepicker-autoclose").datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy',
        });
    });
</script>
<script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>

{{-- for datepicker --}}
<script src="{{ URL::to('/') }}/assets/libs/moment/moment.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/clockpicker/bootstrap-clockpicker.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
{{-- end datepicker --}}

<script src="{{ URL::to('/') }}/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

<!-- Init js-->
<script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>
<script src="{{ URL::to('/') }}/assets/js/pages/form-pickers.init.js"></script>


<!-- Plugin js-->
<script src="{{ URL::to('/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
