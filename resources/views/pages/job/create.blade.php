@extends('layouts.base')
@section('title', 'Create Job')

@section('css')
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        .format-numb {
            all: unset;
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <h4 class="header-title">Create Job</h4>

                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="{{ route('job.list.index') }}" class="nav-link">
                                <i class=" mdi mdi-arrow-collapse-left"></i><span
                                    class="d-none d-sm-inline-block ml-2">Back</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#inquiry-job" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                {{-- <i class="far fa-edit"></i> --}}
                                <span class="d-none d-sm-inline-block">Inquiry</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#non-inquiry-job" data-toggle="tab" aria-expanded="true" class="nav-link income">
                                {{-- <i class="mdi mdi-cash-multiple"></i> --}}
                                <span class="d-none d-sm-inline-block">Non Inquiry</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#reguler" data-toggle="tab" aria-expanded="false" class="nav-link">
                                {{-- <i class="fas fa-money-check-alt"></i> --}}
                                <span class="d-none d-sm-inline-block">Reguler</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="inquiry-job">
                            @include('pages.job.components.create.create-content-inquiry')
                        </div>
                        <div class="tab-pane" id="non-inquiry-job">
                            @include('pages.job.components.create.create-content-nonInquiry')
                        </div>
                        <div class="tab-pane" id="reguler">
                            @include('pages.job.components.create.create-content-reguler')
                        </div>
                    </div>

                    @include('pages.job.components.create.job-product-modal')

                </div>
                {{-- /.card-box --}}
            </div>
            {{-- /.col-12 --}}
        </div>
        {{-- /.row --}}
    </div>
    {{-- /.container-fluid --}}
@endsection


@section('js-vendor')
    <!-- Plugins js -->
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-mask-plugin/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>

    <!-- Init js-->
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-masks.init.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>

    @include('pages.job.js.createJobProduct')
    @include('pages.job.js.addJobStore')
@endsection
