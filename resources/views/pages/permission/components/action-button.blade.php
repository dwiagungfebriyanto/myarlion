<div class="button-list">

    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit" data-toggle="modal"
        data-target="#editModal" data-url="{{ route('permission.edit', $id) }}">
        <i class="fas fa-pen-alt"></i></button>

    <form action="{{ route('permission.destroy', $id) }}" method="post" style="display:inline">
        @csrf
        @method('delete')

        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger btn-delete">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('.btn-edit').click(function (e) {
            let url = $(this).data('url');

            $.ajax({
                type: "get",
                url: url,
                success: function (response) {
                    let indexUrl = $('#form-edit').data('url');

                    $('#form-edit').attr('action', indexUrl + '/' + response.id)
                    $('#edit-permission').val(response.name)
                }
            });
        });

        $(".btn-delete").click(function () {
            let element = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(function (t) {
                if (t.value === true) {
                    $.ajax({
                        type: 'POST',
                        url: element.parent().attr('action'),
                        data: element.parent().serialize(),
                        success: function (data) {
                            // Handle success response
                            $('#permission-datatable').DataTable().ajax.reload();

                            $.toast({
                                heading: "Success!",
                                text: 'Permission data successfully deleted.',
                                position: "top-right",
                                loaderBg: "#5ba035",
                                icon: "success",
                                hideAfter: 3e3,
                                stack: 1
                            })
                        },
                        error: function (xhr, status, error) {
                            // Handle error response
                            $.toast({
                                heading: "Something went wrong!",
                                text: 'Permission data failed to delete.',
                                position: "top-right",
                                loaderBg: "#bf441d",
                                icon: "error",
                                hideAfter: 3e3,
                                stack: 1
                            })
                        }
                    });
                }
            })
        })

    });

</script>
