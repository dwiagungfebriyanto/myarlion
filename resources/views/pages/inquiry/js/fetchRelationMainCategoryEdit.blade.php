<script>
    function fetchMainCategory(data) {
        var main_category_id = data;
        console.log(main_category_id);
        $.ajax({
            url: "{{ route('product.fetchMainCategory') }}",
            type: "POST",
            data: {
                main_category: main_category_id,
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(response) {
                // console.log(response);
                // sub category
                $('#sub_category').removeAttr('disabled');
                $(".sub_category").html('<option value="">-- Select Sub Category --</option>');
                $.each(response.sub_category, function(key, value) {
                    $(".sub_category").append('<option class="' + value.id + '" value="' + value
                        .id + '">' + value.code +
                        ' - ' + value.category_name + '</option>');
                    if (value.id == "{{ $product->sub_category_id }}") {
                        $("." + value.id).attr('selected', 'selected');
                    }
                });
                // product type
                $('#product_type').removeAttr('disabled');
                $(".product_type").html('<option value="">-- Select Product Type --</option>');
                $.each(response.product_type, function(key, value) {
                    $(".product_type").append('<option class="' + value.id + '" value="' + value
                        .id + '">' + value.code +
                        ' - ' + value.product_type_name + '</option>');
                    if (value.id == "{{ $product->product_type_id }}") {
                        $("." + value.id).attr('selected', 'selected');
                    }
                });
                // brand
                $('#brand').removeAttr('disabled');
                $(".brand").html('<option value="">-- Select Brand --</option>');
                $.each(response.brand, function(key, value) {
                    $(".brand").append('<option class="' + value.id + '" value="' + value.id +
                        '">' + value.code + ' - ' +
                        value.brand_name + '</option>');
                    if (value.id == "{{ $product->brand_id }}") {
                        $("." + value.id).attr('selected', 'selected');
                    }
                });
                // specification
                $('#specification').removeAttr('disabled');
                $(".specification").html('<option value="">-- Select Specification --</option>');
                $.each(response.specification, function(key, value) {
                    $(".specification").append('<option class="' + value.id + '" value="' + value
                        .id + '">' + value.code +
                        ' - ' + value.specification_name + '</option>');
                    if (value.id == "{{ $product->specification_id }}") {
                        $("." + value.id).attr('selected', 'selected');
                    }
                });
                // packaging
                $('#packaging').removeAttr('disabled');
                $(".packaging").html('<option value="">-- Select Packaging/Size --</option>');
                $.each(response.packaging, function(key, value) {
                    $(".packaging").append('<option class="' + value.id + '" value="' + value.id +
                        '">' + value.code +
                        ' - ' + value.packaging_name + '</option>');
                    if (value.id == "{{ $product->packaging_id }}") {
                        $("." + value.id).attr('selected', 'selected');
                    }
                });
                // supplier
                $('#supplier').removeAttr('disabled');
                $(".supplier").html('<option value="">-- Select Supplier --</option>');
                $.each(response.supplier, function(key, value) {
                    $(".supplier").append('<option class="' + value.id + '" value="' + value.id +
                        '">' + value.code + ' - ' +
                        value.supplier_name + '</option>');
                    if (value.id == "{{ $product->supplier_id }}") {
                        $("." + value.id).attr('selected', 'selected');
                    }
                });

            }
        })
    }

    $(document).ready(function() {
        $('#main_category').val() != '' ? fetchMainCategory($('#main_category').val()) : null;

        $('#main_category').on({
            change: fetchMainCategory($('#main_category').val()),
        });
    })
</script>
