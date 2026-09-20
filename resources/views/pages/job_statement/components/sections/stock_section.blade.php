<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <h4 class="header-title" style="font-size: 12px">Stock</h4>

            <div class="row">
                <div class="col-md">
                    @if($job->statusIsOpen() && auth()->user()->can('job statement add stock'))
                        <button type="button" class="btn btn-success waves-effect waves-light btnAddStock mr-2 my-2"
                            data-toggle="modal" data-target="#addStatementStock" data-stock-title="Stock"
                            data-with-sample="false">
                            <i class="fas fa-plus-square mr-1"></i> Add Stock</button>

                        @include('pages.job_statement.components.modals.add_stock_modal')
                    @endif
                </div>
            </div>

            <table class="table">
                <thead>
                    <th>No.</th>
                    <th>SKU</th>
                    <th>Stock Name</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    @foreach($stocks as $stock)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $stock->productSku() }}</td>
                            <td>{{ $stock->productType() }}</td>
                            <td>{{ $stock->quantity }}</td>
                            <td>{{ currencyFormat($stock->total) }}</td>
                            <td>
                                @if($job->statusIsOpen())
                                    @include('pages.job_statement.components.action_button.stock_action_button')
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="6" class="text-right">
                            <span class="total-label">TOTAL STOCK:
                                {{ currencyFormat($stocks->sum('total')) }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- /.table-responsive --}}
    </div>
    {{-- /.col-md-12 --}}
</div>
{{-- /.row --}}

<hr class="mb-4">

{{-- @include('pages.job_statement.components.modals.edit_stock_modal') --}}