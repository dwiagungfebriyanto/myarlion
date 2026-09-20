<script>
    function unformatNumeric(param) {
        return param.slice(0, -3).replace(/[^0-9]/g, '')
    }

    // function getRealNumeric(idAmount, idEstProfit) {
    //     let amount = $('#' + idAmount).val();
    //     let est_profit = $('#' + idEstProfit).val();

    //     if (isNaN(amount)) {
    //         $('#' + idAmount).val(parseFloat(unformatNumeric(amount)));
    //     }
    //     if (isNaN(est_profit)) {
    //         $('#' + idEstProfit).val(parseFloat(unformatNumeric(est_profit)));
    //     }
    // }

    $('.btn-edit-submit').on('click', function(e) {
        e.preventDefault();
        // getRealNumeric('qty', 'price');
        $('#edit-form').parsley().validate();
        // console.log($('#edit-form').parsley().isValid());

        if ($('#edit-form').parsley().isValid()) {
            $.ajax({
                type: 'POST',
                url: '{{ route('job.list.statement.stock.update', $jobStatement->id ) }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    job_id: $('#job_id').val(),
                    // inventory_stock_temp: $('#inventory_stock_temp').val(),
                    unit_id: $('#unit_id').val(),
                    inventory_stock_id: $('#inventory_stock_id').val(),
                    stock: $('#stock-validation').val(),
                    quantity: parseFloat(unformatNumeric($('#quantity').val())),
                    price: parseFloat(unformatNumeric($('#price').val())),
                    total: parseFloat(unformatNumeric($('#total').val())),
                },
                success: function(data) {
                    // Handle success response
                    $('#edit-form').trigger('reset');
                    $('#edit-form').parsley().reset();
                    $('#edit-form').parsley().destroy();
                    // $('#editStatementStock').modal('hide');
                    $('.modal-header button.close').click();
                    //reload datatable
                    $('.reload').click();
                    $.toast({
                        heading: "Success!",
                        text: data.message,
                        position: "top-right",
                        loaderBg: "#5ba035",
                        icon: "success",
                        hideAfter: 3e3,
                        stack: 1
                    })
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    let response = xhr.responseJSON;
                    console.log(response);
                    if ($.isEmptyObject(response) == false) {

                        $.each(response.errors, function(key, value) {
                            // console.log(key);
                            // if (key == 'inventory_stock_id') {
                            //     key = 'job_has_product';
                            // }
                            if ($('#' + key).closest('.validate-input').hasClass(
                                    'has-error')) {
                                $('#' + key).closest('.validate-input')
                                    .removeClass(
                                        'has-error')
                                $('#' + key).closest('.validate-input').find(
                                        '.help-block')
                                    .remove();
                            }
                            $('#' + key).closest('.validate-input').addClass(
                                    'has-error')
                                .append('<span class="help-block"><mdall>' + value +
                                    '</mdall></span>');
                        });
                    }
                }
            });
        }
    });
</script>
