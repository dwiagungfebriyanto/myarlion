<form id="create-form" action="{{ route('inquiry.store') }}" method="post" class="parsley-examples">
    @csrf
    <div class="form-group">
        <div class="col-12">
            <label for="">Customer Category<span class="text-danger">*</span></label>
            <br>
            <select class="form-control select2 customer_category" required name="customer_category" id="customer_category">
                <option value="">-- Select Customer Category --</option>
                <option value="new_customer">New Customer</option>
                <option value="exis_customer">Existing Customer</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="">Date<span class="text-danger">*</span></label>
                    <div>
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="dd/mm/yyyy"
                                id="datepicker-autoclose" id="date" name="date" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group text_customer">
                <div class="col-12">
                    <label for="name">Name<span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" parsley-trigger="change" required
                        placeholder="Enter customer name" class="form-control name" autocomplete="off">
                </div>
            </div>
            <div class="form-group select_customer" style="display: none">
                <div class="col-12">
                    <label >Name<span class="text-danger">*</span></label>
                    <br>
                    <select class="form-control select2 customer_name" name="customer_name" id="customer_name">
                        {!! selectGenerate('Customer', $customers, 'id', ['code', 'name'], old('customer_name')) !!}
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="">Website</label>
                    <br>
                    <select class="form-control select2 website" name="website" id="website">
                        {!! selectGenerate('Website', $websites, 'id', ['id', 'web_domain'], old('website')) !!}
                    </select>
                </div>
            </div>
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
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" parsley-trigger="change" placeholder="Enter customer city"
                        class="form-control" autocomplete="off">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" maxlength="15" name="phone" parsley-trigger="change"
                        placeholder="Enter customer phone number" class="form-control" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" parsley-type="email"
                        placeholder="Enter a valid e-mail" autocomplete="off" />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="phone">Destination</label>
                    <select class="form-control select2 destination" name="destination" id="destination">
                        {!! selectGenerate('Destination', $countries, 'id', ['country_code', 'country_name'], old('country')) !!}
                    </select>
                </div>
            </div>
        </div>
        <div class="col-6 ">
            <div class="form-group platform-class" style="display: none">
                <div class="col-12">
                    <label for="email">Platform</label>
                    <select class="form-control select2" name="platform" id="platform">
                        <option value="">-- Select Platform --</option>
                        <option value="indonesiacocopeat">indonesiacocopeat.com</option>
                        <option value="unknown">Unknown</option>
                    </select>
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

    <div class="form-group text-right mb-0">
        <a href="{{ route('inquiry.index') }}" class="btn btn-light waves-effect waves-light mr-1">
            <i class=" mdi mdi-arrow-collapse-left"></i><span class="d-none d-sm-inline-block ml-2">Back</span>
        </a>
        <button class="btn btn-primary waves-effect waves-light mr-1 btn-submit-inq" type="button">
            <i class="mdi mdi-arrow-collapse-right mr-2"></i>Next
        </button>
    </div>
</form>
