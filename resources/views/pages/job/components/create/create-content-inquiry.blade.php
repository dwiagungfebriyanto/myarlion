<form class="parsley-examples" method="post" id="form-inquiry-job">
    @csrf
    <div class="modal-header">
        <h4 class="modal-title">Add Job - From Inquiry</h4>
    </div>
    <div class="modal-body">

        <input type="text" name="source" id="source-inq" value="inquiry" hidden readonly>
        <div class="form-group">
            <div class="col-12 validate-input">
                <label for="">Inquiry<span class="text-danger">*</span></label>
                <br>
                <select class="form-control select2" name="inquiry" id="inquiry" required>
                    <option value="">-- Select Inquiry --</option>
                    @foreach ($inquiries as $item)
                        <option value="{{ $item->id }}">
                            {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') . ' - ' . $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="job_id">Job ID<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="job_id-inq" name="job_id"
                            value="{{ $job_id }}" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Currency<span class="text-danger">*</span></label>
                        <br>
                        <select class="form-control select2" name="currency" id="currency-inq" required>
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
                        <input class="form-control autonumber" type="text" id="amount-inq" name="amount"
                            placeholder="0" value="{{ old('amount') }}" data-a-sign="" data-a-sep="." data-a-dec=","
                            required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-12 validate-input">
                        <label for="">Est. Profit<span class="text-danger">*</span></label>
                        <input class="form-control autonumber" type="text" id="est_profit-inq" name="est_profit"
                            placeholder="0" value="{{ old('est_profit') }}" data-a-sign="" data-a-sep="." data-a-dec=","
                            required>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.job.components.create.job-product-section', ['sectionKey' => 'inq'])

        <div class="col-12 text-right">
            <button type="button" class="btn btn-primary waves-effect waves-light btn-jobInquiry-submit">
                <i class="mdi mdi-send mr-1"></i>Add Job
            </button>
        </div>
    </div>
</form>
