<form class="parsley-examples" method="post" id="form-non-inquiry-job">
    @csrf
    <div class="modal-header">
        <h4 class="modal-title">Add Job - From Non Inquiry</h4>
    </div>
    <div class="modal-body">

        <input type="text" name="source" id="source-nonInq" value="non_inquiry" hidden readonly>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="job_id">Job ID</label>
                        <input class="form-control" type="text" id="job_id-nonInq" name="job_id"
                            value="{{ $job_id }}" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Customer<span class="text-danger">*</span></label>
                        <br>
                        <select class="form-control select2" required name="customer" id="customer-nonInq">
                            {!! selectGenerate('Customer', $customer, 'id', ['code', 'name'], old('customer')) !!}
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-12 validate-input">
                <label for="">Channel<span class="text-danger">*</span></label>
                <br>
                <select class="form-control select2" required name="channel" id="channel-nonInq">
                    {!! selectGenerate('Channel', $channels, 'id', 'channel_name', old('channel')) !!}
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Country<span class="text-danger">*</span></label>
                        <br>
                        <select class="form-control select2" required name="country" id="country-nonInq">
                            {!! selectGenerate('Country', $countries, 'id', ['country_code', 'country_name'], '104') !!}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Currency<span class="text-danger">*</span></label>
                        <br>
                        <select class="form-control select2" required name="currency" id="currency-nonInq">
                            {!! selectGenerate('Currency', $currencies, 'id', ['currency_code', 'currency_name'], '3') !!}
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Amount<span class="text-danger">*</span></label>
                        <input class="form-control autonumber" type="text" id="amount-nonInq" name="amount"
                            placeholder="0" value="{{ old('amount') }}" data-a-sign="" data-a-sep="." data-a-dec=","
                            required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Est. Profit<span class="text-danger">*</span></label>
                        <input class="form-control autonumber" type="text" id="est_profit-nonInq" name="est_profit"
                            placeholder="0" value="{{ old('est_profit') }}" data-a-sign="" data-a-sep="." data-a-dec=","
                            required>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.job.components.create.job-product-section', ['sectionKey' => 'nonInq'])

        <div class="col-12 text-right">
            <button type="button" class="btn btn-primary waves-effect waves-light btn-jobNonInquiry-submit">
                <i class="mdi mdi-send mr-1"></i>Add Job
            </button>
        </div>
    </div>
</form>
