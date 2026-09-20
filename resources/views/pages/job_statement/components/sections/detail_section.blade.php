<div class="row">
    <div class="col-md-6 col-xl-3 h">
        <div class="card-box tilebox-one">
            <i class="fas fa-user-check float-right text-muted"></i>
            <h5 class="text-muted text-uppercase mt-0">JOB ID : {{ $job->code }}</h5>
            <h3>{{ $job->customer->code . ' - ' . $job->customer->name }}</h3>
        </div>
    </div>

    <div class="col-md">
        <div class="col-md col-xl-6 ml-auto">
            <div class="card-box tilebox-one">
                <i class="fas fa-user-tie float-right text-muted"></i>
                <h6 class="text-muted text-uppercase mt-0">Marketing</h6>
                <h4>{{ $job->marketing->name }}</h4>
            </div>
        </div>

        <div class="col-md col-xl-6 ml-auto">
            <div class="card-box tilebox-one">
                <i class="fas fa-money-bill-wave float-right text-muted"></i>
                <h6 class="text-muted text-uppercase mt-0">Overall Expense</h6>
                <h4>{{ currencyFormat($overallExpenses) }}</h4>
            </div>
        </div>

    </div>
    {{-- /.col-md --}}
</div>
{{-- /.row --}}
<hr class="mb-4">
