<div class="d-flex">
    <button type="button" class="btn btn-info btn-sm waves-effect waves-light mr-2 btn-detail"
        data-id="{{ $id }}" data-toggle="modal" data-target="#viewModal"
        data-url="{{ route('product.list.show', $id) }}"><i class="fas fa-eye"></i></button>

    @can('edit product')
        <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btn-edit-product" type="button" data-toggle="modal"
        data-id="{{ $id }}" data-jenis="edit" data-target="#editModal"
        data-url="{{ route('product.list.edit', $id) }}"><i class="fas fa-pen-alt"></i></button>
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
                    $('#editModal').find('.modal-dialog').html(response);
                }
            })
        });

        $('.btn-detail').off('click').on('click', function(e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                dataType: "json",
                success: function(response) {
                    let note = (response.product.note !== null) ?
                        response.product.note :
                        '-';
                    let harga_rata_rata = (response.product.harga_rata_rata !== null) ?
                        'Rp. ' +
                        response.product.harga_rata_rata.toLocaleString('id-ID') +
                        '.00' : 'Rp. 0.00';
                    let harga_tertinggi = (response.product.harga_tertinggi !== null) ?
                        'Rp. ' + response.product.harga_tertinggi.toLocaleString('id-ID') +
                        '.00' : 'Rp. 0.00';

                    $('#sku').text(response.product.sku);
                    $('#view-main_category').text(response.main_category);
                    $('#view-sub_category').text(response.sub_category);
                    $('#view-product_type').text(response.product_type);
                    $('#view-brand').text(response.brand);
                    $('#view-specification').text(response.specification);
                    $('#view-packaging').text(response.packaging);
                    $('#view-supplier').text(response.supplier);
                    $('#stock').text(response.product.qty);
                    $('#stock_unit').text(response.unit);
                    $('#harga_rata_rata').text(harga_rata_rata);
                    $('#harga_tertinggi').text(harga_tertinggi);
                    $('#note').text(note);

                }
            });
        });



        $(".sa-warning").click(function() {
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
                    element.parent().submit()
                }
            })
        })

    });
</script>
