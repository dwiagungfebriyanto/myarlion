@extends('layouts.base')
@section('title', $title)


@section('css')
{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">

{{-- plugins --}}
<link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />

<style>
    .btnChangeStatus:hover {
        cursor: pointer;
    }
</style>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title col-12 mb-3">{{ $title }}</h4>

                <div class="row justify-content-end mb-2">
                    <div class="col-md-3">
                        <select class="form-control select2" id="filterStatus">
                            {!! selectGenerate('All', config('constant.edit_amount_approval_status'), 'value', 'key',
                            request('status')) !!}
                        </select>
                    </div>
                </div>


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

@include('pages.job_edit_amount_approval.components.modal_change_status')

@endsection


@section('js-vendor')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<script>
    $(function () {
        $('#filterStatus').change(function (e) {
            window.location.href =
                "{{ route('job.edit_amount_approval.index') }}/?status=" +
                $(this).val();
        });
    });

</script>


@if(session('success'))
    <script>
        $(document).ready(function () {
            toastSuccess('{{ session("success") }}');
        });

    </script>
@endif


@if(session('error'))
    <script>
        $(document).ready(function () {
            toastDanger('{{ session("error") }}');
        })

    </script>
@endif


{{ $dataTable->scripts() }}
@endsection
