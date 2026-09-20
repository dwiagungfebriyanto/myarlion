@extends('layouts.base')
@section('title', 'Other Income')


@section('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/jquery-toast/jquery.toast.min.css') }}" rel="stylesheet"
    type="text/css" />

{{-- datatables --}}
<link href="{{ asset('assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
    type="text/css" />

<style>
.swal2-container {
    z-index: 9999 !important;
}
</style>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex mb-3">
                    <h4 class="header-title mb-3">Other Income</h4>

                    <div class="ml-auto">
                        @can('add other income')
                            @include('pages.other_income.components.modal_add')
                        @endcan
                    </div>
                </div>
                {{-- /.d-flex --}}

                {{-- filter --}}
                <div class="row justify-content-md-end">
                    <div class="col-6 mb-2">
                        <select id="filter-account" class="form-control select2 myFilter" name="account">
                            <option value="">All Bank Accounts</option>

                            @foreach($bankAccounts as $bankAccount)
                                <option value="{{ $bankAccount->id }}"
                                    {{ request()->get('account') == $bankAccount->id ? 'selected' : '' }}>
                                    {{ $bankAccount->bankAccountLabel() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 mb-2">
                        <select id="filter-category" class="form-control select2 myFilter" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_slug }}"
                                    {{ request()->get('category') == $category->category_slug ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 mb-2">
                        <select id="filter-cost-category" class="form-control select2 myFilter" name="cost_category">
                            {!! selectGenerate('All Cost Category', $costCategories, 'id', 'name', request()->get('cost_category')) !!}
                        </select>
                    </div>
                </div>

                {{ $dataTable->table(
                    attributes: [
                        'class' => 'table table-bordered table-bordered dt-responsive nowrap',
                        'style' => 'border-collapse: collapse; border-spacing: 0; width: 100%;',
                    ],
                ) }}

                <div class="card-footer mt-4">
                    <div class="text-muted">Entries without a Cost Category will not be shown in the Income Statement.</div>
                </div>
            </div>
            {{-- /.card-box --}}
        </div>
        {{-- /.col-12 --}}
    </div>
    {{-- /.row --}}
</div>
{{-- /.container-fluid --}}
@endsection


@section('js-vendor')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive datatable -->
<script src="{{ asset('assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>

{{-- datatable button js --}}
<script src="{{ asset('assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/buttons.print.min.js') }}"></script>


<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-toast/jquery.toast.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>
@endsection


@push('scripts')
    {{ $dataTable->scripts() }}

    <script>
        $(function () {
            $('.myFilter').change(function (e) {
                e.preventDefault();
                let params = new URLSearchParams(window.location.search);

                $('.myFilter').each(function (index, element) {
                    params.set($(element).attr('name'), $(element).val());
                });

                window.location.search = params.toString();
            });
        });

        function setEditModalData(e) {
            let data = $(e).data();

            $('#form-edit').attr('action', data.url);
            $('#edit-date').val(data.date);
            $('#edit-account').val(data.account).trigger('change');
            $('#edit-category').val(data.category).trigger('change');
            $('#edit-amount').autoNumeric('set', data.amount);
            $('#edit-description').val(data.description);
        }

        @if(session()->has('success'))
            toastSuccess('{{ session("success") }}');
        @endif

        @if(session()->has('error'))
            toastDanger('{{ session("error") }}');
        @endif
    </script>
@endpush
