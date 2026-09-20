@php
    $jobProductFormConfig = [
        'formSelector' => '#edit-form',
        'mainCategorySelector' => '#main_category-edit',
        'supplierSelector' => '#supplier-edit',
        'productSelector' => '#job_has_product-edit',
        'stockSelector' => '#stock-edit',
        'stockValidationSelector' => '#stock-validation-edit',
        'qtyUnitSelector' => '#qty-unit-edit',
        'submitButtonSelector' => '.btn-edit-submit',
        'validationWrapperClass' => 'validate-input-edit',
        'fieldIdSuffix' => '-edit',
        'initializePlugins' => true,
        'triggerProductChangeAfterLoad' => true,
        'selectedSupplierId' => $jobProduct->supplier_id,
        'selectedProductId' => $jobProduct->product_id,
        'supplierOptionsUrl' => route('api.supplier.options'),
        'productOptionsUrl' => route('api.job_product.product_options'),
        'productDetailBaseUrl' => url('/api/job-product/product-detail'),
        'submitUrl' => route('job_product.update', [$jobProduct->job_id, $jobProduct->id]),
        'redirectUrl' => route('job_product.index', $jobProduct->job_id),
        'csrfToken' => csrf_token(),
        'httpMethod' => 'PUT',
        'payloadSelectors' => [
            'product_id' => '#job_has_product-edit',
            'qty' => '#qty-edit',
            'price' => '#price-edit',
            'note' => '#note-edit',
        ],
    ];
@endphp

@include('pages.job.edit.components.job-product-form-script')
