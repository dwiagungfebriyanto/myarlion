@extends('pages.job.edit')


@section('datatable')
<div class="row">
    <div class="col-12">
        <div class="card-box">
            <div class="d-flex justify-content-between align-items-center px-2 flex-wrap mb-2">
                <h4 class="header-title mb-2 mb-md-0">Add Product to Job</h4>
                <div class="btn-group">
                    @if($job->statusIsOpen())
                        @include('pages.job.edit.components.modal-add-job-product')
                    @endif
                    
                    @include('pages.job.edit.components.modal-export-invoice')
                </div>
            </div>
            <hr>
            {{ $dataTable->table(
                    attributes: [
                        'class' => 'table table-bordered table-bordered dt-responsive',
                        'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
                    ],
                ) }}
        </div>
    </div>
</div>
{{-- /.row --}}

<!-- EDIT product MODAL -->
<div id="editProductModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
    </div>
</div>
<!-- END EDIT product MODAL -->
@endsection


@section('datatable_scripts')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

<!-- Plugin js-->
<script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<!-- Init js-->
<script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-mask-plugin/jquery.mask.min.js') }}"></script>

@include('pages.job.edit.components.modal-add-job-product-config')

{{ $dataTable->scripts() }}
@endsection
