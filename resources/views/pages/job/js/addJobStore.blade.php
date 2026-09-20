<script>
    (function () {
        const jobProductBaseUrl = @json(url('/job-product'));
        const createJobConfigs = [
            {
                formSelector: '#form-inquiry-job',
                submitButtonSelector: '.btn-jobInquiry-submit',
                jobProductsSelector: '#job_products_inq',
                sectionKey: 'inq',
                payloadSelectors: {
                    source: '#source-inq',
                    inquiry: '#inquiry',
                    job_id: '#job_id-inq',
                    currency: '#currency-inq',
                    amount: '#amount-inq',
                    est_profit: '#est_profit-inq',
                },
            },
            {
                formSelector: '#form-non-inquiry-job',
                submitButtonSelector: '.btn-jobNonInquiry-submit',
                jobProductsSelector: '#job_products_nonInq',
                sectionKey: 'nonInq',
                payloadSelectors: {
                    source: '#source-nonInq',
                    job_id: '#job_id-nonInq',
                    customer: '#customer-nonInq',
                    channel: '#channel-nonInq',
                    country: '#country-nonInq',
                    currency: '#currency-nonInq',
                    amount: '#amount-nonInq',
                    est_profit: '#est_profit-nonInq',
                },
            },
            {
                formSelector: '#form-reguler-job',
                submitButtonSelector: '.btn-jobReguler-submit',
                jobProductsSelector: '#job_products_reg',
                sectionKey: 'reg',
                payloadSelectors: {
                    source: '#source-reg',
                    job_id: '#job_id-reg',
                    customer: '#customer-reg',
                    channel: '#channel-reg',
                    country: '#country-reg',
                    currency: '#currency-reg',
                    amount: '#amount-reg',
                    est_profit: '#est_profit-reg',
                },
            }
        ];

        function clearFormErrors($form) {
            $form.find('.validate-input').removeClass('has-error');
            $form.find('.help-block').remove();
        }

        function showFieldError(selector, message) {
            const $field = $(selector);
            const $wrapper = $field.closest('.validate-input');

            if (!$wrapper.length) {
                return;
            }

            if ($wrapper.hasClass('has-error')) {
                $wrapper.removeClass('has-error');
                $wrapper.find('.help-block').remove();
            }

            $wrapper.addClass('has-error')
                .append('<span class="help-block"><mdall>' + message + '</mdall></span>');
        }

        function getJobProducts(config) {
            const rawValue = $(config.jobProductsSelector).val();

            if (!rawValue) {
                return [];
            }

            try {
                return JSON.parse(rawValue);
            } catch (error) {
                return [];
            }
        }

        function submitCreateJob(config) {
            const $form = $(config.formSelector);

            clearFormErrors($form);
            if (window.jobProductDraftManager) {
                window.jobProductDraftManager.clearSectionError(config.sectionKey);
            }

            setAutoNumericRawValue();
            $form.parsley().validate();

            if (!$form.parsley().isValid()) {
                return;
            }

            const jobProducts = getJobProducts(config);

            if (!jobProducts.length) {
                if (window.jobProductDraftManager) {
                    window.jobProductDraftManager.showSectionError(
                        config.sectionKey,
                        'At least one Job Product is required.'
                    );
                }
                return;
            }

            const payload = {
                _token: '{{ csrf_token() }}',
                _method: 'POST',
                job_products: jobProducts,
            };

            $.each(config.payloadSelectors, function (key, selector) {
                payload[key] = $(selector).val();
            });

            $.ajax({
                type: 'POST',
                url: '{{ route('job.list.store') }}',
                data: payload,
                success: function (response) {
                    const numericJobId = Number(response.job_id || 0);

                    if (response.redirect_url && numericJobId > 0 && !String(response.redirect_url).endsWith('/0')) {
                        window.location.href = response.redirect_url;
                        return;
                    }

                    if (numericJobId > 0) {
                        window.location.href = jobProductBaseUrl + '/' + numericJobId;
                        return;
                    }

                    toastDanger('Failed to resolve redirect Job ID from create response.');
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;

                    if (!response || $.isEmptyObject(response.errors)) {
                        toastDanger(response && response.message ? response.message : null);
                        return;
                    }

                    $.each(response.errors, function (key, value) {
                        const message = $.isArray(value) ? value[0] : value;

                        if (key.indexOf('job_products') === 0) {
                            if (window.jobProductDraftManager) {
                                window.jobProductDraftManager.showSectionError(config.sectionKey, message);
                            }
                            return;
                        }

                        if (config.payloadSelectors[key]) {
                            showFieldError(config.payloadSelectors[key], message);
                        }
                    });
                }
            });
        }

        createJobConfigs.forEach(function (config) {
            $(document).on('click', config.submitButtonSelector, function (e) {
                e.preventDefault();
                submitCreateJob(config);
            });
        });
    })();
</script>
