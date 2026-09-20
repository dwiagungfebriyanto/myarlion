@extends('layouts.base')
@section('title', $pageTitle)

@section('css')
<link href="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
<link href="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .action-btn {
        margin: 0 3px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex">
                    <h4 class="header-title mb-3">{{ $pageTitle }}</h4>
                </div>

                <br>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                    <tr>
                        <th style="width: 10%">#</th>
                        <th>Warehouse</th>
                        <th>Inventory Stock</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                        @foreach ($returnStocks as $returnStock)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $returnStock->warehouse ? $returnStock->warehouse->warehouse_name : $returnStock->warehouse_id }}</td>
                                <td>
                                    @if ($returnStock->inventoryStock && $returnStock->inventoryStock->product)
                                        {{ $returnStock->inventoryStock->product->skuFormat() }}
                                    @else
                                        #{{ $returnStock->inventory_stock_id }}
                                    @endif
                                </td>
                                <td>{{ optional($returnStock->supplier)->supplier_name ?? '-' }}</td>
                                <td>
                                    @php
                                        $qty = $returnStock->qty;
                                        $unitType = "";

                                        if ($returnStock->inventoryStock && $returnStock->inventoryStock->unit) {
                                            $unitType = $returnStock->inventoryStock->unit->unit_name;
                                        }

                                        $formattedQty = (floor($qty) == $qty) ? number_format($qty, 0) : rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
                                    @endphp
                                    {{ $formattedQty }} {{ $unitType }}
                                </td>
                                <td data-order="{{ $returnStock->total }}">{{ currencyFormat($returnStock->total) }}</td>
                                <td>
                                    @include('pages.return_stock.components.action-button', ['return' => $returnStock])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-vendor')
<script src="{{ URL::to('/') }}/assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/dataTables.responsive.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/datatables/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>

<script>
   $(function () {
    $("#responsive-datatable").dataTable();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let deleteId, inventoryId, deleteQty;

    $(document).off('click', '.delete-btn').on('click', '.delete-btn', function() {
        deleteId = $(this).data('id');
        inventoryId = $(this).data('inventory-id');
        deleteQty = $(this).data('qty');
        const otherIncomeId = $(this).data('other-income-id');

        Swal.fire({
            title: 'Delete Confirmation',
            text: "Are you sure you want to delete this return stock record? This action will restore the quantity back to inventory.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                if (otherIncomeId) {
                    Swal.fire({
                        title: 'Delete Related Data',
                        text: 'Do you also want to delete the related Other Income record?',
                        icon: 'question',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete both',
                        denyButtonText: 'No, only Return Stock',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteReturnStock(true);
                        } else if (result.isDenied) {
                            deleteReturnStock(false);
                        }
                    });
                } else {
                    deleteReturnStock(false);
                }
            }
        });
    });

    function deleteReturnStock(deleteOtherIncome) {
        $.ajax({
            url: '{{ route("product.return-stock.destroy", ":id") }}'.replace(':id', deleteId),
            type: 'DELETE',
            data: {
                delete_other_income: deleteOtherIncome ? true : false
            },
            success: function(response) {
                if (response.success) {
                    toastSuccess(response.message || 'Return stock successfully deleted.');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastDanger(response.message || 'Failed to delete return stock.');
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An error occurred while deleting the record.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                }

                toastDanger(errorMessage);
            }
        });
    }
});
</script>
@endpush
