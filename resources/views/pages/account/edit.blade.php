@extends('layouts.base')
@section('title', 'Edit Account')

@section('css')
    <!-- Plugins css -->

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
                    <h4 class="header-title">Edit Account</h4>

                    <form action="{{ route('account.update', $user) }}" method="post" class="parsley-examples"
                        data-parsley-validate>
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" parsley-trigger="change" required
                                        placeholder="Enter your name" class="form-control" id="name"
                                        value="{{ $user->name }}">
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
                                        value="{{ $user->username }}">
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
                                        value="{{ $user->position }}">
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
                                        {!! selectGenerate('Role', $roles, 'id', 'name', $user->role_id) !!}
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
                                value="{{ $user->email }}">
                            @if ($errors->get('email'))
                                <span class="help-block">
                                    {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="pass1">Password<span class="text-danger"></span></label>
                            <input id="pass1" type="password" placeholder="Password" class="form-control"
                                name="password">
                            @if ($errors->get('password'))
                                <span class="help-block">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="passWord2">Confirm Password <span class="text-danger"></span></label>
                            <input data-parsley-equalto="#pass1" type="password" placeholder="Password" class="form-control"
                                id="passWord2" name="password_confirmation">

                        </div>

                        <div class="form-group">
                            <label for="signature">Your Signature <span class="text-danger"></span></label>
                            @if ($user->signature)
                                <div class="card" style="width: 200px; height: 200px;">
                                    <img class="card-img img-fluid w-100 h-100"
                                        src="{{ URL::to('/') }}/images/signature_photo/{{ $user->signature }}"
                                        alt="{{ $user->signature }}">
                                </div>
                            @else
                                <br>
                                <span class="help-block">
                                    *Anda belum memiliki tanda tangan
                                </span>
                            @endif


                        </div>

                        <div class="form-group text-right mb-0">
                            <button class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                                Next
                            </button>
                            <a href="{{ route('account.index') }}" class="btn btn-light waves-effect">Cancel</a>
                            {{-- <button type="reset" class="btn btn-light waves-effect">

                            </button> --}}
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
