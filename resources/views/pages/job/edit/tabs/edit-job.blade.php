<form id="edit-form" action="{{ route('job.list.update', $job->id) }}" method="post"
    class="parsley-examples">
    @csrf
    @method('PUT')

    @php
        $disabledElement = !$job->statusIsOpen() ? 'disabled' : '';
        $hasWaitingEditAmountRequest = $job->waitingEditAmountRequest()->exists();
        $canRequestEditAmount = !$hasWaitingEditAmountRequest
            // && (($job->statusIsOpen() && $job->employee_id === auth()->id())
            && auth()->user()->can('request edit amount all job');
    @endphp

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="job_id">Job ID</label>
                    <input class="form-control" type="text" value="{{ $job->code }}" disabled>
                    <span class="text-muted">Created at:
                        {{ date('d-m-Y H:i', strtotime($job->created_at)) }}</span>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <div class="col-12 validate-input">
                    <label for="">Marketing</label>
                    <input class="form-control" type="text" value="{{ $marketing->name }}" disabled>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="">Customer<span class="text-danger">*</span></label>
                    <br>
                    <select class="form-control select2" required name="customer" id="customer" {{ $disabledElement }}>
                        {!! selectGenerate('Customer', $customer, 'id', ['code', 'name'], $job->customer_id) !!}
                    </select>
                    @if($errors->get('customer'))
                        <span class="help-block">
                            {{ $errors->first('customer') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="">Country<span class="text-danger">*</span></label>
                    <br>
                    <select class="form-control select2" required name="country" id="country" {{ $disabledElement }}>
                        {!! selectGenerate('Country', $countries, 'id', ['country_code', 'country_name'],
                        $job->country_id) !!}
                    </select>
                    @if($errors->get('country'))
                        <span class="help-block">
                            {{ $errors->first('country') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($job->source != 'inquiry')
        <div class="form-group">
            <div class="col-12">
                <label for="">Channel<span class="text-danger">*</span></label>
                <br>
                <select class="form-control select2" required name="channel" id="channel-reg" {{ $disabledElement }}>
                    {!! selectGenerate('Channel', $channels, 'id', 'channel_name', $job->channel_id) !!}
                </select>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="">Currency<span class="text-danger">*</span></label>
                    <br>
                    <select class="form-control select2" required name="currency" id="currency" {{ $disabledElement }}>
                        {!! selectGenerate('Currency', $currencies, 'id', ['currency_code', 'currency_name'],
                        $job->currency_id) !!}
                    </select>
                    @if($errors->get('currency'))
                        <span class="help-block">
                            {{ $errors->first('currency') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @can('edit period job')
            <div class="col-6">
                <div class="form-group">
                    <div class="col-12">
                        <label for="">Period</label>
                        <br>
                        <div class="input-group">
                            <input type="text" class="form-control period" placeholder="mm/yyyy"
                                id="datepicker-autoclose" name="period" {{ $disabledElement }}
                                value="{{ isset($job->period_job) ? date('F Y', strtotime($job->period_job)) : '' }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        <input type="text" name="init_period_job" value="{{ $job->period_job }}" hidden>
    </div>
    <div class="form-group">
        <div class="col-12">
            <label for="">Amount<span class="text-danger">*</span></label>
            <input class="form-control autonumber" type="text" id="amount" name="amount" placeholder="0"
                {{ $disabledElement }} readonly value="{{ $job->amount }}" data-a-sign="" data-a-sep="." data-a-dec=",">

            @if($hasWaitingEditAmountRequest)
                <small class="text-muted">Request edit PI Amount has been sent.</small>
            @endif

            @if($canRequestEditAmount)
                <button type="button" class="btn btn-sm btn-secondary mt-1" data-toggle="modal"
                    data-target="#requestEditAmountModal">
                    Request Edit Amount
                </button>
            @endif

            @if($errors->get('amount'))
                <span class="help-block">
                    {{ $errors->first('amount') }}
                </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <div class="col-12">
            <label for="">Est. Profit<span class="text-danger">*</span></label>
            <input class="form-control autonumber" type="text" id="est_profit" name="est_profit" placeholder="0"
                {{ $disabledElement }} value="{{ $job->est_profit }}" required data-a-sign="" data-a-sep="."
                data-a-dec=",">
            @if($errors->get('est_profit'))
                <span class="help-block">
                    {{ $errors->first('est_profit') }}
                </span>
            @endif
        </div>
    </div>

    <div class="form-group text-right mb-0">
        @if($job->statusIsOpen())
            <button class="btn btn-primary waves-effect waves-light mr-1 btn-edit-submit" type="button">
                Update
            </button>
        @endif
    </div>
</form>


@include('pages.job.edit.components.modal-request-edit-amount')


@push('scripts')
    <script src="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}">
    </script>
    <script src="{{ asset('assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('assets/js/helper.js') }}"></script>

    <script>
        $(function () {
            $("#datepicker-autoclose").datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'MM yyyy',
                viewMode: "months",
                minViewMode: "months"
            });

            function formatDate(value) {
                return moment("01 " + value, "DD MMMM YYYY").format("YYYY-MM-DD");
            }

            $('.btn-edit-submit').click(function () {
                unformatNumeric($('#amount'))
                unformatNumeric($('#est_profit'))

                let date = $('#datepicker-autoclose').val();

                if (date != '') {
                    $('#datepicker-autoclose').val(formatDate(date));
                }

                $('#edit-form').submit();
            });
        });

    </script>
@endpush
