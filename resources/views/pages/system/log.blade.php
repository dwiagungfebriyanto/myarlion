@extends('layouts.base')
@section('title', 'Log')


@section('css')
{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />

<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="row">
                    <h4 class="header-title col-12 mb-3">Activity Log</h4>

                    <div class="col-3">
                        <select id="causer" class="form-control select2">
                            {!! selectGenerate('All', $causers, 'id', ['name', 'position'], request('causer')) !!}
                        </select>
                    </div>
                </div>
                <br>

                {{ $dataTable->table(
                    attributes: [
                        'class' => 'table table-bordered table-bordered dt-responsive nowrap',
                        'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
                    ],
                ) }}
            </div>
            {{-- /.card-box --}}
        </div>
        {{-- /.col-12 --}}
    </div>
    {{-- /.row --}}
</div>
{{-- /.container-fluid --}}

@include('pages.system.components.log_modal_detail')
@endsection


@section('js-vendor')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
@endsection


@push('scripts')
    {{ $dataTable->scripts() }}

    <script>
        $(function () {
            $('.select2').select2();

            $('#causer').change(function (e) { 
                window.location.href = "{{ route('system.log') }}/?causer=" + $(this).val();
            });
        });

    </script>
@endpush
