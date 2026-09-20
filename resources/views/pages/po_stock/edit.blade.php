@extends('layouts.base')
@section('title', $pageTitle)

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/select2/select2.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" type="text/css" />
@endsection

@section('content')
@php
    $mainCategoryName = $poStock->mainCategory?->main_category_name ?? '';
    $supplierCode = $poStock->supplier?->code;
    $supplierCode = is_null($supplierCode) ? null : str_pad((string) $supplierCode, 3, '0', STR_PAD_LEFT);
    $supplierName = $poStock->supplier?->supplier_name ?? '';
    $supplierLabel = collect([$mainCategoryName, $supplierCode, $supplierName])
        ->filter(fn ($value) => $value !== null && $value !== '')
        ->implode(' | ');
    $mainCategoryName = $mainCategoryName === '' ? '-' : $mainCategoryName;
    $supplierLabel = $supplierLabel === '' ? '-' : $supplierLabel;
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="header-title mb-0">{{ $pageTitle }}</h4>
                    <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary btn-rounded">
                        <i class="mdi mdi-arrow-left mr-1"></i> Back
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <div class="font-weight-bold mb-1">Please fix the following validation errors:</div>
                        <ul class="mb-0 pl-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ $actionUrl }}" id="form-edit-po" class="form-parsley" method="post">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="po-type">PO Type</label>
                            <input type="text" id="po-type" class="form-control text-capitalize" value="{{ $poStock->po_type }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="po-unique-id">Unique ID</label>
                            <input type="text" id="po-unique-id" class="form-control" value="{{ $poStock->unique_id }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="main-category">Main Category</label>
                            <input type="text" id="main-category" class="form-control" value="{{ $mainCategoryName }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="supplier">Supplier</label>
                            <input type="text" id="supplier" class="form-control" value="{{ $supplierLabel }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="product-sku">Product SKU<span class="text-danger">*</span></label>
                            <select id="product-sku" class="form-control select2 select2-multiple"
                                name="product_id[]" multiple required>
                                {!! $productOptions !!}
                            </select>

                            @error('product_id')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>Quantity<span class="text-danger">*</span></label>
                            <div id="form-qty-area">
                                @foreach($selectedProductIDs as $productId)
                                    @php
                                        $detail = $detailsByProductId->get((string) $productId) ?? $detailsByProductId->get($productId);
                                        $product = $productsById->get((int) $productId) ?? $detail?->product;
                                        $unitName = $product?->unit?->unit_name ?? $detail?->unit?->unit_name ?? '-';
                                        $qtyValue = old("quantity.$productId", $detail?->qty);
                                        $unitValue = old("unit.$productId", $detail?->unit_id ?? $product?->unit_id);
                                    @endphp

                                    <div class="form-group" id="select{{ $productId }}">
                                        <div class="col-12 validate-input">
                                            @if(!$product)
                                                <div class="alert alert-warning mb-2">
                                                    Product ID {{ $productId }} sudah tidak tersedia. Silakan pilih produk lain.
                                                </div>
                                            @else
                                                <h6>Qty for {{ $product->sku }} ({{ $unitName }})<span class="text-danger">*</span></h6>
                                                <input type="text" class="form-control form-qty autonumber"
                                                    data-product-id="{{ $productId }}"
                                                    name="quantity[{{ $productId }}]"
                                                    value="{{ $qtyValue }}"
                                                    autocomplete="off">
                                                <input type="hidden" name="unit[{{ $productId }}]" value="{{ $unitValue }}">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label>Unit Price<span class="text-danger">*</span></label>
                            <div id="form-price-area">
                                @foreach($selectedProductIDs as $productId)
                                    @php
                                        $detail = $detailsByProductId->get((string) $productId) ?? $detailsByProductId->get($productId);
                                        $product = $productsById->get((int) $productId) ?? $detail?->product;
                                        $unitName = $product?->unit?->unit_name ?? $detail?->unit?->unit_name ?? '-';
                                        $priceValue = old("price.$productId", $detail?->price);
                                    @endphp

                                    @if($product)
                                        <div class="form-group" id="select{{ $productId }}">
                                            <div class="col-12 validate-input">
                                                <h6>Unit Price for {{ $product->sku }} ({{ $unitName }})<span class="text-danger">*</span></h6>
                                                <input type="text" placeholder="harga satuan" data-a-sign="Rp "
                                                    class="form-control autonumber disableEnterSubmit"
                                                    name="price[{{ $productId }}]"
                                                    value="{{ $priceValue }}">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="shipping-cost">Shipping Cost</label>
                            <input id="shipping-cost" type="text" placeholder="Ongkir" data-a-sign="Rp "
                                class="form-control autonumber disableEnterSubmit autoCalculateInput"
                                name="shipping_cost"
                                value="{{ old('shipping_cost', $poStock->shipping_cost ?? 0) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="note">Notes<span class="text-danger">*</span></label>
                            <textarea id="note" class="form-control" rows="3" name="note">{{ old('note', $poStock->note) }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="additional-expenses">Additional Expenses</label>
                            <input id="additional-expenses" type="text" data-a-sign="Rp "
                                class="form-control autonumber autoCalculateInput"
                                name="additional_expenses"
                                value="{{ old('additional_expenses', $poStock->additional_expenses ?? 0) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="total">Total<span class="text-danger">*</span></label>
                            <input id="total" type="text" data-a-sign="Rp "
                                class="form-control autonumber"
                                name="total"
                                value="{{ old('total', $poStock->total ?? 0) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="status">Status<span class="text-danger">*</span></label>
                            <select id="status" class="form-control select2" name="status" required>
                                {!! selectGenerate(null, $statuses, 'value', 'name', old('status', $poStock->status)) !!}
                            </select>
                        </div>
                    </div>

                    <div class="col-12 px-0 row justify-content-end">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-mask-plugin/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-masks.init.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

<script>
    $(function () {
        const oldQuantity = @json((array) old('quantity', []));
        const oldPrice = @json((array) old('price', []));

        function hasValue(source, key) {
            return Object.prototype.hasOwnProperty.call(source, key) ||
                Object.prototype.hasOwnProperty.call(source, String(key));
        }

        function getValue(source, key, fallback = '') {
            if (hasValue(source, key)) {
                return source[key] ?? source[String(key)] ?? fallback;
            }

            return fallback;
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function addQtyForm(selectedId, sku, productId, unitId, unitName, value = '') {
            if ($(`#form-qty-area #select${selectedId}`).length) {
                return;
            }

            const qtyValue = value === null ? '' : value;
            const html = `<div class="form-group" id="select${selectedId}">
                            <div class="col-12 validate-input">
                                <h6>Qty for ${escapeHtml(sku)} (${escapeHtml(unitName)})<span class="text-danger">*</span></h6>
                                <input type="text" class="form-control form-qty autonumber"
                                    data-product-id="${escapeHtml(productId)}"
                                    name="quantity[${escapeHtml(productId)}]"
                                    value="${escapeHtml(qtyValue)}"
                                    autocomplete="off">
                                <input type="hidden" name="unit[${escapeHtml(productId)}]" value="${escapeHtml(unitId)}">
                            </div>
                        </div>`;

            $('#form-qty-area').append(html);
        }

        function addPriceForm(selectedId, sku, productId, unitName, value = '') {
            if ($(`#form-price-area #select${selectedId}`).length) {
                return;
            }

            const priceValue = value === null ? '' : value;
            const html = `<div class="form-group" id="select${selectedId}">
                            <div class="col-12 validate-input">
                                <h6>Unit Price for ${escapeHtml(sku)} (${escapeHtml(unitName)})<span class="text-danger">*</span></h6>
                                <input type="text" placeholder="harga satuan" data-a-sign="Rp "
                                    class="form-control autonumber disableEnterSubmit"
                                    name="price[${escapeHtml(productId)}]"
                                    value="${escapeHtml(priceValue)}">
                            </div>
                        </div>`;

            $('#form-price-area').append(html);
        }

        function getProductOptionData(optionElement, fallbackSelectedId = null) {
            if (!optionElement || !optionElement.dataset) {
                return null;
            }

            const selectedId = fallbackSelectedId ?? optionElement.value;
            const productId = optionElement.dataset.id;
            const sku = optionElement.dataset.sku;
            const unitId = optionElement.dataset.unitId;
            const unitName = optionElement.dataset.unitName;

            if (!selectedId || !productId || !sku || !unitId || !unitName) {
                return null;
            }

            return {
                selectedId,
                productId,
                sku,
                unitId,
                unitName,
            };
        }

        function getAutoNumericValue(inputElement) {
            const element = $(inputElement);

            if (!element.length) {
                return 0;
            }

            let rawValue = element.val();

            if (typeof element.autoNumeric === 'function') {
                try {
                    rawValue = element.autoNumeric('get');
                } catch (e) {
                    rawValue = element.val();
                }
            }

            const numericValue = parseFloat(rawValue);

            return isNaN(numericValue) ? 0 : numericValue;
        }

        function recalculateTotal() {
            let total = 0;

            $('#form-edit-po .form-qty').each(function () {
                const productId = $(this).data('product-id');
                const qty = getAutoNumericValue(this);
                const priceInput = $(`#form-edit-po [name="price[${productId}]"]`);
                const price = getAutoNumericValue(priceInput);

                total += qty * price;
            });

            $('#form-edit-po .autoCalculateInput').each(function () {
                total += getAutoNumericValue(this);
            });

            if (typeof $('#total').autoNumeric === 'function') {
                $('#total').autoNumeric('set', total);
            } else {
                $('#total').val(total);
            }
        }

        $('#product-sku').on('select2:select', function (e) {
            const selectedOption = e.params.data;
            const optionData = getProductOptionData(selectedOption.element, selectedOption.id);

            if (!optionData) {
                return;
            }

            addQtyForm(
                optionData.selectedId,
                optionData.sku,
                optionData.productId,
                optionData.unitId,
                optionData.unitName,
                getValue(oldQuantity, optionData.productId, '')
            );
            addPriceForm(
                optionData.selectedId,
                optionData.sku,
                optionData.productId,
                optionData.unitName,
                getValue(oldPrice, optionData.productId, '')
            );

            $('.autonumber').autoNumeric('init');
            recalculateTotal();
        });

        $('#product-sku').on('select2:unselect', function (e) {
            const unselectedOption = e.params.data;

            $(`#form-qty-area #select${unselectedOption.id}`).remove();
            $(`#form-price-area #select${unselectedOption.id}`).remove();
            recalculateTotal();
        });

        $('#form-edit-po').on('keyup change input blur', '.form-qty, [name^="price["], .autoCalculateInput', function () {
            recalculateTotal();
        });

        $('#form-edit-po').submit(function () {
            if (typeof setAutoNumericRawValue === 'function') {
                setAutoNumericRawValue();
            }
        });

        $('.autonumber').autoNumeric('init');
        recalculateTotal();
    });
</script>

@if(session('success'))
    <script>
        toastSuccess('{{ session("success") }}');
    </script>
@endif

@if(session('danger'))
    <script>
        toastDanger('{{ session("danger") }}');
    </script>
@endif
@endpush
