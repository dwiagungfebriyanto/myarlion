<script>
    $(document).ready(function() {
        $('#main_category').on('change', function() {
            var main_category_id = $(this).val();
            $.ajax({
                url: "{{ route('product.fetchMainCategory') }}",
                type: "POST",
                data: {
                    main_category: main_category_id,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    // sub category
                    $('#sub_category').removeAttr('disabled');
                    $(".sub_category").html('<option value="">-- Select Sub Category --</option>');
                    $.each(response.sub_category, function (key, value) {
                        $(".sub_category").append('<option value="' + value.id + '">' + value.code + ' - ' + value.category_name + '</option>');
                    });
                    // product type
                    $('#product_type').removeAttr('disabled');
                    $(".product_type").html('<option value="">-- Select Product Type --</option>');
                    $.each(response.product_type, function (key, value) {
                        $(".product_type").append('<option value="' + value.id + '">' + value.code + ' - ' + value.product_type_name + '</option>');
                    });
                    // brand
                    $('#brand').removeAttr('disabled');
                    $(".brand").html('<option value="">-- Select Brand --</option>');
                    $.each(response.brand, function (key, value) {
                        $(".brand").append('<option value="' + value.id + '">' + value.code + ' - ' + value.brand_name + '</option>');
                    });
                    // specification
                    $('#specification').removeAttr('disabled');
                    $(".specification").html('<option value="">-- Select Specification --</option>');
                    $.each(response.specification, function (key, value) {
                        $(".specification").append('<option value="' + value.id + '">' + value.code + ' - ' + value.specification_name + '</option>');
                    });
                    // packaging
                    $('#packaging').removeAttr('disabled');
                    $(".packaging").html('<option value="">-- Select Packaging/Size --</option>');
                    $.each(response.packaging, function (key, value) {
                        $(".packaging").append('<option value="' + value.id + '">' + value.code + ' - ' + value.packaging_name + '</option>');
                    });
                    // supplier
                    $('#supplier').removeAttr('disabled');
                    $(".supplier").html('<option value="">-- Select Supplier --</option>');
                    $.each(response.supplier, function (key, value) {
                        $(".supplier").append('<option value="' + value.id + '">' + value.code + ' - ' + value.supplier_name + '</option>');
                    });

                }
            })
        })
    })
</script>
