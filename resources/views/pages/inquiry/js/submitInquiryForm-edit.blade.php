<script>
    $(document).ready(function() {
        $('.btn-submit-edit-inq').click(function() {
            if ($('#status').val() === 'sales') {
                $('.edit-inq').removeClass('active');
                $('#edit-inq').removeClass('show active');

                $('.edit-product').addClass('active');
                $('#edit-product').addClass('show active');
                $('.reloadProdList').click();
            } else {
                $('#edit-form').parsley().validate();
                if ($('#edit-form').parsley().isValid()) {

                    $.ajax({
                        url: "{{ route('inquiry.update', $inquiry->id) }}",
                        type: "PUT",
                        data: $('#edit-form').serialize(),
                        success: function(data) {
                            $.toast({
                                heading: "Success!",
                                text: "Inquiry data successfully updated.",
                                position: "top-right",
                                loaderBg: "#5ba035",
                                icon: "success",
                                hideAfter: 3e3,
                                stack: 1
                            })

                            $('.edit-inq').removeClass('active');
                            $('#edit-inq').removeClass('show active');

                            $('.edit-product').addClass('active');
                            $('#edit-product').addClass('show active');

                            $('.tab-content').find('#edit-product').html(data);
                            $('.reloadProductList').click();
                        },
                        error: function(data) {
                            $.toast({
                                heading: "Failed!",
                                text: "Change a few things up and try submitting again.",
                                position: "top-right",
                                loaderBg: "#bf441d",
                                icon: "error",
                                hideAfter: 3e3,
                                stack: 1
                            })
                        }
                    });
                }
            }

        });
    })
</script>
