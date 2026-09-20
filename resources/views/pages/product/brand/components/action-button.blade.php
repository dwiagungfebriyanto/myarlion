<div class="d-flex my-2 justify-content-center">

    @can('edit brand')
    <button class="btn btn-warning btn-sm waves-effect waves-light btn-edit" type="button" data-toggle="modal"
        data-id="{{ $id }}" data-jenis="edit" data-target="#editModal"
        data-url="{{ route('product.brand.edit', $id) }}"><i class="fas fa-pen-alt"></i></button>
    @endcan

</div>

<script>
    $(document).ready(function() {

        $('.btn-edit').off('click').on('click', function(e) {
            let data = $(this).data();
            $.ajax({
                method: "get",
                url: data.url,
                success: function(response) {
                    $('#editModal').find('.modal-dialog').html(response);
                }
            })
        });

    });
</script>
