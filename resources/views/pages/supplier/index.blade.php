@extends('layouts.base')
@section('title', 'Supplier')

@section('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.css') }}"
    rel="stylesheet" type="text/css" />


{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="header-title mb-3">Supplier List</h4>

                @can('add supplier')
                    <div class="row justify-content-between px-2 my-2">
                        @include('pages.supplier.modals.create-modal')
                        <div class="btn-group-wrapper" role="group">
                            <div class="btn-group" role="group">
                                <form action="{{ route('supplier.import') }}" method="post" id="form-import" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" class="filestyle" data-input="false" data-btnClass="btn-success"
                                        data-text="<i class='mdi mdi-file-excel'></i> Import Bulk" name="excel_file">
                                </form>
                                <form action="{{ route('supplierUpdate.import') }}" method="post" id="form-import-update" enctype="multipart/form-data" class="mr-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" class="filestyle" data-input="false" data-btnClass="btn-success"
                                        data-text="<i class='mdi mdi-file-excel'></i> Update Bulk" name="excel_file_update">
                                </form>
                            </div>
                            <div class="btn-group" role="group">

                                <a class="btn btn-success waves-effect waves-light rounded" href="{{ route('supplier.import_template') }}">
                                    <i class="mdi mdi-file-document"></i> Get Import Template
                                </a>

                                <button class="btn btn-success waves-effect waves-light rounded" onclick="openExportModal()">
                                    <i class="mdi mdi-file-export"></i> Get Update Template
                                </button>
                            </div>
                        </div>
                    </div>
                @endcan

                {{ $dataTable->table(
                        attributes: [
                            'class' => 'table table-bordered table-bordered dt-responsive nowrap',
                            'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
                        ],
                    ) }}
            </div>
        </div>
    </div>
    <!-- end row -->
</div>
<!-- end container-fluid -->


<!-- EDIT MODAL-->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog"></div>
</div>
<!--END EDIT MODAL-->



<!-- SHOW MODAL-->
@include('pages.supplier.modals.view-modal')
@include('pages.supplier.modals.export-modal')
<!-- END SHOW MODAL-->
@endsection

@section('js-vendor')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-filestyle2/bootstrap-filestyle.min.js') }}">
</script>
<script src="{{ asset('assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}">
</script>

<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

{{-- Store data create supplier using ajax --}}
<script>
    $('[name="excel_file"]').change(function (e) {
        $('#form-import').submit();
    });

    $('[name="excel_file_update"]').change(function (e) {
        Swal.fire({
            title: 'Please wait...',
            text: 'Your import is being processed.',
            icon: 'info',

            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });

        $('#form-import-update').submit();
    });

</script>
<!-- untuk open modal Export -->
<script>
    function openExportModal() {
        $('#exportModal').modal('show');
    }
</script>

@if(session('success'))
    <script>
        toastSuccess("{{ session('success') }}");

    </script>
@endif

@if(session('danger'))
    <script>
        toastDanger("{{ session('danger') }}");

    </script>
@endif
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush
