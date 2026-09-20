@extends('layouts.base')
@section('title', $pageTitle)

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/select2/select2.min.css') }}"
    type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}"
    type="text/css" />

{{-- datatables --}}
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}"
    type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}"
    type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/buttons.bootstrap4.css') }}"
    type="text/css" />
<style>
    .show-all-filter-label {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .po-filter-stack {
        min-width: 320px;
        position: relative;
        z-index: 10;
    }

    .po-filter-stack .select2-container {
        width: 100% !important;
    }
</style>
@endsection

@section('content')
@php
    $showAllData = request()->boolean('show_all');
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex">
                    <h4 class="header-title mb-3">{{ $pageTitle }}</h4>
                    <div class="ml-auto">
                        @can('add PO stock')
                            @include('pages.po_stock.components.create_modal')
                        @endcan
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <form id="tableFilter" action="" method="get">
                        <div class="po-filter-stack">
                            <div class="row">
                                <div class="col">
                                    <select name="type" id="filter-type" class="form-control select2">
                                        {!! selectGenerate('All Type', $poTypes, 'value', 'name', request('type')) !!}
                                    </select>
                                </div>

                                <div class="col">
                                    <select name="status" id="filter-status" class="form-control select2">
                                        {!! selectGenerate('All Status', $statuses, 'value', 'name', request('status')) !!}
                                    </select>
                                </div>
                            </div>

                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox"
                                    class="custom-control-input"
                                    id="showAllData"
                                    name="show_all"
                                    value="1"
                                    {{ $showAllData ? 'checked' : '' }}>
                                <label class="custom-control-label show-all-filter-label" for="showAllData">
                                    <span>Show All Data</span>
                                    <button type="button"
                                        class="btn btn-xs btn-secondary py-0 px-1"
                                        data-toggle="popover"
                                        data-trigger="focus"
                                        data-placement="top"
                                        data-content="Jika dicentang, halaman ini menampilkan semua PO sesuai filter type dan status, termasuk PO yang qty remaining-nya sudah habis dan/atau status complete. Jika tidak dicentang, halaman fokus ke PO operasional aktif dengan remaining qty lebih dari 0.">
                                        <i class="mdi mdi-information-outline"></i>
                                    </button>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                {{ $dataTable->table(
                    attributes: [
                        'class' => 'table table-bordered table-bordered dt-responsive',
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

@include('pages.po_stock.components.edit_status_modal')

@endsection


@push('scripts')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

    {{-- datatable button --}}
    <script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

    <!-- Plugins js -->
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-mask-plugin/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
    <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>

    <!-- Init js-->
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-masks.init.js') }}"></script>
    <script src="{{ asset('assets/js/helper.js') }}"></script>

    <script>
        $(function () {
            $('#tableFilter select').change(function (e) {
                e.preventDefault();
                const form = $('#tableFilter');

                form.submit();
            });

            $('#showAllData').off('change.poStockFilter').on('change.poStockFilter', function () {
                $('#tableFilter').submit();
            });

            $('[data-toggle="popover"]').popover();
        });
    </script>


    {{ $dataTable->scripts() }}


    @if(session('success'))
        <script>
            toastSuccess('{{ session("success") }}');

        </script>
    @endif

    @if(session('danger'))
        <script>
            toastDanger('{{ session("danger") }}');

        </script>
    @endif
@endpush
