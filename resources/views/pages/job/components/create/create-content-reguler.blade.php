<form class="parsley-examples" method="post" id="form-reguler-job">
    @csrf
    <div class="modal-header">
        <h4 class="modal-title">Add Job - From Reguler</h4>
    </div>
    <div class="modal-body">
        <input type="text" name="source" id="source-reg" value="reguler" hidden readonly>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="job_id">Job ID</label>
                        <input class="form-control" type="text" id="job_id-reg" name="job_id"
                            value="{{ $job_id }}" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Customer<span class="text-danger">*</span></label>
                        <br>
                        <select class="form-control select2" required name="customer" id="customer-reg">
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
                <select class="form-control select2" required name="channel" id="channel-reg">
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
                        <select class="form-control select2" required name="country" id="country-reg">
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
                        <select class="form-control select2" required name="currency" id="currency-reg">
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
                        <input class="form-control autonumber" type="text" id="amount-reg" name="amount"
                            placeholder="0" value="{{ old('amount') }}" data-a-sign="" data-a-sep="." data-a-dec=","
                            required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Est. Profit<span class="text-danger">*</span></label>
                        <input class="form-control autonumber" type="text" id="est_profit-reg" name="est_profit"
                            placeholder="0" value="{{ old('est_profit') }}" data-a-sign="" data-a-sep="." data-a-dec=","
                            required>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.job.components.create.job-product-section', ['sectionKey' => 'reg'])

        <div class="col-12 text-right">
            <button type="button" class="btn btn-primary waves-effect waves-light btn-jobReguler-submit">
                <i class="mdi mdi-send mr-1"></i>Add Job
            </button>
        </div>
    </div>
</form>
