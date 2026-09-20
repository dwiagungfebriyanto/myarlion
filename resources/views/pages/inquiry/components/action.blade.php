<div class="d-flex my-2 justify-content-start">

    @can('edit inquiry_item')
    <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btn-edit-product"
        data-url="{{ route('inquiry.inquiryProduct.edit', $id) }}"><i class="fas fa-pen-alt"></i></button>
    @endcan

    @can('delete inquiry_item')
    <form action="" method="POST">
        @method('DELETE')
        <button type="button" data-url="{{ route('inquiry.inquiryProduct.destroy', $id) }}"
            class="btn btn-danger btn-sm waves-effect waves-light sa-warning"><i class="fas fa-trash-alt"></i></button>
    </form>
    @endcan
</div>

<script>
    $(document).ready(function() {
        $('.btn-edit-product').off('click').on('click', function(e) {
            let data = $(this).data();
            $.ajax({
                method: "get",
                url: data.url,
                success: function(response) {
                    $("#main_category").val(response.main_category_id).trigger('change');
                    $("#id").val(response.id);
                    setTimeout(() => {
                        $("#sub_category").val(response.sub_category_id).trigger(
                            'change');
                        $("#product_type").val(response.product_type_id).trigger(
                            'change');
                        $("#specification").val(response.specification_id).trigger(
                            'change');
                        $("#packaging").val(response.packaging_id).trigger(
                        'change');
                        $("#brand").val(response.brand_id).trigger('change');
                    }, 500);
                }
            })
        });
    });
    $(".sa-warning").off('click').on('click', function() {
        let element = $(this);
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then(function(t) {
            if (t.value === true) {
                $.ajax({
                    method: "delete",
                    url: element.data('url'),
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $.toast({
                            heading: "Success!",
                            text: "Inquiry Product Deleted Successfully",
                            position: "top-right",
                            loaderBg: "#5ba035",
                            icon: "success",
                            hideAfter: 3e3,
                            stack: 1
                        });
                        $('.reloadProductList').click();
                    }
                });
            }
        })
    })
</script>
