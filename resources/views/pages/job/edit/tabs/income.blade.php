@extends('pages.job.edit')


@section('datatable')
{{-- # Statistics Income --}}
<div class="row">
    <div class="col-md-6 col-xl-3 h">
        <div class="card-box tilebox-one">
            <i class="fas fa-user-check float-right text-muted"></i>
            <h5 class="text-muted text-uppercase mt-0">JOB ID : {{ $job->code }}</h5>
            <h3><span>{{ $job->customer->name }}</span></h3>
        </div>
    </div>

    <div class="col-md">
        <div class="col-md col-xl-6 ml-auto">
            <div class="card-box tilebox-one">
                <i class="fas fa-money-bill-wave float-right text-muted"></i>
                <h6 class="text-muted text-uppercase mt-0">Total</h6>
                <h4>
                    <span>
                        {{ currencyFormat($job->amount, $job->currency->currency_code) }}
                    </span>
                </h4>
            </div>
        </div>
        {{-- /.col-md col-xl-6 ml-auto --}}

        <div class="col-md col-xl-6 ml-auto">
            <div class="card-box tilebox-one">
                <i class="fas fa-money-check float-right text-muted"></i>
                <h6 class="text-muted text-uppercase mt-0">Outstanding</h6>
                <h4>
                    <span id="outstanding-info">
                        {{ currencyFormat($job->currentOutstanding(), $job->currency->currency_code) }}
                    </span>
                </h4>

                <span class="badge badge-danger" id="outstanding-percentage-info">
                    {{ "$percent_out%" }}</span>

                <span class="text-muted">From total amount</span>
            </div>
        </div>
        {{-- /.col-md col-xl-6 ml-auto --}}
    </div>
    {{-- /.col-md --}}
</div>

{{-- # CRUD Income --}}
<div class="border-top">
    <div class="d-flex justify-content-end mb-2">
        @if($job->statusIsOpen())
            @include('pages.job.edit.components.modal-add-job-income')

            @php
                $lockIcon = (!$job->statusPaymentIsOpen())
                ? 'fa-lock-open'
                : 'fa-lock' ;

                $statusPayment = ($job->statusPaymentIsOpen()) ? 'close' : 'open';
            @endphp

            @if(auth()->user()->can("job income $statusPayment transaction"))
                <a href="{{ route('job_income.change_status_payment', $job) }}"
                    class="btn btn-dark waves-effect waves-light text-capitalize my-2">
                    <i class="fas {{ $lockIcon }} mr-1"></i>
                    {{ "$statusPayment Transaction" }}
                </a>
            @endif
        @endif
    </div>

    {{ $dataTable->table(
        attributes: [
            'class' => 'table table-bordered table-bordered dt-responsive',
            'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
        ],
    ) }}
</div>

@include('pages.job.edit.components.modal-edit-job-income')

@endsection


@section('datatable_scripts')
<!-- Required datatable js -->
<script src="{{ URL::to('/') }}/assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Responsive examples -->
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.min.js"></script>

<!-- Init js-->
<script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/jquery-mask-plugin/jquery.mask.min.js"></script>

{{ $dataTable->scripts() }}
@endsection
