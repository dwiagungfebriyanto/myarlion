<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Indococo - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::to('/') }}/assets/images/favicon.ico">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS Custom -->
    @yield('css')

    {{-- js custom --}}
    @stack('js-head')

    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet"
        type="text/css" />

    <style>
        .help-block {
            color: red
        }

    </style>
</head>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        @include('layouts.components.sidebar')
        <!-- Left Sidebar End -->


        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Topbar Start -->
            @include('layouts.components.navbar')
            <!-- end Topbar -->


            <!-- Start Content-->
            <div class="content">
                @yield('content')
            </div>
            <!-- end content -->


            <!-- Footer Start -->
            @include('layouts.components.footer')
            <!-- end Footer -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->


    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>

    @stack('datatable-js')

    @yield('js-vendor')

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    <!-- Javascript Custom -->
    @stack('scripts')

    <script src="{{ asset('assets/js/helper.js') }}"></script>

    @if(session('success'))
        <script>
            toastSuccess('{{ session("success") }}')

        </script>
    @endif


    @if(session('error'))
        <script>
            toastDanger('{{ session("error") }}')

        </script>
    @endif
</body>

</html>
