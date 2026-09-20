@extends('layouts.base')
@section('title', 'Create Account')

@section('css')
    <!-- Plugins css -->
    <link href="{{ URL::to('/') }}/assets/libs/dropzone/dropzone.min.css" rel="stylesheet" type="text/css" />

    <link href="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />


    <style>
        .help-block {
            color: red
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title">Upload Your Signature</h4>
                @if ($errors->get('file'))
                    <span class="help-block">
                        {{ $errors->first('file') }}
                    </span>
                @endif
                <form action="{{ route('account.uploadSign', $user->id) }}" method="post" class="dropzone"
                    id="my-dropzone">
                    @csrf
                    @method('PUT')

                    <div class="fallback">
                        <input name="file" type="file" />
                    </div>

                    <div class="dz-message needsclick">
                        <i class="h1 text-muted dripicons-cloud-upload"></i>
                        <h3>Drop files here or click to upload.</h3>
                    </div>

                </form>
                <div class="text-right mt-3">
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="btn-submit"><i
                            class="mdi mdi-send mr-1"></i> Submit All</button>

                    {{-- <a
                        href="{{ route('account.index') }}"><i
                            class="mdi mdi-send mr-1"></i> Submit All</a> --}}
                </div>
            </div> <!-- end card-box -->
        </div> <!-- end col-->
    </div>
@endsection


@section('js-vendor')
    <script src="{{ URL::to('/') }}/assets/libs/jquery-toast/jquery.toast.min.js"></script>

    @if (session('success'))
        <input type="hidden" id="session-success" value="{{ session('success') }}">
        <script>
            $(document).ready(function() {
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

@endsection

@push('scripts')
    <!-- Plugins js -->

    <script src="{{ URL::to('/') }}/assets/libs/dropzone/dropzone.min.js"></script>
    <script>
        Dropzone.options.myDropzone = {
            paramName: "file",
            maxFilesize: 2, // MB
            maxFiles: 1,
            addRemoveLinks: true,
            uploadMultiple: false,
            paralellUploads: 1,
            autoProcessQueue: false,
            acceptedFiles: ".jpg,.jpeg,.png,",
            dictFallbackMessage: "Your browser does not support drag and drop file uploads.",
            dictFileTooBig: "File is too big ( 2 MB). Max filesize: 2 MB.",
            dictInvalidFileType: "Invalid file type. Only .jpg, .jpeg, .png files are allowed.",
            init: function() {
                var myDropzone = this;
                this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });

                this.on("addedfile", function() {
                    $('.dz-progress').remove();

                    document.querySelector("button[type=button]").addEventListener("click", function(
                        e) {
                        e.preventDefault();
                        myDropzone.processQueue();
                        window.location.href =
                            "{{ route('account.index', $response = 'success') }}";
                    });
                });



            },

        };

        document.querySelector("button[type=button]").addEventListener("click", function(
            e) {
            window.location.href = "{{ route('account.index', $response = 'successNotSign') }}";
        });
    </script>
@endpush
