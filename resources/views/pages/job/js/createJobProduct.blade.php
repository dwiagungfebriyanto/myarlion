<script>
    (function () {
        const sectionConfigs = {
            inq: {
                hiddenSelector: '#job_products_inq',
                tableBodySelector: '#job-product-items-inq',
                errorSelector: '#job-product-error-inq',
            },
            nonInq: {
                hiddenSelector: '#job_products_nonInq',
                tableBodySelector: '#job-product-items-nonInq',
                errorSelector: '#job-product-error-nonInq',
            },
            reg: {
                hiddenSelector: '#job_products_reg',
                tableBodySelector: '#job-product-items-reg',
                errorSelector: '#job-product-error-reg',
            }
        };

        const state = {
            inq: [],
            nonInq: [],
            reg: [],
        };

        const config = {
            supplierOptionsUrl: @json(route('api.supplier.options')),
            productOptionsUrl: @json(route('api.job_product.product_options')),
            productDetailBaseUrl: @json(url('/api/job-product/product-detail')),
        };

        const $modal = $('#jobProductDraftModal');
        const $modalTitle = $('#jobProductDraftLabel');
        const $sectionField = $('#job-product-draft-section');
        const $indexField = $('#job-product-draft-index');
        const $mainCategory = $('#job-product-draft-main-category');
        const $supplier = $('#job-product-draft-supplier');
        const $product = $('#job-product-draft-product');
        const $qty = $('#job-product-draft-qty');
        const $price = $('#job-product-draft-price');
        const $note = $('#job-product-draft-note');
        const $stock = $('#job-product-draft-stock');
        const $stockValidation = $('#job-product-draft-stock-validation');
        const $qtyUnit = $('#job-product-draft-qty-unit');

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function nl2br(value) {
            return escapeHtml(value).replace(/\n/g, '<br>');
        }

        function formatNumber(value, maxFractionDigits = 2) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: maxFractionDigits,
            }).format(Number(value || 0));
        }

        function formatPrice(value) {
            return formatNumber(value, 2);
        }

        function getAutoNumericRaw($element) {
            if (typeof $element.autoNumeric === 'function' && $element.data('jobProductAutoNumericReady')) {
                try {
                    return $element.autoNumeric('get');
                } catch (error) {
                    return $element.val();
                }
            }

            return $element.val();
        }

        function setAutoNumericValue($element, value) {
            if (typeof $element.autoNumeric === 'function' && $element.data('jobProductAutoNumericReady')) {
                try {
                    $element.autoNumeric('set', value);
                    return;
                } catch (error) {
                }
            }

            $element.val(value);
        }

        function clearFieldError(selector) {
            const $field = $(selector);
            const $wrapper = $field.closest('.validate-input');

            if ($wrapper.hasClass('has-error')) {
                $wrapper.removeClass('has-error');
                $wrapper.find('.help-block').remove();
            }
        }

        function clearModalErrors() {
            $modal.find('.validate-input').removeClass('has-error');
            $modal.find('.help-block').remove();
        }

        function showFieldError(selector, message) {
            const $field = $(selector);
            const $wrapper = $field.closest('.validate-input');

            if (!$wrapper.length) {
                return;
            }

            clearFieldError(selector);
            $wrapper.addClass('has-error')
                .append('<span class="help-block"><mdall>' + message + '</mdall></span>');
        }

        function showSectionError(sectionKey, message) {
            const $error = $(sectionConfigs[sectionKey].errorSelector);
            $error.text(message).removeClass('d-none');
        }

        function clearSectionError(sectionKey) {
            const $error = $(sectionConfigs[sectionKey].errorSelector);
            $error.addClass('d-none').text('');
        }

        function syncSectionState(sectionKey) {
            $(sectionConfigs[sectionKey].hiddenSelector).val(JSON.stringify(state[sectionKey]));
        }

        function renderSection(sectionKey) {
            const items = state[sectionKey];
            const $tableBody = $(sectionConfigs[sectionKey].tableBodySelector);

            if (!items.length) {
                $tableBody.html(
                    '<tr class="job-product-empty-row">' +
                        '<td colspan="6" class="text-center text-muted">No product added.</td>' +
                    '</tr>'
                );
                syncSectionState(sectionKey);
                return;
            }

            const rows = items.map(function (item, index) {
                return '' +
                    '<tr>' +
                        '<td>' + escapeHtml(item.sku || '-') + '</td>' +
                        '<td class="text-wrap">' + escapeHtml(item.product_label || '-') + '</td>' +
                        '<td>' + escapeHtml(formatNumber(item.qty, 2)) + ' ' + escapeHtml(item.unit_name || '') + '</td>' +
                        '<td>' + escapeHtml(formatPrice(item.price)) + '</td>' +
                        '<td class="text-wrap">' + nl2br(item.note || '-') + '</td>' +
                        '<td class="text-center">' +
                            '<button type="button" class="btn btn-warning btn-sm mr-1 btn-edit-job-product-draft" data-section-key="' + sectionKey + '" data-index="' + index + '">' +
                                '<i class="mdi mdi-square-edit-outline"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-danger btn-sm btn-delete-job-product-draft" data-section-key="' + sectionKey + '" data-index="' + index + '">' +
                                '<i class="mdi mdi-delete"></i>' +
                            '</button>' +
                        '</td>' +
                    '</tr>';
            }).join('');

            $tableBody.html(rows);
            syncSectionState(sectionKey);
        }

        function initializeModalPlugins() {
            if (typeof $.fn.select2 === 'function') {
                $modal.find('.select2').each(function () {
                    const $element = $(this);

                    if ($element.data('select2')) {
                        $element.select2('destroy');
                    }

                    $element.select2({
                        dropdownParent: $modal,
                        width: '100%',
                    });
                });
            }

            if (typeof $.fn.autoNumeric === 'function') {
                $modal.find('.autoNumeric').each(function () {
                    const $element = $(this);

                    if (!$element.data('jobProductAutoNumericReady')) {
                        $element.autoNumeric();
                        $element.data('jobProductAutoNumericReady', true);
                    }
                });
            }
        }

        function resetProductState() {
            $product
                .html('<option selected disabled>-- Select product --</option>')
                .prop('disabled', true)
                .val(null)
                .trigger('change');

            $stock.text('');
            $stockValidation.val('');
            $qtyUnit.text('-');
        }

        function resetModalFields() {
            clearModalErrors();
            $mainCategory.val(null).trigger('change');
            $supplier.html('<option selected disabled>-- Select supplier --</option>').prop('disabled', true).val(null).trigger('change');
            resetProductState();
            setAutoNumericValue($qty, '');
            setAutoNumericValue($price, '');
            $note.val('');
            $indexField.val('');
        }

        function refreshSupplierOptions(selectedSupplierId = null, callback = null) {
            const mainCategoryId = $mainCategory.val();

            if (!mainCategoryId) {
                $supplier
                    .html('<option selected disabled>-- Select supplier --</option>')
                    .prop('disabled', true)
                    .val(null)
                    .trigger('change');

                if (typeof callback === 'function') {
                    callback();
                }

                return;
            }

            $.ajax({
                url: config.supplierOptionsUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    main_category_id: mainCategoryId,
                    selected: selectedSupplierId,
                },
                success: function (response) {
                    $supplier.html(response.options).prop('disabled', false);
                    if (selectedSupplierId) {
                        $supplier.val(String(selectedSupplierId)).trigger('change.select2');
                    }
                },
                complete: function () {
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            });
        }

        function fetchProductOptions(selectedProductId = '', callback = null) {
            const mainCategoryId = $mainCategory.val();

            if (!mainCategoryId) {
                resetProductState();
                if (typeof callback === 'function') {
                    callback();
                }
                return;
            }

            const productOptionsUrl = selectedProductId
                ? config.productOptionsUrl + '/' + selectedProductId
                : config.productOptionsUrl;

            $.ajax({
                url: productOptionsUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    selected: selectedProductId,
                    main_category_id: mainCategoryId,
                    supplier_id: $supplier.val(),
                },
                success: function (response) {
                    $product.html(response.options).prop('disabled', false);

                    if (selectedProductId) {
                        $product.val(String(selectedProductId)).trigger('change.select2');
                    } else {
                        $product.val(null).trigger('change.select2');
                        $stock.text('');
                        $stockValidation.val('');
                        $qtyUnit.text('-');
                    }
                },
                complete: function () {
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            });
        }

        function updateStockInformation(response) {
            const unitName = response.unit ? response.unit.unit_name : '-';
            const stockValue = Number(response.qty || 0);

            $stock.text(formatNumber(stockValue, 2) + ' ' + unitName);
            $stockValidation.val(stockValue);
            $qtyUnit.text(unitName);
        }

        function fetchStockDetail(productId, callback = null) {
            if (!productId) {
                $stock.text('');
                $stockValidation.val('');
                $qtyUnit.text('-');

                if (typeof callback === 'function') {
                    callback();
                }

                return;
            }

            $.ajax({
                url: config.productDetailBaseUrl + '/' + productId,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    updateStockInformation(response);

                    if (typeof callback === 'function') {
                        callback(response);
                    }
                }
            });
        }

        function getDraftItemFromModal() {
            const $selectedProduct = $product.find('option:selected');

            return {
                product_id: $product.val(),
                main_category_id: $mainCategory.val(),
                supplier_id: $supplier.val(),
                qty: getAutoNumericRaw($qty),
                price: getAutoNumericRaw($price),
                note: $note.val().trim(),
                sku: $selectedProduct.data('sku') || '',
                product_label: $.trim($selectedProduct.text()),
                unit_name: $qtyUnit.text() || '',
                stock_label: $.trim($stock.text()),
            };
        }

        function validateDraftItem(item) {
            let valid = true;

            if (!item.main_category_id) {
                showFieldError('#job-product-draft-main-category', 'Main Category is required.');
                valid = false;
            }

            if (!item.supplier_id) {
                showFieldError('#job-product-draft-supplier', 'Supplier is required.');
                valid = false;
            }

            if (!item.product_id) {
                showFieldError('#job-product-draft-product', 'Product is required.');
                valid = false;
            }

            if (!item.qty || Number(item.qty) <= 0) {
                showFieldError('#job-product-draft-qty', 'Quantity must be greater than 0.');
                valid = false;
            }

            if (item.price === '' || Number(item.price) < 0) {
                showFieldError('#job-product-draft-price', 'Unit Price is required.');
                valid = false;
            }

            return valid;
        }

        function openDraftModal(sectionKey, index = null) {
            initializeModalPlugins();
            clearSectionError(sectionKey);
            resetModalFields();

            $sectionField.val(sectionKey);

            if (index === null) {
                $modalTitle.text('Add Job Product');
                $indexField.val('');
                $modal.modal('show');
                return;
            }

            const item = state[sectionKey][index];

            if (!item) {
                return;
            }

            $modalTitle.text('Edit Job Product');
            $indexField.val(index);
            $mainCategory.val(String(item.main_category_id)).trigger('change.select2');

            refreshSupplierOptions(item.supplier_id, function () {
                fetchProductOptions(item.product_id, function () {
                    fetchStockDetail(item.product_id);
                });
            });

            setAutoNumericValue($qty, item.qty);
            setAutoNumericValue($price, item.price);

            $note.val(item.note || '');
            $qtyUnit.text(item.unit_name || '-');
            $stock.text(item.stock_label || '');

            $modal.modal('show');
        }

        function saveDraftItem() {
            clearModalErrors();

            const sectionKey = $sectionField.val();
            const editIndex = $indexField.val() === '' ? null : Number($indexField.val());
            const item = getDraftItemFromModal();

            if (!validateDraftItem(item)) {
                return;
            }

            const duplicateIndex = state[sectionKey].findIndex(function (draftItem, index) {
                return String(draftItem.product_id) === String(item.product_id)
                    && index !== editIndex;
            });

            if (duplicateIndex !== -1) {
                showFieldError('#job-product-draft-product', 'Product already added to this Job.');
                return;
            }

            if (editIndex === null) {
                state[sectionKey].push(item);
            } else {
                state[sectionKey][editIndex] = item;
            }

            renderSection(sectionKey);
            clearSectionError(sectionKey);
            $modal.modal('hide');
        }

        function removeDraftItem(sectionKey, index) {
            state[sectionKey].splice(index, 1);
            renderSection(sectionKey);
        }

        $(document).on('click', '.btn-open-job-product-modal', function () {
            openDraftModal($(this).data('sectionKey'));
        });

        $(document).on('click', '.btn-edit-job-product-draft', function () {
            openDraftModal($(this).data('sectionKey'), Number($(this).data('index')));
        });

        $(document).on('click', '.btn-delete-job-product-draft', function () {
            if (!window.confirm('Delete this product from the draft list?')) {
                return;
            }

            removeDraftItem($(this).data('sectionKey'), Number($(this).data('index')));
        });

        $mainCategory.on('change', function () {
            resetProductState();
            refreshSupplierOptions(null);
        });

        $supplier.on('change', function () {
            fetchProductOptions();
        });

        $product.on('change', function () {
            fetchStockDetail($(this).val());
        });

        $('#btn-save-job-product-draft').on('click', function (e) {
            e.preventDefault();
            saveDraftItem();
        });

        Object.keys(sectionConfigs).forEach(function (sectionKey) {
            renderSection(sectionKey);
        });

        window.jobProductDraftManager = {
            showSectionError: showSectionError,
            clearSectionError: clearSectionError,
        };
    })();
</script>
