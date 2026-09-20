@extends('pages.job.edit')


@section('datatable')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex">
                    <h4 class="header-title mb-3">Job Team</h4>
                </div>

                @if($job->statusIsOpen())
                <div class="col-12 text-right">
                    @if (auth()->user()->can('job statement add team') || auth()->user()->id === $marketing->id)
                        <button type="button" id="btn-add-team" class="btn btn-success waves-effect waves-light"
                            data-toggle="modal" data-target="#teamModal">
                            
                            <i class="mdi mdi-plus mr-1"></i> Add Job Team
                        </button>
                    @endif
                </div>
                <br>
                @endif

                {{ $dataTable->table(
                    attributes: [
                        'class' => 'table table-bordered table-bordered dt-responsive',
                        'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
                    ],
                ) }}

                <button id="reload-job-team" hidden></button>
            </div>
        </div>
        {{-- /.col-12 --}}
    </div>
    {{-- /.row --}}

    @include('pages.job.edit.components.modal-add-job-team')
    @include('pages.job.edit.components.modal-edit-job-team')
@endsection


@section('datatable_scripts')
<!-- Required datatable js -->
<script src="{{ URL::to('/') }}/assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Responsive examples -->
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.min.js"></script>

{{ $dataTable->scripts() }}
@endsection
