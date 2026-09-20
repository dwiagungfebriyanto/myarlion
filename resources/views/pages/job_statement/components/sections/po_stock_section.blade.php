<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <h4 class="header-title" style="font-size: 12px">PO Stock</h4>

            <div class="row">
                <div class="col-md">
                    @if($job->statusIsOpen() && auth()->user()->can('job statement add PO stock'))
                        <button type="button" class="btn btn-success waves-effect waves-light mr-2 my-2"
                            data-toggle="modal" data-target="#addPoStock">
                            <i class="fas fa-plus-square mr-1"></i> Add PO Stock</button>

                            @include('pages.job_statement.components.modals.add_po_stock_modal')
                    @endif
                </div>
            </div>

            <table class="table">
                <thead>
                    <th>No.</th>
                    <th>PO Stock</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Note</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    @foreach($jobPoStocks as $jobPoStock)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $jobPoStock->poStock->unique_id }}</td>
                            <td>{{ $jobPoStock->product->skuFormat() }}</td>
                            <td>{{ $jobPoStock->qty }}</td>
                            <td>{{ currencyFormat($jobPoStock->amount) }}</td>
                            <td>{{ $jobPoStock->note }}</td>
                            <td>
                                @if($job->statusIsOpen())
                                    @include('pages.job_statement.components.action_button.po_stock_action_button')
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="7" class="text-right">
                            <span class="total-label">TOTAL PO STOCK:
                                {{ currencyFormat($totalJobPoStock) }}</span>
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

@include('pages.job_statement.components.modals.edit_po_stock_modal')

@push('scripts')
    <script>
        $(function () {
            $('.btnEditPoStock').click(function (e) {
                let data = $(this).data();

                $('#edit-po-stock').val(data.poStockId).trigger('change').select2();

                $.ajax({
                    type: "get",
                    url: "{{ route('po_stock.get_po_products') }}",
                    data: {
                        po_stock_id: data.poStockId,
                        product_id: data.productId
                    },
                    success: function (response) {
                        $('#edit-po-product').html(response.options);

                        let poDetailId = $('#edit-po-product').find("option:selected").data('poDetailId');
                        $('#formEditPoStock [name="po_detail_id"]').val(poDetailId);
                    }
                });

                $('#formEditPoStock').attr('action', data.url);

                $('#formEditPoStock #edit-qty').autoNumeric('set', data.qty);
                $('#formEditPoStock #edit-amount').autoNumeric('set', data.amount);
                $('#formEditPoStock #edit-note').val(data.note);
            });
        });
    </script>
@endpush
