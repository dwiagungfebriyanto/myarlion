@extends('layouts.base')
@section('title', 'Create Account')

@section('css')
    <!-- Plugins css -->
    {{-- <link href="{{ URL::to('/') }}/assets/libs/dropzone/dropzone.min.css" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ URL::to('/') }}/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />

    <style>
        .help-block {
            color: red
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <h4 class="header-title">Create Account</h4>

                    <form action="{{ route('account.store') }}" method="post" class="parsley-examples"
                        data-parsley-validate>
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" parsley-trigger="change" required
                                        placeholder="Enter your name" class="form-control" id="name"
                                        value="{{ old('name') }}">
                                    @if ($errors->get('name'))
                                        <span class="help-block">
                                            {{ $errors->first('name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username<span class="text-danger">*</span></label>
                                    <input type="username" name="username" parsley-trigger="change" required
                                        placeholder="Enter username" class="form-control" id="username"
                                        value="{{ old('username') }}">
                                    @if ($errors->get('username'))
                                        <span class="help-block">
                                            {{ $errors->first('username') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Position<span class="text-danger">*</span></label>
                                    <input type="text" name="position" parsley-trigger="change" required
                                        placeholder="Enter your position" class="form-control" id="position"
                                        value="{{ old('position') }}">
                                    @if ($errors->get('position'))
                                        <span class="help-block">
                                            {{ $errors->first('position') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Role<span class="text-danger">*</span></label>
                                    <select id="role" class="form-control selectpicker" name="role">
                                        {!! selectGenerate('Role', $roles, 'id', 'name', old('role')) !!}
                                    </select>
                                    @if ($errors->get('role'))
                                        <span class="help-block">
                                            {{ $errors->first('role') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email address<span class="text-danger">*</span></label>
                            <input type="email" name="email" parsley-trigger="change" required
                                placeholder="Enter your email" class="form-control" id="email"
                                value="{{ old('email') }}">
                            @if ($errors->get('email'))
                                <span class="help-block">
                                    {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>

                        {{-- <div class="form-group">
                            @if ($errors->get('signature'))
                                <span class="help-block">
                                    {{ $errors->first('signature') }}
                                </span>
                            @endif
                            <label for="signature">Signature<span class="text-danger">*</span></label>

                            <div class="dropzone" id="my-dropzone">
                                <div class="fallback">
                                    <input name="file" type="file" />
                                </div>

                                <div class="dz-message needsclick">
                                    <i class="h1 text-muted dripicons-cloud-upload"></i>
                                    <h3>Drop files here or click to upload.</h3>
                                </div>
                            </div>


                        </div> --}}

                        <div class="form-group">
                            <label for="pass1">Password<span class="text-danger">*</span></label>
                            <input id="pass1" type="password" placeholder="Password" required class="form-control"
                                name="password">
                            @if ($errors->get('password'))
                                <span class="help-block">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="passWord2">Confirm Password <span class="text-danger">*</span></label>
                            <input data-parsley-equalto="#pass1" type="password" required placeholder="Password"
                                class="form-control" id="passWord2" name="password_confirmation">
                            {{-- @if ($errors->get('pass_confirm'))
                                <span class="help-block">
                                    {{ $errors->first('pass_confirm') }}
                                </span>
                            @endif --}}
                        </div>

                        {{-- <div class="form-group">
                            <div class="checkbox checkbox-purple">
                                <input id="checkbox6a" type="checkbox">
                                <label for="checkbox6a">
                                    Remember me
                                </label>
                            </div>

                        </div> --}}

                        <div class="form-group text-right mb-0">
                            <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                                Next
                            </button>
                            <button type="reset" class="btn btn-light waves-effect">
                                Cancel
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- end row -->


    </div> <!-- end container-fluid -->
@endsection

@section('js-vendor')
    <!-- Plugins js -->
    <script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/bootstrap-select/bootstrap-select.min.js"></script>

    <!-- Init js-->
    <script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pages/form-masks.init.js"></script>

@endsection

{{-- @push('scripts')
    <!-- Plugins js -->

    <script src="{{ URL::to('/') }}/assets/libs/dropzone/dropzone.min.js"></script>
    <script>
        Dropzone.options.myDropzone = {
            url: "{{ route('account.store') }}",
            paramName: "file",
            maxFilesize: 2, // MB
            maxFiles: 1,
            acceptedFiles: ".jpg,.jpeg,.png,",
            dictFallbackMessage: "Your browser does not support drag and drop file uploads.",
            dictFileTooBig: "File is too big ( 2 MB). Max filesize: 2 MB.",
            dictInvalidFileType: "Invalid file type. Only .jpg, .jpeg, .png files are allowed.",
            init: function() {
                this.on("success", function(file, response) {
                    alert(response.success);
                });
            }
        };
    </script>
@endpush --}}
