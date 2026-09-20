@extends('layouts.base')
@section('title', 'System')

@section('css')
<link href="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet"
    type="text/css" />
@endsection

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card-box">

                <div class="button-list">
                    <a href="{{ route('system.cache_clear') }}"
                        class="btn btn-block btn-info waves-effect waves-light mb-1">
                        Run <span style="font-weight: bold">"artisan cache:clear"</span></a>

                        <a href="{{ route('system.log') }}"
                            class="btn btn-block btn-warning waves-effect waves-light mb-1">
                            View logs</a>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@section('js-vendor')
<script src="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.js"></script>

@if(session('success'))
    <input type="hidden" id="session-success" value="{{ session('success') }}">
    <script>
        $(document).ready(function () {
            $.toast({
                heading: "Success!",
                text: $('#session-success').val(),
                position: "top-right",
                loaderBg: "#5ba035",
                icon: "success",
                hideAfter: 3e3,
                stack: 1
            })
        });

    </script>
@endif

@if(session('danger'))
    <input type="hidden" id="session-danger" value="{{ session('danger') }}">
    <script>
        $(document).ready(function () {
            $.toast({
                heading: "Failed!",
                text: $('#session-danger').val(),
                position: "top-right",
                loaderBg: "#bf441d",
                icon: "error",
                hideAfter: 3e3,
                stack: 1
            })
        })

    </script>
@endif
@endsection
