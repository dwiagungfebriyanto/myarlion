<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <h4 class="header-title">Expenses</h4>
            @if($job->statusIsOpen() && auth()->user()->can('add cost'))
            <a href="{{ route('accounting.cost.index') }}"
                class="btn btn-success waves-effect waves-light btn-add mr-2 my-2">
                <i class="fas fa-plus-square mr-1"></i> Add Cost</a>
            @endif

            @foreach($job->groupedExpenses() as $key => $expenses)
            <h4 class="header-title" style="font-size: 12px">
                {{ outcomeGroup($key)->name }} : </h4>

            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $expense->date }}</td>
                        <td>{{ $expense->note ?? $expense->description }}</td>
                        <td>{{ currencyFormat($expense->amount) }}</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="4" class="text-right">
                            <span style="font-weight: bold">TOTAL {{ outcomeGroup($key)->name }}:
                                {{ currencyFormat($expensesTotalByCategory[$key]) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endforeach

            <div class="text-right">
                <span class="total-label">TOTAL EXPENSES: {{ currencyFormat($totalExpenses) }}</span>
            </div>
        </div>
        {{-- /.table-responsive --}}
    </div>
    {{-- /.col-md-12 --}}
</div>
{{-- /.row --}}
<hr class="mb-4">
