<form id="edit-form" method="post" class="parsley-examples">
    @csrf
    @method('PUT')
    @php
        $isCustomerLocked = !is_null($inquiry->customer_id);
    @endphp
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
                                    id="datepicker-autoclose" id="date" name="date" value="{{ $date }}"
                                    required {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}}>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="name">Name<span class="text-danger">*</span></label>
                    <input type="text" name="name" parsley-trigger="change" required
                        placeholder="Enter customer name" class="form-control" id="name"
                        value="{{ $inquiry->customer?->name ?? $inquiry->name }}"
                        {{ $inquiry->status == 'sales' ? 'readonly disabled' : '' }}
                        {{ $isCustomerLocked && $inquiry->status != 'sales' ? 'readonly' : '' }}>
                    @if ($isCustomerLocked && $inquiry->status != 'sales')
                        <small class="text-muted">Customer mengikuti relasi inquiry dan tidak bisa diubah dari form ini.</small>
                    @endif
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
                    <select class="form-control select2 channel" required name="channel" id="channel" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}}>
                        {!! selectGenerate('Channel', $channels, 'id', ['id', 'channel_name'], $inquiry->channel_id) !!}
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="">Website</label>
                    <br>
                    <select class="form-control select2 website" name="website" id="website" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}}>
                        {!! selectGenerate('Website', $websites, 'id', ['id', 'web_domain'], $inquiry->website_id) !!}
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
                    <select class="form-control select2 country" name="country" id="country" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}} required>
                        {!! selectGenerate('Country', $countries, 'id', ['country_code', 'country_name'], $inquiry->country_code) !!}
                    </select>

                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="city">City</label>
                    <input type="text" name="city" parsley-trigger="change" placeholder="Enter customer city"
                        class="form-control" id="city" value="{{ $inquiry->city }}" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}}>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="phone">Phone</label>
                    <input type="text" maxlength="15" name="phone" parsley-trigger="change"
                        placeholder="Enter customer phone number" class="form-control" id="phone"
                        value="{{ $inquiry->phone }}" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}}>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" parsley-type="email"
                        placeholder="Enter a valid e-mail" value="{{ $inquiry->email }}" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}} />
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <div class="col-12">
                    <label for="phone">Destination</label>
                    <select class="form-control select2 destination" name="destination" id="destination" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}}>
                        {!! selectGenerate('Destination', $countries, 'id', ['country_code', 'country_name'], $inquiry->destination_id) !!}
                    </select>
                </div>
            </div>
        </div>
        <div class="col-6 ">
            <div class="form-group">
                <div class="col-12">
                    <label for="email">Status</label>
                    <select class="form-control select2" name="status" id="status" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}}>
                        <option value="onprogress" {{ $inquiry->status == 'onprogress' ? 'selected' : '' }}>On Progress
                        </option>
                        <option value="junk" {{ $inquiry->status == 'junk' ? 'selected' : '' }}>Junk
                        </option>
                        <option value="notdeal" {{ $inquiry->status == 'notdeal' ? 'selected' : '' }}>Not Deal
                        </option>
                        <option value="sales" {{ $inquiry->status == 'sales' ? 'selected' : '' }}>Sales ( Deal )
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group platform-class" style="display: none">
        <div class="col-6 pr-3">
            <label for="email">Platform</label>
            <select class="form-control select2" name="platform" id="platform" {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}}>
                <option value="">-- Select Platform --</option>
                <option value="indonesiacocopeat" {{ $inquiry->platform == 'indonesiacocopeat' ? 'selected' : '' }}>
                    indonesiacocopeat.com</option>
                <option value="unknown" {{ $inquiry->platform == 'unknown' ? 'selected' : '' }}>Unknown</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-12">
            <label for="note">Note</label>
            <textarea id="note" name="note" class="form-control" maxlength="225" rows="3"
                placeholder="Add note..." {{$inquiry->status == 'sales' ? 'readonly disabled' : ''}} >{{ $inquiry->note }}</textarea>
        </div>
    </div>

    <div class="form-group text-right mb-0">
        <a href="{{ route('inquiry.index') }}" class="btn btn-light waves-effect waves-light mr-1">
            <i class=" mdi mdi-arrow-collapse-left"></i><span class="d-none d-sm-inline-block ml-2">Back</span>
        </a>
        <button class="btn btn-primary waves-effect waves-light mr-1 btn-submit-edit-inq" type="button">
            <i class="mdi mdi-arrow-collapse-right mr-2"></i>Next
        </button>
    </div>
</form>
