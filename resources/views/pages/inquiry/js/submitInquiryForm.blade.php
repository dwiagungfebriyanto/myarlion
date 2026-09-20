<script>
    $(document).ready(function() {
        $('.btn-submit-inq').click(function() {
            $('#create-form').parsley().validate();
            // console.log($('#create-form').parsley().isValid());
            if ($('#create-form').parsley().isValid()) {
                $.ajax({
                    url: "{{ route('inquiry.store') }}",
                    type: "POST",
                    data: $('#create-form').serialize(),
                    success: function(data) {
                        $.toast({
                            heading: "Success!",
                            text: "Inquiry data successfully added.",
                            position: "top-right",
                            loaderBg: "#5ba035",
                            icon: "success",
                            hideAfter: 3e3,
                            stack: 1
                        })

                        $('.create-inq').removeClass('active');
                        $('#create-inq').removeClass('show active');

                        $('.product').addClass('active');
                        $('#product').addClass('show active');

                        $('.tab-content').find('#product').html(data);
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
        });
    });
</script>
