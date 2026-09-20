{{ $dataTable->table(
    attributes: [
        'class' => 'table table-bordered table-bordered dt-responsive nowrap',
        'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;text-align:center;',
    ],
) }}

<script src="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>

<!-- Required datatable js -->
<script src="{{ URL::to('/') }}/assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Responsive examples -->
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.min.js"></script>

{{ $dataTable->scripts() }}
