<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <h4 class="header-title" style="font-size: 12px">Sample</h4>

            <div class="row">
                <div class="col-md">
                    @if($job->statusIsOpen() && auth()->user()->can('job statement add stock'))
                        <button type="button" class="btn btn-success waves-effect waves-light btnAddStock mr-2 my-2"
                            data-toggle="modal" data-target="#addStatementStock" data-stock-title="Sample"
                            data-with-sample="only">
                            <i class="fas fa-plus-square mr-1"></i> Add Sample</button>
                    @endif
                </div>
            </div>

            <table class="table">
                <thead>
                    <th>No.</th>
                    <th>PO Sample</th>
                    <th>SKU</th>
                    <th>Sample Name</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    @foreach($samples as $stock)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('po_stock.detail', $stock->inventoryStock?->inventoryIn?->poStock?->id) }}">
                                    {{ $stock->inventoryStock?->inventoryIn?->poStock?->unique_id }}
                                </a>
                            </td>
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
                        <td colspan="7" class="text-right">
                            <span class="total-label">TOTAL SAMPLE:
                                {{ currencyFormat($samples->sum('total')) }}</span>
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