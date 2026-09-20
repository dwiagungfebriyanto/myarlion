@php
    $jobProductFormConfig = [
        'formSelector' => '#create-form',
        'mainCategorySelector' => '#main_category',
        'supplierSelector' => '#supplier',
        'productSelector' => '#job_has_product',
        'stockSelector' => '#stock',
        'stockValidationSelector' => '#stock-validation',
        'qtyUnitSelector' => '#qty-unit',
        'submitButtonSelector' => '.btn-create-submit',
        'validationWrapperClass' => 'validate-input',
        'fieldIdSuffix' => '',
        'initializePlugins' => false,
        'triggerProductChangeAfterLoad' => false,
        'selectedSupplierId' => null,
        'selectedProductId' => '',
        'supplierOptionsUrl' => route('api.supplier.options'),
        'productOptionsUrl' => route('api.job_product.product_options'),
        'productDetailBaseUrl' => url('/api/job-product/product-detail'),
        'submitUrl' => route('job_product.store', $job),
        'redirectUrl' => route('job_product.index', $job),
        'csrfToken' => csrf_token(),
        'httpMethod' => 'POST',
        'payloadSelectors' => [
            'job_id' => '#job_id',
            'product_id' => '#job_has_product',
            'qty' => '#qty',
            'price' => '#price',
            'note' => '#note',
        ],
    ];
@endphp

@include('pages.job.edit.components.job-product-form-script')
