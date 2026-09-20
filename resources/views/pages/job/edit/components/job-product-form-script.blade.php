<script>
    (function (config) {
        const $form = $(config.formSelector);
        const $mainCategory = $(config.mainCategorySelector);
        const $supplier = $(config.supplierSelector);
        const $product = $(config.productSelector);
        const $stock = $(config.stockSelector);
        const $stockValidation = $(config.stockValidationSelector);
        const $qtyUnit = $(config.qtyUnitSelector);
        const $submitButton = $(config.submitButtonSelector);

        if (!$form.length) {
            return;
        }

        if (config.initializePlugins) {
            $form.find('.select2').select2();
            $form.find('.autoNumeric').autoNumeric();
        }

        function resetProductState() {
            $product.val(null).prop('disabled', true).html('');
            $stock.html('');
            $stockValidation.val('');
            $qtyUnit.text('-');
        }

        function clearFieldError(selector) {
            const $field = $(selector);
            const $wrapper = $field.closest('.' + config.validationWrapperClass);

            if ($wrapper.hasClass('has-error')) {
                $wrapper.removeClass('has-error');
                $wrapper.find('.help-block').remove();
            }
        }

        function showFieldError(selector, message) {
            const $field = $(selector);
            const $wrapper = $field.closest('.' + config.validationWrapperClass);

            if (!$wrapper.length) {
                return;
            }

            clearFieldError(selector);
            $wrapper.addClass('has-error')
                .append('<span class="help-block"><mdall>' + message + '</mdall></span>');
        }

        function resolveFieldSelector(key) {
            if (key === 'product_id') {
                return config.productSelector;
            }

            const suffix = config.fieldIdSuffix || '';
            return '#' + key + suffix;
        }

        function refreshSupplierOptions(selectedSupplierId = null, callback = null) {
            const mainCategoryId = $mainCategory.val();

            if (!mainCategoryId) {
                $supplier
                    .html('<option selected disabled>-- Select supplier --</option>')
                    .prop('disabled', true);

                if (typeof callback === 'function') {
                    callback();
                }
                return;
            }

            $.ajax({
                url: config.supplierOptionsUrl,
                type: 'GET',
                data: {
                    main_category_id: mainCategoryId,
                    selected: selectedSupplierId,
                },
                dataType: 'json',
                success: function (response) {
                    $supplier.html(response.options).prop('disabled', false);
                },
                complete: function () {
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            });
        }

        function fetchProductOptions(selectedProductId = '') {
            const mainCategoryId = $mainCategory.val();

            if (!mainCategoryId) {
                resetProductState();
                return;
            }

            const productOptionsUrl = selectedProductId
                ? config.productOptionsUrl + '/' + selectedProductId
                : config.productOptionsUrl;

            $.ajax({
                url: productOptionsUrl,
                type: 'GET',
                data: {
                    selected: selectedProductId,
                    main_category_id: mainCategoryId,
                    supplier_id: $supplier.val(),
                },
                dataType: 'json',
                success: function (response) {
                    $product.removeAttr('disabled').html(response.options);
                    $stock.html('');
                    $stockValidation.val('');
                    $qtyUnit.text('-');

                    if (config.triggerProductChangeAfterLoad) {
                        $product.trigger('change');
                    }
                }
            });
        }

        $mainCategory.off('change.jobProductForm').on('change.jobProductForm', function () {
            refreshSupplierOptions(null, function () {
                fetchProductOptions();
            });
        });

        $supplier.off('change.jobProductForm').on('change.jobProductForm', function () {
            fetchProductOptions();
        });

        $product.off('change.jobProductForm').on('change.jobProductForm', function () {
            const productId = $(this).val();

            if (!productId) {
                $stock.html('');
                $stockValidation.val('');
                $qtyUnit.text('-');
                return;
            }

            $.ajax({
                url: config.productDetailBaseUrl + '/' + productId,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    const stock = Number(response.qty || 0);
                    const unit = response.unit ? response.unit.unit_name : '-';

                    $stock.html(stock + ' ' + unit);
                    $stockValidation.val(stock);
                    $qtyUnit.text(unit);
                }
            });
        });

        $submitButton.off('click.jobProductForm').on('click.jobProductForm', function (e) {
            e.preventDefault();

            setAutoNumericRawValue();
            $form.parsley().validate();

            if (!$form.parsley().isValid()) {
                return;
            }

            const payload = {
                _token: config.csrfToken,
                _method: config.httpMethod,
            };

            $.each(config.payloadSelectors, function (key, selector) {
                payload[key] = $(selector).val();
            });

            $.ajax({
                type: 'POST',
                url: config.submitUrl,
                data: payload,
                success: function () {
                    window.location.href = config.redirectUrl;
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;

                    if ($.isEmptyObject(response) === false) {
                        $.each(response.errors, function (key, value) {
                            showFieldError(resolveFieldSelector(key), value);
                        });
                    }
                }
            });
        });

        refreshSupplierOptions(config.selectedSupplierId, function () {
            fetchProductOptions(config.selectedProductId);
        });
    })(@json($jobProductFormConfig));
</script>
