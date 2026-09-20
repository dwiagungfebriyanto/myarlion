<div class="d-flex my-2 justify-content-center">
    <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btn-edit" type="button" data-toggle="modal"
        data-id="" data-target="#editProductModal"
        data-url="{{ route('job_product.edit_modal', [$job_id, $id]) }}">
        <i class="fas fa-pen-alt"></i></button>

    <form action="{{route('job_product.destroy', [$job_id, $id])}}" method="POST">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light sa-warning">
            <i class="fas fa-trash-alt"></i></button>
    </form>
</div>

<script>
    $(document).ready(function() {

        $('.btn-edit').off('click').on('click', function(e) {
            let data = $(this).data();

            $.ajax({
                method: "get",
                url: data.url,
                success: function(response) {
                    $('#editProductModal').find('.modal-dialog').html(response);
                }
            })
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
                    element.parent().submit()
                }
            })
        })

    });
</script>
